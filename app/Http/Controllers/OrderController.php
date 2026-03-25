<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wedding;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\TemplateRegistry;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    /** Form pesan paket (publik — untuk pelanggan) */
    public function create(Request $request): View
    {
        $template  = $request->query('template', 'classic');
        $templates = TemplateRegistry::all();

        // Fallback ke classic jika template tidak valid
        if (!isset($templates[$template])) {
            $template = 'classic';
        }

        $category = $templates[$template]['category'] ?? 'wedding';
        $isBirthday   = $category === 'birthday';
        $isGreeting   = $category === 'greeting';
        $isAnniversary = $category === 'anniversary';
        $isVipTemplate = !empty($templates[$template]['vip_only']);

        // Baca pkg dari URL untuk pre-select paket di form
        $pkgFromUrl = $request->query('pkg');
        $validPkgs  = ['basic', 'premium', 'vip'];
        $defaultPkg = $isVipTemplate ? 'vip'
            : (in_array($pkgFromUrl, $validPkgs) ? $pkgFromUrl : 'basic');

        // Perpanjang: pre-fill form dari order yang ada via public_token
        $prefill    = [];
        $renewToken = null;
        $isRenewal  = false;
        $renewWedding = null;
        $renewParam = $request->query('renew', old('renew_token'));
        if ($renewParam) {
            $renewOrder = Order::where('public_token', $renewParam)->with('wedding')->first();
            if ($renewOrder) {
                $renewToken   = $renewParam;
                $isRenewal    = true;
                $renewWedding = $renewOrder->wedding;
                // Force template from the existing order
                $template    = $renewOrder->template ?? $template;
                if (!isset($templates[$template])) {
                    $template = 'classic';
                }
                $category      = $templates[$template]['category'] ?? 'wedding';
                $isBirthday    = $category === 'birthday';
                $isGreeting    = $category === 'greeting';
                $isAnniversary = $category === 'anniversary';
                $isVipTemplate = !empty($templates[$template]['vip_only']);

                $prefill = [
                    'bride_name'     => $renewOrder->bride_name ?? '',
                    'groom_name'     => $renewOrder->groom_name ?? '',
                    'event_date'     => $renewOrder->event_date?->format('Y-m-d') ?? '',
                    'location'       => $renewOrder->location ?? '',
                    'customer_name'  => $renewOrder->customer_name ?? '',
                    'customer_phone' => $renewOrder->customer_phone ?? '',
                    'customer_email' => $renewOrder->customer_email ?? '',
                    'package'        => $renewOrder->package ?? $defaultPkg,
                ];
                if (!in_array($pkgFromUrl, $validPkgs)) {
                    $defaultPkg = $isVipTemplate ? 'vip' : ($prefill['package'] ?: $defaultPkg);
                }
            }
        }

        // Perpanjang: tampilkan view khusus (bukan form pesanan penuh)
        if ($isRenewal) {
            return view('orders.renew', [
                'template'      => $template,
                'templateInfo'  => $templates[$template],
                'category'      => $category,
                'prefill'       => $prefill,
                'renewToken'    => $renewToken,
                'renewWedding'  => $renewWedding,
            ]);
        }

        return view('orders.create', [
            'template'      => $template,
            'templateInfo'  => $templates[$template],
            'templates'     => $templates,
            'category'      => $category,
            'isBirthday'    => $isBirthday,
            'isGreeting'    => $isGreeting,
            'isAnniversary' => $isAnniversary,
            'isVipTemplate' => $isVipTemplate,
            'defaultPkg'    => $defaultPkg,
            'prefill'       => $prefill,
            'renewToken'    => $renewToken,
            'isRenewal'     => false,
        ]);
    }

    /** Simpan order & redirect ke halaman pembayaran (via token) */
    public function store(Request $request): RedirectResponse
    {
        $templates = TemplateRegistry::all();

        // BH-8: Whitelist template sebelum dispatch ke blok validasi per-kategori
        $request->validate([
            'template' => 'required|string|in:' . implode(',', array_keys($templates)),
        ]);

        // ── Perpanjangan masa aktif — form khusus (bukan form pesanan penuh) ──
        if ($request->filled('renewal_days')) {
            $renewToken = $request->input('renew_token', '');
            $renewOrder = Order::where('public_token', $renewToken)->with('wedding')->first();

            // Validasi: token harus valid & terhubung ke wedding aktif
            abort_unless($renewOrder && $renewOrder->wedding, 422, 'Token perpanjangan tidak valid.');

            $request->validate([
                'renewal_days'   => 'required|integer|min:1|max:365',
                'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9\+\-\s\(\)]+$/'],
                'customer_email' => 'nullable|email|max:255',
                'notes'          => 'nullable|string|max:500',
            ]);

            $days = (int) $request->input('renewal_days');
            $expiryMinutes = (int) config('admin.order_expiry_minutes', 30);

            $order = Order::create([
                'template'       => $renewOrder->template,
                'package'        => $renewOrder->package,
                'bride_name'     => $renewOrder->bride_name,
                'groom_name'     => $renewOrder->groom_name,
                'event_date'     => $renewOrder->event_date,
                'location'       => $renewOrder->location,
                'customer_name'  => $renewOrder->customer_name,
                'customer_phone' => $request->input('customer_phone'),
                'customer_email' => $request->input('customer_email'),
                'notes'          => $request->input('notes'),
                'renewal_days'   => $days,
                'wedding_id'     => $renewOrder->wedding->id,
                'status'         => 'baru',
                'payment_status' => 'belum_bayar',
                'expires_at'     => now()->addMinutes($expiryMinutes),
            ]);

            return redirect()->route('orders.payment', $order->payment_token);
        }

        $template = $request->input('template', 'classic');
        $category = $templates[$template]['category'] ?? 'wedding';
        $isBirthday    = $category === 'birthday';
        $isGreeting    = $category === 'greeting';
        $isAnniversary = $category === 'anniversary';

        $phoneRule = ['required', 'string', 'max:30', 'regex:/^[0-9\+\-\s\(\)]+$/'];

        if ($isBirthday) {
            // ── Ulang Tahun ──────────────────────────────────────────────
            $data = $request->validate([
                'template'       => 'required|string',
                'package'        => 'required|in:basic,premium',
                'bride_name'     => 'required|string|max:100',
                'event_date'     => 'nullable|date',
                'location'       => 'nullable|string|max:255',
                'customer_name'  => 'required|string|max:100',
                'customer_phone' => $phoneRule,
                'customer_email' => 'nullable|email|max:255',
                'notes'          => 'nullable|string|max:1000',
            ]);
            $data['groom_name'] = '';
        } elseif ($isGreeting) {
            // ── Greeting Card ─────────────────────────────────────────────
            $data = $request->validate([
                'template'       => 'required|string',
                'package'        => 'required|in:basic,premium',
                'bride_name'     => 'required|string|max:100',
                'event_date'     => 'nullable|date',
                'location'       => 'nullable|string|max:255',
                'customer_name'  => 'required|string|max:100',
                'customer_phone' => $phoneRule,
                'customer_email' => 'nullable|email|max:255',
                'notes'          => 'nullable|string|max:1000',
            ]);
            $data['groom_name'] = '';
        } elseif ($isAnniversary) {
            // ── Anniversary ───────────────────────────────────────────────
            $data = $request->validate([
                'template'       => 'required|string',
                'package'        => 'required|in:basic,premium',
                'bride_name'     => 'required|string|max:100',
                'groom_name'     => 'required|string|max:100',
                'event_date'     => 'nullable|date',
                'location'       => 'nullable|string|max:255',
                'customer_name'  => 'required|string|max:100',
                'customer_phone' => $phoneRule,
                'customer_email' => 'nullable|email|max:255',
                'notes'          => 'nullable|string|max:1000',
            ]);
        } else {
            // ── Pernikahan ───────────────────────────────────────────────
            $vipOnly = !empty($templates[$template]['vip_only']);
            $data = $request->validate([
                'template'       => 'required|string',
                'package'        => $vipOnly ? 'required|in:vip' : 'required|in:basic,premium,vip',
                'bride_name'     => 'required|string|max:100',
                'groom_name'     => 'required|string|max:100',
                'event_date'     => 'nullable|date',
                'location'       => 'nullable|string|max:255',
                'customer_name'  => 'required|string|max:100',
                'customer_phone' => $phoneRule,
                'customer_email' => 'nullable|email|max:255',
                'notes'          => 'nullable|string|max:1000',
            ]);
        }

        $expiryMinutes = (int) config('admin.order_expiry_minutes', 30);

        $order = Order::create($data + [
            'status'         => 'baru',
            'payment_status' => 'belum_bayar',
            'expires_at'     => now()->addMinutes($expiryMinutes),
        ]);

        return redirect()->route('orders.payment', $order->payment_token);
    }

    /** Halaman terima kasih setelah upload bukti (publik) */
    public function thanks(string $token): View
    {
        $order = Order::withTrashed()->where('public_token', $token)->firstOrFail();
        $adminPhone = config('admin.whatsapp', '');

        // Jika baru saja upload bukti, siapkan link WA untuk notifikasi admin
        $adminNotifyUrl = null;
        if (session()->pull('notify_admin_proof') && $adminPhone) {
            $nama = $order->groom_name
                ? "{$order->bride_name} & {$order->groom_name}"
                : $order->bride_name;
            $orderId = strtoupper(substr($order->public_token, 0, 8));
            $msg = "⚠️ Bukti bayar masuk!\n\n"
                . "Order #${orderId}\n"
                . "Nama: {$nama}\n"
                . "Template: {$order->template}\n"
                . "Paket: " . $order->packageLabel() . " (" . $order->packagePrice() . ")\n"
                . "Pemesan: {$order->customer_name}\n"
                . "WA: {$order->customer_phone}\n\n"
                . "Silakan konfirmasi di panel admin.";
            $adminNotifyUrl = 'https://wa.me/' . preg_replace('/\D+/', '', $adminPhone)
                . '?text=' . rawurlencode($msg);
        }

        return view('orders.thanks', compact('order', 'adminPhone', 'adminNotifyUrl'));
    }

    /** Halaman pembayaran QRIS — dicari via token (publik) */
    public function payment(string $token): View|RedirectResponse
    {
        $order = Order::withTrashed()->where('payment_token', $token)->firstOrFail();

        // Order dihapus — tampilkan halaman terima kasih dengan status dibatalkan
        if ($order->trashed()) {
            return redirect()->route('orders.thanks', $order->public_token);
        }

        // Sudah bayar, ditolak, atau status lain — arahkan ke terima kasih
        if ($order->payment_status !== 'belum_bayar') {
            return redirect()->route('orders.thanks', $order->public_token);
        }

        // Cek kedaluwarsa
        if ($order->isExpired()) {
            $order->delete();
            return redirect()->route('orders.create')
                ->with('error', 'Waktu pembayaran habis. Silakan buat pesanan baru.');
        }

        $qrisUrl    = Storage::disk('public')->exists('qris.png')
            ? asset('storage/qris.png') . '?v=' . filemtime(storage_path('app/public/qris.png'))
            : null;
        $adminPhone = config('admin.whatsapp', '6282139069782');

        return view('orders.payment', compact('order', 'token', 'qrisUrl', 'adminPhone'));
    }

    /** Upload bukti bayar — dicari via token, cek expiry ketat */
    public function uploadProof(Request $request, string $token): RedirectResponse
    {
        $order = Order::withTrashed()->where('payment_token', $token)->firstOrFail();

        // Order dihapus atau ditolak — jangan izinkan upload
        if ($order->trashed() || $order->payment_status === 'ditolak') {
            return redirect()->route('orders.thanks', $order->public_token);
        }

        // Tolak jika sudah expired
        if ($order->isExpired()) {
            $order->delete();
            return redirect()->route('orders.create')
                ->with('error', 'Waktu pembayaran habis. Silakan buat pesanan baru.');
        }

        // Tolak jika sudah pernah upload (cegah spam re-upload)
        if ($order->payment_status !== 'belum_bayar') {
            return redirect()->route('orders.thanks', $order->public_token);
        }

        $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'proof.required' => 'Harap upload foto bukti pembayaran.',
            'proof.mimes'    => 'File harus berupa gambar JPG, PNG, atau PDF.',
            'proof.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $path = $request->file('proof')->store('payment-proofs', 'local'); // L-2: private storage

        $order->update([
            'payment_proof'  => $path,
            'payment_status' => 'menunggu_konfirmasi',
            // Hapus expiry — sudah upload, tidak perlu kedaluwarsa
            'expires_at'     => null,
        ]);

        // Tandai agar halaman terima kasih auto-buka notifikasi WA ke admin
        session()->flash('notify_admin_proof', true);

        return redirect()->route('orders.thanks', $order->public_token);
    }

    // ═══════════════════════════════
    //  ADMIN AREA
    // ═══════════════════════════════

    /** Unduh/tampilkan bukti bayar — hanya admin terautentikasi (L-2) */
    public function proofDownload(Order $order): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        abort_unless(session('admin_authenticated'), 403);
        abort_unless($order->payment_proof, 404);
        abort_unless(Storage::disk('local')->exists($order->payment_proof), 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        return $disk->response($order->payment_proof);
    }

    /** Daftar semua order (admin) */
    public function adminIndex(Request $request): View
    {
        $status        = $request->query('status');
        $paymentFilter = $request->query('payment');
        $date          = $request->query('date');     // 'today' | null
        $category      =    $request->query('category'); // 'wedding'|'birthday'|'greeting'|null
        $search        = trim($request->query('search', ''));

        $orders = Order::query()
            ->with('wedding')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($paymentFilter, fn($q) => $q->where('payment_status', $paymentFilter))
            ->when($date === 'today', fn($q) => $q->whereDate('created_at', now()->toDateString()))
            ->when($category === 'wedding',     fn($q) => $q->where(fn($s) =>
                $s->where('template', 'not like', 'birthday%')
                  ->where('template', 'not like', 'greeting%')
                  ->where('template', 'not like', 'anniversary%')))
            ->when($category === 'birthday',    fn($q) => $q->where('template', 'like', 'birthday%'))
            ->when($category === 'greeting',    fn($q) => $q->where('template', 'like', 'greeting%'))
            ->when($category === 'anniversary', fn($q) => $q->where('template', 'like', 'anniversary%'))
            ->when($search !== '', fn($q) => $q->where(function ($s) use ($search) {
                $s->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('bride_name', 'like', "%{$search}%")
                  ->orWhere('groom_name', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(30);

        // Hitung per-kategori (tidak mempedulikan filter status/payment/date)
        $categoryCounts = [
            'wedding'     => Order::where('template', 'not like', 'birthday%')
                                   ->where('template', 'not like', 'greeting%')
                                   ->where('template', 'not like', 'anniversary%')
                                   ->count(),
            'birthday'    => Order::where('template', 'like', 'birthday%')->count(),
            'greeting'    => Order::where('template', 'like', 'greeting%')->count(),
            'anniversary' => Order::where('template', 'like', 'anniversary%')->count(),
        ];

        // Stat ringkas untuk header
        $statCounts = [
            'total'        => Order::count(),
            'baru'         => Order::where('status', 'baru')->count(),
            'menunggu'     => Order::where('payment_status', 'menunggu_konfirmasi')->count(),
            'today'        => Order::whereDate('created_at', now()->toDateString())->count(),
        ];

        $ditolakCount = Order::where('payment_status', 'ditolak')->count();
        $weddings     = Wedding::orderBy('bride_name')->get();

        return view('admin.orders.index', compact(
            'orders', 'status', 'date', 'weddings', 'paymentFilter',
            'ditolakCount', 'category', 'categoryCounts', 'statCounts', 'search'
        ));
    }

    /** Update status order (admin) */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['status' => 'required|in:baru,diproses,selesai']);
        $old = $order->status;
        $order->update(['status' => $request->status]);

        // Flash WA notifikasi saat status berubah ke selesai
        if ($request->status === 'selesai' && $old !== 'selesai') {
            $order->load('wedding');
            $domain = rtrim(config('app.url'), '/');
            $opts = [
                'wedding_url'  => $order->wedding ? $domain . '/' . $order->wedding->slug : null,
                'tracking_url' => $order->wedding ? $domain . '/tracking/' . $order->wedding->tracking_token : null,
            ];
            $waUrl = $order->customerWhatsappLink('selesai', $opts);
            return back()->with('wa_notification', [
                'url'      => $waUrl,
                'name'     => $order->customer_name,
                'order_id' => strtoupper(substr($order->public_token, 0, 8)),
                'type'     => 'selesai',
                'label'    => 'Undangan Selesai',
            ]);
        }

        // Flash WA notifikasi saat status berubah ke diproses
        if ($request->status === 'diproses' && $old !== 'diproses') {
            $waUrl = $order->customerWhatsappLink('diproses');
            return back()->with('wa_notification', [
                'url'      => $waUrl,
                'name'     => $order->customer_name,
                'order_id' => strtoupper(substr($order->public_token, 0, 8)),
                'type'     => 'diproses',
                'label'    => 'Sedang Diproses',
            ]);
        }

        return back()->with('success', "Status order diperbarui.");
    }

    /** Link order ke wedding yang sudah dibuat (admin) */
    public function linkWedding(Request $request, Order $order): RedirectResponse
    {
        if ($order->payment_status !== 'lunas') {
            return back()->with('error', 'Undangan hanya bisa ditautkan ke pesanan yang sudah LUNAS.');
        }

        $request->validate(['wedding_id' => 'required|exists:weddings,id']);
        $order->update(['wedding_id' => $request->wedding_id, 'status' => 'selesai']);
        $order->load('wedding');

        // Set masa aktif undangan berdasarkan paket order
        if ($order->wedding) {
            $package = $order->package ?? 'basic';
            $order->wedding->update([
                'package'          => $package,
                'trial_expires_at' => now()->addDays(\App\Models\Wedding::expiryDays($package)),
            ]);
        }

        $domain = rtrim(config('app.url'), '/');
        $waUrl  = $order->customerWhatsappLink('selesai', [
            'wedding_url'  => $order->wedding ? $domain . '/' . $order->wedding->slug : null,
            'tracking_url' => $order->wedding ? $domain . '/tracking/' . $order->wedding->tracking_token : null,
        ]);

        return back()->with('wa_notification', [
            'url'      => $waUrl,
            'name'     => $order->customer_name,
            'order_id' => strtoupper(substr($order->public_token, 0, 8)),
            'type'     => 'selesai',
            'label'    => 'Undangan Selesai',
        ]);
    }

    /** Konfirmasi pembayaran lunas (admin) */
    public function confirmPayment(Order $order): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $order->update([
            'payment_status' => 'lunas',
            'status'         => 'diproses',
        ]);

        AuditLogger::log('payment_confirmed', 'order', $order->id, ['customer' => $order->customer_name]);

        $waUrl = $order->customerWhatsappLink('lunas');

        if (request()->wantsJson()) {
            return response()->json([
                'message'       => "Pembayaran {$order->customer_name} dikonfirmasi sebagai LUNAS.",
                'wa_url'        => $waUrl,
                'wa_label'      => 'Pembayaran Dikonfirmasi',
                'customer_name' => $order->customer_name,
                'order_id'      => strtoupper(substr($order->public_token, 0, 8)),
            ]);
        }

        return back()->with('wa_notification', [
            'url'      => $waUrl,
            'name'     => $order->customer_name,
            'order_id' => strtoupper(substr($order->public_token, 0, 8)),
            'type'     => 'lunas',
            'label'    => 'Pembayaran Dikonfirmasi',
        ]);
    }

    /** Tolak pembayaran dengan alasan (admin) */
    public function rejectPayment(Order $order, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi.',
            'reason.max'      => 'Alasan maksimal 500 karakter.',
        ]);

        $order->update([
            'payment_status'   => 'ditolak',
            'rejection_reason' => $request->reason,
        ]);

        AuditLogger::log('payment_rejected', 'order', $order->id, [
            'customer' => $order->customer_name,
            'reason'   => $request->reason,
        ]);

        $waUrl = $order->customerWhatsappLink('ditolak', ['reason' => $request->reason]);

        return back()->with('wa_notification', [
            'url'      => $waUrl,
            'name'     => $order->customer_name,
            'order_id' => strtoupper(substr($order->public_token, 0, 8)),
            'type'     => 'ditolak',
            'label'    => 'Pembayaran Ditolak',
        ]);
    }

    /** Buka ulang pesanan yang ditolak — reset ke belum_bayar agar customer bisa upload ulang (admin) */
    public function resetPayment(Order $order): RedirectResponse
    {
        if ($order->payment_status !== 'ditolak') {
            return back()->with('error', 'Hanya pesanan berstatus Ditolak yang dapat dibuka ulang.');
        }

        // Simpan path bukti lama sebelum di-clear
        $oldProof = $order->payment_proof;

        $order->update([
            'payment_status'   => 'belum_bayar',
            'rejection_reason' => null,
            'payment_proof'    => null,
            'expires_at'       => now()->addHours(24), // berikan waktu 24 jam untuk upload ulang
        ]);

        // Hapus file bukti lama dari storage
        if ($oldProof) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($oldProof);
        }

        AuditLogger::log('payment_reset', 'order', $order->id, [
            'customer' => $order->customer_name,
        ]);

        $domain = rtrim(config('app.url'), '/');
        $waUrl  = $order->customerWhatsappLink('pengingat_bayar', [
            'payment_url' => $domain . '/pesan/bayar/' . $order->payment_token,
        ]);

        return back()->with('wa_notification', [
            'url'      => $waUrl,
            'name'     => $order->customer_name,
            'order_id' => strtoupper(substr($order->public_token, 0, 8)),
            'type'     => 'pengingat_bayar',
            'label'    => 'Pesanan Dibuka Ulang',
        ]);
    }

    /** Upload / ganti gambar QRIS (admin) */
    public function uploadQris(Request $request): RedirectResponse
    {
        $request->validate([
            'qris' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'qris.required' => 'Pilih file gambar QRIS.',
            'qris.mimes'    => 'File harus berupa JPG atau PNG.',
            'qris.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        // Simpan ke storage/app/public/qris.png (overwrite)
        // Diakses via public/storage/qris.png (symlink sudah ada)
        $request->file('qris')->storeAs('', 'qris.png', 'public');

        return back()->with('success', 'Gambar QRIS berhasil diperbarui.');
    }

    /** Hapus order (admin) — untuk order iseng / spam / tidak valid */
    public function destroy(Order $order): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $orderId  = strtoupper(substr($order->public_token, 0, 8));
        $customer = $order->customer_name;

        // Soft delete — file bukti tetap ada sampai dipurge dari Recycle Bin
        $order->delete();

        AuditLogger::log('order_trashed', 'order', $order->id, [
            'customer' => $customer,
            'token'    => $orderId,
        ]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "Order #{$orderId} ({$customer}) dipindahkan ke Recycle Bin."]);
        }

        return redirect()->route('admin.orders.index')
            ->with('success', "Order #{$orderId} ({$customer}) dipindahkan ke Recycle Bin.");
    }
}
