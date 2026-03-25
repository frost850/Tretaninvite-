<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Diterima!</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/tretaninvite.css">
    <script src="/js/tretaninvite.js"></script>
    <style>
        .pay-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
        }
        .step-done  { background:rgba(34,197,94,.15);  color:#86efac; border:1px solid rgba(34,197,94,.3); }
        .step-active{ background:linear-gradient(135deg,#f59e0b,#ef4444); color:#fff; }
        .step-idle  { background:rgba(255,255,255,.06); color:rgba(255,255,255,.35); border:1px solid rgba(255,255,255,.08); }
        .ti-row { display:flex; justify-content:space-between; align-items:center; font-size:.8rem; padding:.35rem 0; border-bottom:1px solid rgba(255,255,255,.05); }
        .ti-row:last-child { border-bottom:none; }
        /* Timeline */
        .tl-title-done   { color: #86efac; }
        .tl-title-active { color: #fbbf24; }
        .tl-title-idle   { color: rgba(255,255,255,.25); }
        .tl-desc-active  { color: rgba(255,255,255,.5); }
        .tl-desc-idle    { color: rgba(255,255,255,.25); }
        .tl-connector-done { background: rgba(34,197,94,.4); }
        .tl-connector-idle { background: rgba(255,255,255,.07); }
    </style>
</head>
<body class="text-white antialiased" onload="TI.initPage({ starsId:'ti-stars', tiltSel:false })">

    <div id="ti-stars" style="position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;"></div>

    {{-- ════ NAVBAR ════ --}}
    <nav class="navbar px-6 py-4">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2 select-none">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-lg font-black shadow-lg">T</div>
                <span class="font-bold text-lg tracking-tight"><span class="text-amber-400">Tretan</span>Invite</span>
            </a>
            <span class="text-xs font-mono" style="color:rgba(255,255,255,.3)">#{{ strtoupper(substr($order->public_token, 0, 8)) }}</span>
        </div>
    </nav>

    {{-- ════ CONTENT ════ --}}
    <div class="page-bg pt-24 pb-20 px-4">
        <div class="inner max-w-lg mx-auto space-y-5">

            {{-- ── Langkah ── --}}
            <div class="flex items-center justify-center gap-2 text-xs font-semibold reveal">
                <span class="px-3 py-1.5 rounded-full step-done">✅ 1. Isi Data</span>
                <span style="color:rgba(255,255,255,.25)">→</span>
                <span class="px-3 py-1.5 rounded-full step-done">✅ 2. Bayar QRIS</span>
                <span style="color:rgba(255,255,255,.25)">→</span>
                <span class="px-3 py-1.5 rounded-full step-active">3. Selesai</span>
            </div>

            <div class="pay-card p-6 text-center reveal">

            {{-- Ikon dan judul sesuai status bayar --}}
            @if($order->trashed())
                <div class="text-5xl mb-3">🗑️</div>
                <h1 class="text-2xl font-black text-white mb-2">Pesanan Dibatalkan</h1>
                <p class="text-sm mb-5" style="color:rgba(255,255,255,.45)">
                    Maaf, <strong class="text-white">{{ $order->customer_name }}</strong>. Pesanan ini sudah tidak aktif atau telah dihapus.
                    Silakan buat pesanan baru atau hubungi admin untuk informasi lebih lanjut.
                </p>
            @elseif($order->payment_status === 'ditolak')
                <div class="text-5xl mb-3">❌</div>
                <h1 class="text-2xl font-black text-white mb-2">Pembayaran Ditolak</h1>
                <p class="text-sm mb-4" style="color:rgba(255,255,255,.45)">
                    Maaf, <strong class="text-white">{{ $order->customer_name }}</strong>. Kami tidak dapat mengkonfirmasi pembayaran Anda.
                </p>
                @if($order->rejection_reason)
                <div class="rounded-xl p-4 mb-5 text-left" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.35)">
                    <div class="flex items-start gap-3">
                        <div class="text-xl shrink-0">⚠️</div>
                        <div>
                            <div class="font-bold text-red-400 mb-1 text-sm">Alasan Penolakan</div>
                            <p class="text-sm text-white/80">{{ $order->rejection_reason }}</p>
                        </div>
                    </div>
                </div>
                @endif
            @elseif($order->payment_status === 'lunas')
                <div class="text-5xl mb-3">🎉</div>
                <h1 class="text-2xl font-black text-white mb-2">Pembayaran Dikonfirmasi!</h1>
                <p class="text-sm mb-5" style="color:rgba(255,255,255,.45)">
                    Terima kasih, <strong class="text-white">{{ $order->customer_name }}</strong>. Pembayaran Anda sudah dikonfirmasi lunas. Tim kami akan segera memproses undangan Anda.
                </p>
            @elseif($order->payment_status === 'menunggu_konfirmasi')
                <div class="text-5xl mb-3">🔍</div>
                <h1 class="text-2xl font-black text-white mb-2">Bukti Bayar Terkirim!</h1>
                <p class="text-sm mb-5" style="color:rgba(255,255,255,.45)">
                    Terima kasih, <strong class="text-white">{{ $order->customer_name }}</strong>. Bukti bayar Anda sudah kami terima dan menunggu konfirmasi admin. Kami akan menghubungi Anda setelah dikonfirmasi.
                </p>
            @else
                <div class="text-5xl mb-3">📋</div>
                <h1 class="text-2xl font-black text-white mb-2">Pesanan Diterima!</h1>
                <p class="text-sm mb-4" style="color:rgba(255,255,255,.45)">
                    Terima kasih, <strong class="text-white">{{ $order->customer_name }}</strong>. Pesanan Anda sudah masuk. Selesaikan pembayaran untuk memproses undangan Anda.
                </p>
                <a href="{{ route('orders.payment', $order->payment_token) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white mb-2"
                   style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
                    💳 Selesaikan Pembayaran →
                </a>
            @endif

            {{-- PENTING: Save Link --}}
            <div class="rounded-xl p-4 mb-5 text-left" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25)">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">📌</div>
                    <div class="flex-1">
                        <div class="font-bold text-amber-400 mb-1 text-sm">Simpan Link Ini!</div>
                        <p class="text-xs mb-3" style="color:rgba(255,255,255,.4)">
                            Salin dan simpan alamat halaman ini untuk memantau progres pesanan Anda kapan saja.
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly
                                   value="{{ url()->current() }}"
                                   id="trackingUrl"
                                   class="ti-input flex-1 text-xs font-mono">
                            <button onclick="copyTrackingUrl()"
                                    class="shrink-0 px-3 py-2 rounded-xl text-xs font-bold text-white whitespace-nowrap"
                                    style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
                                📋 Salin
                            </button>
                        </div>
                        <div id="copyFeedback" class="text-xs text-green-400 mt-2 hidden">✅ Link berhasil disalin!</div>
                    </div>
                </div>
            </div>

            </div>{{-- /pay-card --}}

            {{-- Portal VIP / Premium (hanya jika undangan sudah dibuat & lunas) --}}
            @if($order->package === 'vip' && $order->wedding_id)
            <div class="pay-card p-5 reveal text-left" style="background:rgba(245,158,11,.06);border-color:rgba(245,158,11,.3)">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">👑</div>
                    <div class="flex-1">
                        <div class="font-bold text-amber-400 mb-1 text-sm">Portal VIP Anda</div>
                        <p class="text-xs mb-3" style="color:rgba(255,255,255,.4)">
                            Akses dashboard VIP untuk memantau statistik tamu, moderasi guestbook, dan fitur eksklusif lainnya.
                        </p>
                        <a href="{{ route('my.vip.dashboard', $order->public_token) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white"
                           style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                            👑 Buka Portal VIP →
                        </a>
                    </div>
                </div>
            </div>
            @elseif($order->package === 'premium' && $order->wedding_id)
            <div class="pay-card p-5 reveal text-left" style="background:rgba(139,92,246,.06);border-color:rgba(139,92,246,.3)">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">💎</div>
                    <div class="flex-1">
                        <div class="font-bold text-violet-400 mb-1 text-sm">Portal Premium Anda</div>
                        <p class="text-xs mb-3" style="color:rgba(255,255,255,.4)">
                            Akses dashboard Premium untuk mengelola daftar tamu, scan QR, dan memantau progres undangan Anda.
                        </p>
                        <a href="{{ route('my.vip.dashboard', $order->public_token) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white"
                           style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)">
                            💎 Buka Portal Premium →
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <script>
                function copyTrackingUrl() {
                    const input = document.getElementById('trackingUrl');
                    const feedback = document.getElementById('copyFeedback');
                    input.select();
                    input.setSelectionRange(0, 99999);
                    navigator.clipboard.writeText(input.value).then(() => {
                        feedback.classList.remove('hidden');
                        setTimeout(() => feedback.classList.add('hidden'), 3000);
                    });
                }
            </script>

            {{-- Ringkasan order --}}
            <div class="pay-card p-5 reveal text-left">
                <div class="ti-row">
                    <span style="color:rgba(255,255,255,.4)">Order ID</span>
                    <span class="font-mono font-bold text-amber-400">#{{ strtoupper(substr($order->public_token, 0, 8)) }}</span>
                </div>
                <div class="ti-row">
                    <span style="color:rgba(255,255,255,.4)">Template</span>
                    <span class="font-medium capitalize text-white">{{ $order->template }}</span>
                </div>
                <div class="ti-row">
                    <span style="color:rgba(255,255,255,.4)">{{ $order->groom_name ? 'Pasangan' : 'Yang Berulang Tahun' }}</span>
                    <span class="font-medium text-white">{{ $order->bride_name }}{{ $order->groom_name ? ' & ' . $order->groom_name : '' }}</span>
                </div>
                @if($order->event_date)
                <div class="ti-row">
                    <span style="color:rgba(255,255,255,.4)">Tanggal</span>
                    <span class="text-white">{{ $order->event_date->format('d M Y') }}</span>
                </div>
                @endif
                @if($order->location)
                <div class="ti-row">
                    <span style="color:rgba(255,255,255,.4)">Lokasi</span>
                    <span class="text-white">{{ $order->location }}</span>
                </div>
                @endif
                <div class="ti-row">
                    <span style="color:rgba(255,255,255,.4)">No WA Anda</span>
                    <span class="text-white">{{ $order->customer_phone }}</span>
                </div>
            </div>

            {{-- TIMELINE PROGRESS TRACKER --}}
            @php
                $stages = [
                    [
                        'icon' => '📝',
                        'title' => 'Pesanan Diterima',
                        'desc' => 'Order masuk ke sistem',
                        'done' => true,
                        'active' => $order->payment_status === 'belum_bayar' && $order->status === 'baru'
                    ],
                    [
                        'icon' => '💳',
                        'title' => 'Pembayaran',
                        'desc' => match($order->payment_status) {
                            'lunas'                => 'Pembayaran lunas',
                            'menunggu_konfirmasi'  => 'Menunggu konfirmasi admin',
                            'ditolak'              => 'Pembayaran ditolak',
                            default                => 'Menunggu pembayaran',
                        },
                        'done' => $order->payment_status === 'lunas',
                        'active' => !in_array($order->payment_status, ['lunas', 'ditolak'])
                    ],
                    [
                        'icon' => '⚙️',
                        'title' => 'Sedang Diproses',
                        'desc' => 'Tim membuat undangan Anda',
                        'done' => in_array($order->status, ['diproses', 'selesai']) && $order->payment_status === 'lunas',
                        'active' => $order->status === 'diproses' && $order->payment_status === 'lunas'
                    ],
                    [
                        'icon' => '✅',
                        'title' => 'Selesai',
                        'desc' => 'Undangan siap digunakan',
                        'done' => $order->status === 'selesai',
                        'active' => false
                    ],
                ];
            @endphp

            <div class="pay-card p-5 reveal">
                <h3 class="text-sm font-bold text-white/70 mb-4">Status Pemrosesan</h3>

                {{-- Estimasi waktu --}}
                @if($order->status !== 'selesai')
                <div class="rounded-xl p-3 mb-4" style="background:rgba(96,165,250,.08);border:1px solid rgba(96,165,250,.2)">
                    <div class="flex items-start gap-2">
                        <span class="text-blue-400 text-lg">⏱️</span>
                        <div class="flex-1">
                            <div class="text-xs font-bold text-blue-400 mb-1">Estimasi Waktu</div>
                            <div class="text-xs" style="color:rgba(255,255,255,.5)">
                                @if($order->payment_status === 'belum_bayar')
                                    Selesaikan pembayaran untuk memulai proses
                                @elseif($order->payment_status === 'menunggu_konfirmasi')
                                    Konfirmasi pembayaran: 1-24 jam
                                @elseif($order->status === 'diproses')
                                    Pembuatan undangan: 2-3 hari kerja
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="space-y-3">
                    @foreach($stages as $index => $stage)
                        @php
                            $nextDone      = isset($stages[$index + 1]) && $stages[$index + 1]['done'];
                            $titleClass    = $stage['active'] ? 'tl-title-active' : ($stage['done'] ? 'tl-title-done' : 'tl-title-idle');
                            $descClass     = $stage['active'] ? 'tl-desc-active' : 'tl-desc-idle';
                            $connectorClass= $nextDone ? 'tl-connector-done' : 'tl-connector-idle';
                        @endphp
                        <div class="flex items-start gap-3">
                            {{-- Icon/Status Circle --}}
                            <div class="flex-shrink-0 relative">
                                @if($stage['done'])
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg" style="background:rgba(34,197,94,.2);color:#86efac;border:1px solid rgba(34,197,94,.35)">
                                        ✓
                                    </div>
                                @elseif($stage['active'])
                                    <div class="w-10 h-10 rounded-full text-white flex items-center justify-center text-lg animate-pulse" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
                                        {{ $stage['icon'] }}
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg" style="background:rgba(255,255,255,.05);color:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.07)">
                                        {{ $stage['icon'] }}
                                    </div>
                                @endif

                                {{-- Connector line --}}
                                @if($index < count($stages) - 1)
                                    <div class="absolute left-1/2 top-10 w-0.5 h-6 -ml-px {{ $connectorClass }}"></div>
                                @endif
                            </div>

                            {{-- Text --}}
                            <div class="flex-1 pt-1 text-left">
                                <div class="font-bold text-sm {{ $titleClass }}">
                                    {{ $stage['title'] }}
                                </div>
                                <div class="text-xs mt-0.5 {{ $descClass }}">
                                    {{ $stage['desc'] }}
                                </div>
                                @if($stage['active'])
                                    <div class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold" style="background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.3)">
                                        Sedang Berlangsung
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA sesuai status --}}
            @if($order->trashed())
                <a href="{{ route('welcome') }}"
                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl font-bold text-sm text-white mb-3 transition hover:opacity-90"
                   style="background:linear-gradient(135deg,#6b7280,#4b5563)">
                    🏠 Buat Pesanan Baru
                </a>
            @elseif($order->payment_status === 'ditolak')
                <a href="{{ $order->paymentWhatsappLink($adminPhone) }}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl font-bold text-sm text-white mb-3 transition hover:opacity-90"
                   style="background:linear-gradient(135deg,#dc2626,#b91c1c);box-shadow:0 0 20px rgba(220,38,38,.3)">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.119.555 4.107 1.523 5.83L.057 24l6.305-1.652A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.655-.505-5.19-1.39l-.372-.22-3.862 1.013 1.032-3.763-.241-.387A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    Hubungi Admin via WhatsApp
                </a>
                <p class="text-xs mb-4" style="color:rgba(255,255,255,.3)">Hubungi admin untuk klarifikasi atau melakukan pembayaran ulang.</p>
            @elseif($order->payment_status === 'belum_bayar')
                <a href="{{ route('orders.payment', $order->payment_token) }}"
                   class="btn-glow flex items-center justify-center gap-2 w-full py-3 rounded-xl font-bold text-sm mb-3">
                    💳 Bayar Sekarang
                </a>
            @else
                <a href="{{ $order->paymentWhatsappLink($adminPhone) }}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl font-bold text-sm text-white mb-3 transition hover:opacity-90"
                   style="background:linear-gradient(135deg,#22c55e,#16a34a);box-shadow:0 0 20px rgba(34,197,94,.3)">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.119.555 4.107 1.523 5.83L.057 24l6.305-1.652A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.655-.505-5.19-1.39l-.372-.22-3.862 1.013 1.032-3.763-.241-.387A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    Chat Admin via WhatsApp
                </a>
                <p class="text-xs mb-4" style="color:rgba(255,255,255,.3)">Admin akan mengkonfirmasi pembayaran dalam 1×24 jam.</p>
            @endif

            {{-- Refresh Status Button --}}
            <button onclick="location.reload()"
                    class="w-full py-2.5 rounded-xl font-medium text-sm flex items-center justify-center gap-2 mb-4 transition hover:opacity-80"
                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.55)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh Status
            </button>

            <a href="{{ route('welcome') }}" class="text-amber-400 hover:text-amber-300 transition text-sm">← Kembali ke beranda</a>
        </div>
    </div>

    {{-- Auto-notify admin via WA saat bukti bayar baru diupload --}}
    @if(!empty($adminNotifyUrl))
    <div id="admin-notify-banner"
         class="fixed bottom-0 left-0 right-0 px-4 py-3 flex items-center justify-between gap-3 z-50 shadow-2xl"
         style="background:linear-gradient(135deg,#16a34a,#15803d);backdrop-filter:blur(12px)">
        <div class="flex items-center gap-2 text-sm font-medium text-white">
            <span class="text-lg">📲</span>
            Klik untuk beritahu admin bukti bayar sudah diupload:
        </div>
        <a href="{{ $adminNotifyUrl }}" target="_blank"
           onclick="document.getElementById('admin-notify-banner').remove()"
           class="shrink-0 px-4 py-2 bg-white text-green-700 font-bold text-sm rounded-xl hover:bg-green-50 transition whitespace-nowrap">
            Beritahu Admin →
        </a>
    </div>
    <script>
        setTimeout(function() {
            window.open('{{ $adminNotifyUrl }}', '_blank');
        }, 1500);
    </script>
    @endif

        </div>{{-- /inner --}}
    </div>{{-- /page-bg --}}
</body>
</html>
