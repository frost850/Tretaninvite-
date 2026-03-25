<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wedding;
use App\Services\AuditLogger;
use App\Services\TemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RecycleBinController extends Controller
{
    /** Tampilkan semua item yang sudah di-trash. */
    public function index(): View
    {
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin yang dapat mengakses Recycle Bin.');

        $weddings = Wedding::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get();

        $orders = Order::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get();

        $totalCount = $weddings->count() + $orders->count();
        $allTemplates = TemplateRegistry::all();

        return view('admin.recycle.index', compact('weddings', 'orders', 'totalCount', 'allTemplates'));
    }

    /** Pulihkan undangan dari trash. */
    public function restoreWedding(int $id): RedirectResponse
    {
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin.');

        $wedding = Wedding::onlyTrashed()->findOrFail($id);
        $wedding->restore();

        AuditLogger::log('wedding_restored', 'wedding', $wedding->id, ['slug' => $wedding->slug]);

        return back()->with('success', "Undangan \"{$wedding->bride_name}\" berhasil dipulihkan.");
    }

    /** Pulihkan order dari trash. */
    public function restoreOrder(int $id): RedirectResponse
    {
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin.');

        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();

        AuditLogger::log('order_restored', 'order', $order->id, ['customer' => $order->customer_name]);

        return back()->with('success', "Order \"{$order->customer_name}\" berhasil dipulihkan.");
    }

    /** Hapus permanen satu undangan (+ file). */
    public function forceDeleteWedding(int $id): RedirectResponse
    {
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin.');

        $wedding = Wedding::onlyTrashed()->findOrFail($id);
        $wedding->load('gallery');

        // Hapus foto galeri
        foreach ($wedding->gallery as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->forceDelete();
        }

        // Hapus foto profil
        foreach ([
            'bride_photo', 'groom_photo', 'couple_photo',
            'cover_photo', 'bg_mempelai_photo', 'bg_acara_photo', 'bg_lokasi_photo',
        ] as $field) {
            if ($wedding->$field) {
                Storage::disk('public')->delete($wedding->$field);
            }
        }

        $wedding->guests()->forceDelete();
        $wedding->forceDelete();

        AuditLogger::log('wedding_force_deleted', 'wedding', null, ['slug' => $wedding->slug]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "Undangan \"{$wedding->bride_name}\" dihapus permanen."]);
        }
        return back()->with('success', "Undangan \"{$wedding->bride_name}\" dihapus permanen.");
    }

    /** Hapus permanen satu order (+ bukti bayar). */
    public function forceDeleteOrder(int $id): RedirectResponse
    {
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin.');

        $order = Order::onlyTrashed()->findOrFail($id);

        if ($order->payment_proof && Storage::disk('local')->exists($order->payment_proof)) {
            Storage::disk('local')->delete($order->payment_proof);
        }

        $orderId = strtoupper(substr($order->public_token, 0, 8));
        $order->forceDelete();

        AuditLogger::log('order_force_deleted', 'order', null, [
            'token'    => $orderId,
            'customer' => $order->customer_name,
        ]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "Order #{$orderId} dihapus permanen."]);
        }
        return back()->with('success', "Order #{$orderId} dihapus permanen.");
    }

    /** Kosongkan seluruh recycle bin (purge semua). */
    public function purgeAll(): RedirectResponse
    {
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin.');

        $weddings = Wedding::onlyTrashed()->get();
        foreach ($weddings as $wedding) {
            $wedding->load('gallery');
            foreach ($wedding->gallery as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->forceDelete();
            }
            foreach ([
                'bride_photo', 'groom_photo', 'couple_photo',
                'cover_photo', 'bg_mempelai_photo', 'bg_acara_photo', 'bg_lokasi_photo',
            ] as $field) {
                if ($wedding->$field) Storage::disk('public')->delete($wedding->$field);
            }
            $wedding->guests()->forceDelete();
            $wedding->forceDelete();
        }

        $orders = Order::onlyTrashed()->get();
        foreach ($orders as $order) {
            if ($order->payment_proof && Storage::disk('local')->exists($order->payment_proof)) {
                Storage::disk('local')->delete($order->payment_proof);
            }
            $order->forceDelete();
        }

        $total = $weddings->count() + $orders->count();
        AuditLogger::log('recycle_purged', 'system', null, ['total' => $total]);

        return back()->with('success', "Recycle Bin dikosongkan. {$total} item dihapus permanen.");
    }
}
