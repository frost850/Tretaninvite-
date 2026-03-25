<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran – #{{ strtoupper(substr($order->public_token, 0, 8)) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/tretaninvite.css">
    <script src="/js/tretaninvite.js"></script>
    <style>
        /* ─── payment-page specific ─── */
        .pay-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
        }
        .drop-zone {
            border: 2px dashed rgba(255,255,255,.15);
            border-radius: 16px;
            transition: border-color .2s, background .2s;
            cursor: pointer;
        }
        .drop-zone:hover {
            border-color: rgba(245,158,11,.5);
            background: rgba(245,158,11,.05);
        }
        .step-done  { background:rgba(34,197,94,.15);  color:#86efac; border:1px solid rgba(34,197,94,.3); }
        .step-active{ background:linear-gradient(135deg,#f59e0b,#ef4444); color:#fff; }
        .step-idle  { background:rgba(255,255,255,.06); color:rgba(255,255,255,.35); border:1px solid rgba(255,255,255,.08); }
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

            {{-- ── Header ── --}}
            <div class="text-center reveal">
                <div class="text-5xl mb-3">💳</div>
                <h1 class="text-2xl font-black text-white mb-1">Selesaikan Pembayaran</h1>
                <p class="text-sm" style="color:rgba(255,255,255,.45)">
                    {{ $order->bride_name }}{{ $order->groom_name ? ' &amp; ' . $order->groom_name : '' }}
                </p>
            </div>

            {{-- ── Langkah ── --}}
            <div class="flex items-center justify-center gap-2 text-xs font-semibold reveal">
                <span class="px-3 py-1.5 rounded-full step-done">✅ 1. Isi Data</span>
                <span style="color:rgba(255,255,255,.25)">→</span>
                <span class="px-3 py-1.5 rounded-full step-active">2. Bayar QRIS</span>
                <span style="color:rgba(255,255,255,.25)">→</span>
                <span class="px-3 py-1.5 rounded-full step-idle">3. Selesai</span>
            </div>

            {{-- ── Paket yang dipesan ── --}}
            @php
                $pkgBorder = match($order->package) { 'premium' => 'border-amber-400/30', 'vip' => 'border-purple-400/30', default => 'border-blue-400/30' };
                $pkgText   = match($order->package) { 'premium' => 'text-amber-400', 'vip' => 'text-purple-400', default => 'text-blue-400' };
            @endphp
            <div class="pay-card px-5 py-4 flex items-center justify-between gap-3 reveal {{ $pkgBorder }}">
                <div>
                    <div class="text-xs font-semibold mb-0.5 {{ $pkgText }}">
                        @if($order->isRenewal()) Perpanjang Masa Aktif @else Paket yang Anda pesan @endif
                    </div>
                    <div class="font-bold text-lg text-white">{{ $order->packageLabel() }}</div>
                    @if($order->isRenewal())
                        <div class="text-xs text-amber-400/70 mt-0.5">
                            ✓ Masa aktif diperpanjang {{ $order->renewal_days }} hari setelah konfirmasi admin
                        </div>
                    @elseif($order->package === 'vip')
                        <div class="text-xs text-purple-400/70 mt-0.5">♛ Semua fitur termasuk — 90 hari aktif</div>
                    @elseif($order->package === 'premium')
                        <div class="text-xs text-amber-400/70 mt-0.5">✓ Galeri foto &amp; musik latar termasuk</div>
                    @else
                        <div class="text-xs text-blue-400/70 mt-0.5">✓ Maks 100 tamu · tracking · import Excel</div>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <div class="text-2xl font-black {{ $pkgText }}">
                        {{ $order->packagePrice() }}
                    </div>
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,.3)">Total pembayaran</div>
                </div>
            </div>

            {{-- ── Countdown Timer ── --}}
            @php $secs = $order->secondsRemaining(); @endphp
            <div id="timer-box"
                 class="pay-card border-amber-400/25 px-5 py-4 text-center reveal"
                 data-seconds="{{ $secs }}">
                <p class="text-xs font-semibold text-amber-400 mb-1">⏱ Selesaikan pembayaran dalam:</p>
                <p class="text-3xl font-mono font-black text-amber-400" id="countdown-display">
                    {{ gmdate('i:s', $secs) }}
                </p>
                <p class="text-xs mt-1" style="color:rgba(255,255,255,.35)">Pesanan otomatis dibatalkan jika waktu habis.</p>
            </div>

            {{-- ── QRIS Card ── --}}
            <div class="pay-card p-6 text-center reveal">
                <p class="text-sm font-semibold text-white/70 mb-4">Scan QRIS berikut untuk membayar:</p>

                @if($qrisUrl)
                    <img src="{{ $qrisUrl }}" alt="QRIS Payment"
                         class="mx-auto max-w-[260px] w-full rounded-xl border border-white/10 shadow-lg">
                @else
                    <div class="mx-auto max-w-[260px] w-full h-64 rounded-xl border-2 border-dashed border-white/15 flex flex-col items-center justify-center gap-2"
                         style="color:rgba(255,255,255,.3)">
                        <span class="text-4xl">🖼️</span>
                        <p class="text-sm">Gambar QRIS belum diset.</p>
                        <p class="text-xs">Upload via halaman admin pesanan.</p>
                    </div>
                @endif

                <p class="text-xs mt-4" style="color:rgba(255,255,255,.3)">
                    Gunakan aplikasi perbankan atau e-wallet apapun yang mendukung QRIS.
                </p>
            </div>

            {{-- ── Upload Bukti Bayar ── --}}
            <div class="pay-card p-6 reveal">
                <h2 class="font-bold text-white mb-1">Upload Bukti Pembayaran</h2>
                <p class="text-sm mb-5" style="color:rgba(255,255,255,.4)">Setelah transfer, upload screenshot/foto bukti bayar di bawah ini.</p>

                @if($errors->any())
                    <div class="ti-alert-error mb-4">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('orders.proof', $token) }}" method="POST" enctype="multipart/form-data"
                      id="proof-form">
                    @csrf

                    {{-- File drop area --}}
                    <label for="proof-input" class="drop-zone block w-full p-6 text-center" id="drop-label">
                        <span class="text-3xl block mb-2">📎</span>
                        <span class="text-sm font-medium" style="color:rgba(255,255,255,.6)" id="file-name-label">
                            Klik atau seret file ke sini
                        </span>
                        <span class="block text-xs mt-1" style="color:rgba(255,255,255,.3)">JPG, PNG, atau PDF · maks 5 MB</span>
                        <input type="file" name="proof" id="proof-input" accept=".jpg,.jpeg,.png,.pdf"
                               class="sr-only" required>
                    </label>

                    {{-- Preview gambar --}}
                    <div id="img-preview" class="hidden mt-4">
                        <img id="preview-img" src="" alt="Preview"
                             class="mx-auto max-h-40 rounded-xl border border-white/10">
                    </div>

                    <button type="submit" id="submit-btn"
                            class="btn-glow mt-5 w-full py-3 rounded-xl font-bold text-sm disabled:opacity-50">
                        Kirim Bukti Pembayaran →
                    </button>
                </form>
            </div>

            {{-- ── WA admin ── --}}
            <p class="text-center text-xs reveal" style="color:rgba(255,255,255,.3)">
                Ada kendala?
                <a href="{{ $order->paymentWhatsappLink($adminPhone) }}" target="_blank"
                   class="text-green-400 hover:text-green-300 transition">Chat admin via WhatsApp</a>
            </p>

        </div>
    </div>

    <script>
        // ── Countdown timer ──────────────────────────────
        (function () {
            const box     = document.getElementById('timer-box');
            const display = document.getElementById('countdown-display');
            let remaining = parseInt(box.dataset.seconds, 10);

            function update() {
                if (remaining <= 0) {
                    display.textContent = '00:00';
                    box.style.borderColor = 'rgba(239,68,68,.4)';
                    display.style.color   = '#f87171';
                    setTimeout(() => location.reload(), 1500);
                    return;
                }
                const m = String(Math.floor(remaining / 60)).padStart(2, '0');
                const s = String(remaining % 60).padStart(2, '0');
                display.textContent = m + ':' + s;
                remaining--;

                // Warna merah jika < 2 menit
                if (remaining < 120) {
                    box.style.borderColor = 'rgba(239,68,68,.4)';
                    display.style.color   = '#f87171';
                }
                setTimeout(update, 1000);
            }
            update();
        })();

        // ── File preview ─────────────────────────────────
        const input      = document.getElementById('proof-input');
        const label      = document.getElementById('file-name-label');
        const preview    = document.getElementById('img-preview');
        const previewImg = document.getElementById('preview-img');

        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;
            label.textContent = file.name;
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => { previewImg.src = e.target.result; preview.classList.remove('hidden'); };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        });

        document.getElementById('proof-form').addEventListener('submit', () => {
            const btn = document.getElementById('submit-btn');
            btn.disabled    = true;
            btn.textContent = 'Mengirim...';
        });
    </script>
</body>
</html>
