<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wedding;
use App\Services\AuditLogger;
use App\Services\TemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

/**
 * Mengelola undangan pernikahan (wedding).
 *
 * Controller ini hanya menangani kategori 'wedding'.
 * Kategori lain ditangani oleh controller masing-masing:
 *   - BirthdayController   → ulang tahun
 *   - GreetingController   → kartu ucapan
 *
 * Daftar template dipusatkan di App\Services\TemplateRegistry.
 * Preview template ditangani oleh PreviewController.
 */
class WeddingController extends Controller
{
    /* ======================================================================
       Halaman admin — daftar, pilih template, create, edit, hapus
    ====================================================================== */

    /** Daftar semua undangan + statistik order. */
    public function index(): View
    {
        $weddings = Wedding::orderByDesc('created_at')->get();

        $orderStats = [
            'total'               => Order::count(),
            'baru'                => Order::where('status', 'baru')->count(),
            'menunggu_pembayaran' => Order::where('payment_status', 'menunggu_konfirmasi')->count(),
            'belum_bayar'         => Order::where('payment_status', 'belum_bayar')->count(),
            'lunas'               => Order::where('payment_status', 'lunas')->count(),
            'diproses'            => Order::where('status', 'diproses')->count(),
        ];

        $urgentOrders = Order::where(function ($q) {
            $q->where('status', 'baru')
              ->orWhere('payment_status', 'menunggu_konfirmasi');
        })->orderByDesc('created_at')->limit(5)->get();

        $readyOrders = Order::where('payment_status', 'lunas')
            ->whereNull('wedding_id')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('admin.weddings.index', compact('weddings', 'orderStats', 'urgentOrders', 'readyOrders'));
    }

    /**
     * Galeri pilih template — menampilkan semua kategori agar admin
     * bisa memilih template dari satu halaman terpusat.
     */
    public function selectTemplate(): View
    {
        $templates = TemplateRegistry::all();

        return view('admin.weddings.select-template', compact('templates'));
    }

    /**
     * Form isi data undangan pernikahan setelah template dipilih.
     *
     * Jika template yang dipilih bukan kategori 'wedding', redirect ke
     * controller yang sesuai agar tidak ada dead-end bagi pengguna yang
     * tiba di sini lewat URL manual atau link lama.
     */
    public function createForm(string $template): View|RedirectResponse
    {
        $templates = TemplateRegistry::all();

        if (!isset($templates[$template])) {
            return redirect()->route('admin.weddings.create')
                ->with('error', 'Template tidak ditemukan.');
        }

        $category = $templates[$template]['category'] ?? 'wedding';

        if ($category === 'birthday') {
            $orderId = request('order_id');
            $url = route('admin.birthdays.create.form', $template) . ($orderId ? '?order_id=' . $orderId : '');
            return redirect($url);
        }

        if ($category === 'greeting') {
            $orderId = request('order_id');
            $url = route('admin.greetings.create.form', $template) . ($orderId ? '?order_id=' . $orderId : '');
            return redirect($url);
        }

        if ($category === 'anniversary') {
            $orderId = request('order_id');
            $url = route('admin.anniversaries.create.form', $template) . ($orderId ? '?order_id=' . $orderId : '');
            return redirect($url);
        }

        $orderId       = request('order_id');
        $order         = $orderId ? Order::find($orderId) : null;
        $isVipTemplate = $templates[$template]['vip_only'] ?? false;
        $package       = $order->package ?? ($isVipTemplate ? 'vip' : 'basic');

        return view('admin.weddings.create', [
            'template'     => $template,
            'templateInfo' => $templates[$template],
            'templates'    => $templates,
            'orderId'      => $orderId,
            'package'      => $package,
        ]);
    }

    /** Simpan undangan pernikahan baru. */
    public function store(Request $request): RedirectResponse
    {
        $templates    = TemplateRegistry::all();
        $templateInfo = $templates[$request->template] ?? $templates['classic'];
        $category     = $templateInfo['category'] ?? 'wedding';

        $valid = $request->validate([
            'slug'               => 'required|string|max:100|unique:weddings,slug|regex:/^[a-z0-9\-]+$/',
            'bride_name'         => 'required|string|max:100',
            'bride_fullname'     => 'nullable|string|max:150',
            'bride_father'       => 'nullable|string|max:100',
            'bride_mother'       => 'nullable|string|max:100',
            'bride_age'          => 'nullable|integer|min:1|max:150',
            'bride_parent'       => 'nullable|string|max:255',
            'bride_wa'           => 'nullable|string|max:20',
            'bride_bank'         => 'nullable|string|max:100',
            'bride_norek'        => 'nullable|string|max:100',
            'groom_name'         => ($category === 'birthday' ? 'nullable' : 'required') . '|string|max:100',
            'groom_fullname'     => 'nullable|string|max:150',
            'groom_father'       => 'nullable|string|max:100',
            'groom_mother'       => 'nullable|string|max:100',
            'groom_parent'       => 'nullable|string|max:255',
            'groom_wa'           => 'nullable|string|max:20',
            'groom_bank'         => 'nullable|string|max:100',
            'groom_norek'        => 'nullable|string|max:100',
            'event_date'         => 'nullable|date',
            'akad_date'          => 'nullable|date',
            'akad_time'          => 'nullable|string|max:50',
            'akad_location'      => 'nullable|string|max:255',
            'reception_date'     => 'nullable|date',
            'reception_time'     => 'nullable|string|max:50',
            'reception_location' => 'nullable|string|max:255',
            'location'           => 'nullable|string|max:255',
            'map_link'           => 'nullable|url|max:500',
            'map_embed'          => 'nullable|string|max:2000',
            'dresscode'          => 'nullable|string|max:100',
            'opening_text'             => 'nullable|string|max:1000',
            'music_url'                => 'nullable|url|max:500',
            'template'                 => 'required|string|in:' . implode(',', array_keys($templates)),
            'has_gallery'              => 'nullable|boolean',
            'custom_texts'                => 'nullable|array',
            'custom_texts.quote_text'      => 'nullable|string|max:1000',
            'custom_texts.quote_source'    => 'nullable|string|max:200',
            'custom_texts.event_name'      => 'nullable|string|max:200',
            'custom_texts.ceremony_title'  => 'nullable|string|max:200',
            'custom_texts.reception_title' => 'nullable|string|max:200',
        ], [
            'slug.regex' => 'Slug hanya huruf kecil, angka, dan strip (contoh: andi-siti).',
        ]);

        $valid['is_active']     = true;
        $valid['has_gallery']   = $request->has('has_gallery');
        $valid['map_embed']     = self::sanitizeMapEmbed($valid['map_embed'] ?? null);
        $valid['custom_texts']  = array_filter($request->input('custom_texts', []), fn($v) => trim($v ?? '') !== '') ?: null;

        $pendingOrderId = $request->input('order_id');
        $pendingOrder   = $pendingOrderId ? Order::find($pendingOrderId) : null;
        $pendingPackage = $pendingOrder->package
            ?? (($templateInfo['vip_only'] ?? false) ? 'vip' : 'basic');

        if ($pendingPackage === 'vip') {
            $vipValid = $request->validate([
                'video_url'               => 'nullable|url|max:500',
                'notify_email'            => 'nullable|email|max:150',
                'vip_password'            => 'nullable|string|max:100',
                'guestbook_enabled'       => 'nullable|boolean',
                'extra_events'            => 'nullable|array|max:5',
                'extra_events.*.label'    => 'nullable|string|max:80',
                'extra_events.*.date'     => 'nullable|date',
                'extra_events.*.time'     => 'nullable|string|max:20',
                'extra_events.*.location' => 'nullable|string|max:255',
            ]);

            if (!empty($vipValid['extra_events'])) {
                $vipValid['extra_events'] = array_values(
                    array_filter($vipValid['extra_events'], fn($e) => !empty($e['label']))
                );
            }

            $vipValid['guestbook_enabled'] = $request->has('guestbook_enabled');

            if (!empty($vipValid['vip_password'])) {
                $vipValid['vip_password'] = bcrypt($vipValid['vip_password']);
            }

            $valid = array_merge($valid, $vipValid);
        }

        if ($pendingOrder) {
            $valid['package']          = $pendingOrder->package ?? 'basic';
            $valid['trial_expires_at'] = now()->addDays(Wedding::expiryDays($valid['package']));
        }

        if (!isset($valid['trial_expires_at'])) {
            $valid['package']          = $valid['package'] ?? 'basic';
            $valid['trial_expires_at'] = now()->addDays(Wedding::expiryDays($valid['package']));
        }

        $wedding = Wedding::create($valid);

        if ($pendingOrderId && $pendingOrder) {
            $pendingOrder->update(['wedding_id' => $wedding->id]);
        }

        return redirect()->route('admin.guests.import', ['wedding_id' => $wedding->id])
            ->with('success', 'Undangan berhasil dibuat! Sekarang import daftar tamu Anda.');
    }

    /** Form edit undangan pernikahan. */
    public function edit(Wedding $wedding): View|RedirectResponse
    {
        $templates    = TemplateRegistry::all();
        $template     = $wedding->template ?? 'classic';
        $templateInfo = $templates[$template] ?? $templates['classic'];
        $category     = $templateInfo['category'] ?? 'wedding';

        if ($category === 'birthday') {
            return redirect()->route('admin.birthdays.edit', $wedding->id);
        }
        if ($category === 'greeting') {
            return redirect()->route('admin.greetings.edit', $wedding->id);
        }

        $wedding->load('gallery');

        return view('admin.weddings.edit', [
            'w'            => $wedding,
            'template'     => $template,
            'templateInfo' => $templateInfo,
            'templates'    => $templates,
        ]);
    }

    /** Perbarui data undangan pernikahan. */
    public function update(Request $request, Wedding $wedding): RedirectResponse
    {
        $templates    = TemplateRegistry::all();
        $templateInfo = $templates[$wedding->template] ?? $templates['classic'];
        $category     = $templateInfo['category'] ?? 'wedding';

        $valid = $request->validate([
            'bride_name'         => 'required|string|max:100',
            'bride_fullname'     => 'nullable|string|max:150',
            'bride_father'       => 'nullable|string|max:100',
            'bride_mother'       => 'nullable|string|max:100',
            'bride_age'          => 'nullable|integer|min:1|max:150',
            'bride_parent'       => 'nullable|string|max:255',
            'bride_wa'           => 'nullable|string|max:20',
            'bride_bank'         => 'nullable|string|max:100',
            'bride_norek'        => 'nullable|string|max:100',
            'groom_name'         => ($category === 'birthday' ? 'nullable' : 'required') . '|string|max:100',
            'groom_fullname'     => 'nullable|string|max:150',
            'groom_father'       => 'nullable|string|max:100',
            'groom_mother'       => 'nullable|string|max:100',
            'groom_parent'       => 'nullable|string|max:255',
            'groom_wa'           => 'nullable|string|max:20',
            'groom_bank'         => 'nullable|string|max:100',
            'groom_norek'        => 'nullable|string|max:100',
            'event_date'         => 'nullable|date',
            'akad_date'          => 'nullable|date',
            'akad_time'          => 'nullable|string|max:50',
            'akad_location'      => 'nullable|string|max:255',
            'reception_date'     => 'nullable|date',
            'reception_time'     => 'nullable|string|max:50',
            'reception_location' => 'nullable|string|max:255',
            'location'           => 'nullable|string|max:255',
            'map_link'           => 'nullable|url|max:500',
            'map_embed'          => 'nullable|string|max:2000',
            'dresscode'          => 'nullable|string|max:100',
            'opening_text'             => 'nullable|string|max:1000',
            'music_url'                => 'nullable|url|max:500',
            'has_gallery'              => 'nullable|boolean',
            'custom_texts'                => 'nullable|array',
            'custom_texts.quote_text'      => 'nullable|string|max:1000',
            'custom_texts.quote_source'    => 'nullable|string|max:200',
            'custom_texts.event_name'      => 'nullable|string|max:200',
            'custom_texts.ceremony_title'  => 'nullable|string|max:200',
            'custom_texts.reception_title' => 'nullable|string|max:200',
        ]);

        $valid['has_gallery']  = $request->has('has_gallery');
        $valid['map_embed']    = self::sanitizeMapEmbed($valid['map_embed'] ?? null);
        $valid['custom_texts'] = array_filter($request->input('custom_texts', []), fn($v) => trim($v ?? '') !== '') ?: null;

        if ($wedding->isVip()) {
            $vipValid = $request->validate([
                'video_url'               => 'nullable|url|max:500',
                'notify_email'            => 'nullable|email|max:150',
                'vip_password'            => 'nullable|string|max:100',
                'guestbook_enabled'       => 'nullable|boolean',
                'extra_events'            => 'nullable|array|max:5',
                'extra_events.*.label'    => 'nullable|string|max:80',
                'extra_events.*.date'     => 'nullable|date',
                'extra_events.*.time'     => 'nullable|string|max:20',
                'extra_events.*.location' => 'nullable|string|max:255',
            ]);

            if (!empty($vipValid['extra_events'])) {
                $vipValid['extra_events'] = array_values(
                    array_filter($vipValid['extra_events'], fn($e) => !empty($e['label']))
                );
            }

            $vipValid['guestbook_enabled'] = $request->has('guestbook_enabled');

            if (!empty($vipValid['vip_password'])) {
                $vipValid['vip_password'] = bcrypt($vipValid['vip_password']);
            } else {
                unset($vipValid['vip_password']);
            }

            $valid = array_merge($valid, $vipValid);
        }

        $wedding->update($valid);

        return redirect()->route('admin.weddings.edit', $wedding->id)
            ->with('success', 'Undangan berhasil diperbarui.');
    }

    /** Hapus undangan beserta semua file terkait. */
    public function destroy(Wedding $wedding): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        // P7-3: hanya super admin yang boleh hapus undangan
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin yang dapat menghapus undangan.');

        // Soft delete — file & tamu tetap ada sampai dipurge dari Recycle Bin
        $wedding->delete();

        AuditLogger::log('wedding_trashed', 'wedding', $wedding->id, ['slug' => $wedding->slug]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "{$wedding->bride_name} dipindahkan ke Recycle Bin."]);
        }

        return redirect()->route('admin.weddings.index')
            ->with('success', 'Undangan dipindahkan ke Recycle Bin. Bisa dipulihkan dalam 30 hari.');
    }

    /* ======================================================================
       Manajemen masa aktif & status
    ====================================================================== */

    /** Perpanjang masa aktif undangan. */
    public function extendExpiry(Request $request, Wedding $wedding): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $request->validate(['days' => ['required', 'integer', 'min:1', 'max:365']]);
        $days = (int) $request->days;

        $base      = ($wedding->trial_expires_at && $wedding->trial_expires_at->isFuture())
            ? $wedding->trial_expires_at
            : now();
        $newExpiry = $base->copy()->addDays($days);

        $wedding->update(['trial_expires_at' => $newExpiry]);

        $msg = "Masa aktif diperpanjang {$days} hari. Berlaku s/d {$newExpiry->format('d M Y')}.";

        if ($request->wantsJson()) {
            return response()->json(['message' => $msg]);
        }
        return back()->with('success', $msg);
    }

    /** Paksa expired sekarang (set trial_expires_at = now). Undangan tetap bisa dilihat (mode arsip). */
    public function forceExpire(Wedding $wedding): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $wedding->update(['trial_expires_at' => now()->subSecond()]);
        $msg = "Undangan {$wedding->bride_name} dipaksa expired. Link tetap bisa dilihat (mode arsip).";
        if (request()->wantsJson()) {
            return response()->json(['message' => $msg]);
        }
        return back()->with('success', $msg);
    }

    /** Aktifkan / nonaktifkan undangan. */
    public function toggleActive(Wedding $wedding): RedirectResponse
    {
        $wedding->update(['is_active' => !$wedding->is_active]);
        $status = $wedding->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Undangan berhasil {$status}.");
    }

    /** Cetak undangan fisik A5 per tamu (VIP only). */
    public function printInvitations(Request $request, Wedding $wedding): View
    {
        abort_unless($wedding->isVip(), 403, 'Fitur cetak fisik hanya tersedia untuk paket VIP.');

        $selectedIds = $request->input('guests');
        $guests = $selectedIds
            ? $wedding->guests()->whereIn('id', (array) $selectedIds)->orderBy('guest_name')->get()
            : $wedding->guests()->orderBy('guest_name')->get();

        $allGuests = $wedding->guests()->orderBy('guest_name')->get();

        return view('admin.print-invitations', compact('wedding', 'guests', 'allGuests'));
    }

    /* ======================================================================
       Upload foto — galeri & profil
    ====================================================================== */

    /** Upload foto ke galeri undangan. */
    public function uploadGallery(Request $request, Wedding $wedding)
    {
        if (!$wedding->has_gallery) {
            return response()->json(['success' => false, 'message' => 'Gallery not enabled for this wedding'], 403);
        }

        $request->validate([
            'photos'   => 'required|array|min:1|max:10',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png|max:10240',
        ]);

        $uploaded = [];
        $maxOrder = $wedding->gallery()->max('order') ?? 0;

        // P7-1: pakai ekstensi dari MIME type (bukan dari nama file yg bisa di-spoof)
        $mimeToExt = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        foreach ($request->file('photos') as $index => $photo) {
            $extension = $mimeToExt[$photo->getMimeType()] ?? 'jpg';
            $filename  = time() . '_' . $index . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
            $path      = $photo->storeAs('galleries', $filename, 'public');

            $gallery    = $wedding->gallery()->create(['path' => $path, 'order' => $maxOrder + $index + 1]);
            $uploaded[] = $gallery;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' photos uploaded successfully',
            'photos'  => $uploaded,
        ]);
    }

    /** Hapus satu foto dari galeri undangan. */
    public function deleteGalleryPhoto(Wedding $wedding, \App\Models\WeddingGallery $photo)
    {
        if ($photo->wedding_id !== $wedding->id) {
            return response()->json(['success' => false, 'message' => 'Photo not found'], 404);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return response()->json(['success' => true, 'message' => 'Photo deleted successfully']);
    }

    /** Upload foto profil (bride/groom/couple/cover/background). */
    public function uploadProfilePhoto(Request $request, Wedding $wedding)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'type'  => 'required|in:bride,groom,couple,cover,bg_mempelai,bg_acara,bg_lokasi',
        ]);

        $type      = $request->type;
        $fieldName = $type . '_photo';

        if ($wedding->$fieldName) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($wedding->$fieldName);
        }

        // P7-1: pakai ekstensi dari MIME type (bukan dari nama file yg bisa di-spoof)
        $photo     = $request->file('photo');
        $mimeToExt = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $extension = $mimeToExt[$photo->getMimeType()] ?? 'jpg';
        $filename  = $type . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $path      = $photo->storeAs('profile-photos', $filename, 'public');

        $wedding->update([$fieldName => $path]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' photo uploaded successfully',
            'url'     => asset('storage/' . $path),
        ]);
    }

    /** Hapus foto profil tertentu. */
    public function deleteProfilePhoto(Wedding $wedding, string $type)
    {
        if (!in_array($type, ['bride', 'groom', 'couple', 'cover', 'bg_mempelai', 'bg_acara', 'bg_lokasi'])) {
            return response()->json(['success' => false, 'message' => 'Invalid photo type'], 400);
        }

        $fieldName = $type . '_photo';

        if (!$wedding->$fieldName) {
            return response()->json(['success' => false, 'message' => 'Photo not found'], 404);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($wedding->$fieldName);
        $wedding->update([$fieldName => null]);

        return response()->json(['success' => true, 'message' => ucfirst($type) . ' photo deleted successfully']);
    }

    /**
     * Sanitasi field map_embed: hanya izinkan <iframe> Google Maps.
     * Tolak jika mengandung javascript:, <script>, event handler, atau URL non-Google.
     */
    public static function sanitizeMapEmbed(?string $embed): ?string
    {
        if ($embed === null || trim($embed) === '') {
            return null;
        }

        $embed = trim($embed);

        // Hanya izinkan tag <iframe> tunggal
        if (!preg_match('/^\s*<iframe\b[^>]*>\s*<\/iframe>\s*$/is', $embed)) {
            return null;
        }

        // Larang javascript: protocol
        if (preg_match('/javascript\s*:/i', $embed)) {
            return null;
        }

        // Larang event handler (onload=, onerror=, dsb.)
        if (preg_match('/\bon\w+\s*=/i', $embed)) {
            return null;
        }

        // Larang <script> di dalam atau sesudah iframe
        if (preg_match('/<script/i', $embed)) {
            return null;
        }

        // src harus dari domain Google Maps
        if (!preg_match('/\bsrc\s*=\s*["\']https:\/\/www\.google\.com\/maps\//i', $embed)) {
            return null;
        }

        return $embed;
    }
}
