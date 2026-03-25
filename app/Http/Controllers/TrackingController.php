<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wedding;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function show(string $token)
    {
        $wedding = Wedding::where('tracking_token', $token)->firstOrFail();

        abort_if($wedding->isTrial(), 403, 'Fitur tracking tidak tersedia untuk paket trial.');

        // Hitung statistik dari semua guest (tanpa pagination)
        $allGuests = $wedding->guests()->get();
        
        $stats = [
            'total' => $allGuests->count(),
            'opened' => $allGuests->whereNotNull('first_opened_at')->count(),
            'not_opened' => $allGuests->whereNull('first_opened_at')->count(),
            'rsvp_hadir' => $allGuests->where('is_attending', true)->whereNotNull('replied_at')->count(),
            'rsvp_tidak' => $allGuests->where('is_attending', false)->whereNotNull('replied_at')->count(),
            'rsvp_belum' => $allGuests->whereNull('replied_at')->count(),
        ];

        // Gunakan allGuests yang sudah di-load (tanpa duplikat query)
        // Tidak pakai pagination agar Alpine.js tab filter bekerja benar
        $guests = $allGuests->sortBy('guest_name')->values();

        // Untuk paket VIP: sertakan link portal pelanggan
        $customerVipToken = null;
        if ($wedding->isVip()) {
            $order = Order::where('wedding_id', $wedding->id)->latest()->first();
            $customerVipToken = $order?->public_token;
        }

        return view('tracking.show', compact('wedding', 'guests', 'stats', 'customerVipToken'));
    }
}
