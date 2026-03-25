<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if($isBirthday)Pesan Undangan Ulang Tahun &mdash; {{ $templateInfo['label'] }}
        @elseif($isGreeting)Pesan Greeting Card &mdash; {{ $templateInfo['label'] }}
        @else Pesan Undangan Pernikahan &mdash; {{ $templateInfo['label'] }}
        @endif
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/css/tretaninvite.css">
    <script src="/js/tretaninvite.js"></script>
    <style>
        /* ─── orders/create specific ─── */
        .form-box {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
            border-radius: 24px;
        }
        .pkg-radio {
            cursor: pointer; border-radius: 16px; border: 2px solid rgba(255,255,255,.08);
            padding: 18px; transition: all .2s;
            background: rgba(255,255,255,.03);
            display: flex; flex-direction: column; gap: 6px;
        }
        .pkg-radio:hover { border-color: rgba(255,255,255,.2); background: rgba(255,255,255,.06); }
        .pkg-radio.selected-basic { border-color: rgba(96,165,250,.6); background: rgba(59,130,246,.1); box-shadow: 0 0 20px rgba(59,130,246,.15); }
        .pkg-radio.selected-premium { border-color: rgba(245,158,11,.6); background: rgba(245,158,11,.08); box-shadow: 0 0 20px rgba(245,158,11,.15); }
        .pkg-radio.selected-vip     { border-color: rgba(168,85,247,.6);  background: rgba(168,85,247,.08); box-shadow: 0 0 20px rgba(168,85,247,.2); }
        .pkg-radio.selected-vip { border-color: rgba(168,85,247,.6); background: rgba(168,85,247,.08); box-shadow: 0 0 20px rgba(168,85,247,.2); }
        .field-row { display: grid; gap: 16px; }
        @media (min-width: 640px) { .field-row-2 { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
@php
    $defaultPkg = old('package', $defaultPkg);
    $createUrl  = route('orders.create');
@endphp
<body class="text-white antialiased"
      x-data="{ pkg: '{{ $defaultPkg }}' }"
      x-init="TI.initPage({ starsId:'ti-stars', tiltSel:false })">

    <div id="ti-stars" style="position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;"></div>

    {{-- ════ NAVBAR ════ --}}
    <nav class="navbar px-6 py-4">
        <div class="max-w-3xl mx-auto flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2 select-none">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-lg font-black shadow-lg">T</div>
                <span class="font-bold text-lg tracking-tight"><span class="text-amber-400">Tretan</span>Invite</span>
            </a>
            <a href="{{ route('packages.index') }}"
               class="text-sm font-medium text-white/55 hover:text-white transition flex items-center gap-1">
                &#x2190; Pilihan paket
            </a>
        </div>
    </nav>

    {{-- ════ CONTENT ════ --}}
    <div class="page-bg pt-24 pb-20 px-4 sm:px-6">
        <div class="inner max-w-3xl mx-auto">

            {{-- ── Header ── --}}
            <div class="mb-8 reveal">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-purple-400/30 bg-purple-400/10 text-purple-300 text-xs font-semibold mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                    @if($isBirthday) Undangan Ulang Tahun
                    @elseif($isGreeting) Greeting Card
                    @else Undangan Pernikahan
                    @endif
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-2">
                    Isi Data Pesanan
                </h1>
                <div class="flex flex-wrap items-center gap-3 text-sm text-white/50">
                    <span>Template dipilih:</span>
                    <span class="px-3 py-1 rounded-full font-semibold text-sm"
                          style="background:rgba(167,139,250,.15);color:#c4b5fd;border:1px solid rgba(167,139,250,.25);">
                        {{ $templateInfo['icon'] ?? '' }} {{ $templateInfo['label'] }}
                    </span>
                    <a href="{{ route('preview.template', $template) }}" target="_blank"
                       class="text-amber-400 hover:text-amber-300 transition font-medium">Lihat contoh &#x2197;</a>
                </div>
            </div>

            {{-- ── Renewal banner ── --}}
            @if($isRenewal)
            <div class="mb-6 flex items-start gap-3 px-4 py-4 rounded-xl reveal"
                 style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.35);">
                <span class="text-2xl mt-0.5">🔄</span>
                <div>
                    <div class="font-bold text-amber-300 text-sm mb-0.5">Perpanjang Undangan</div>
                    <div class="text-xs" style="color:rgba(253,230,138,.75);">Data Anda sudah diisi otomatis dari pesanan sebelumnya. Pastikan semua informasi masih benar, lalu kirim pesanan baru untuk memperpanjang masa aktif undangan.</div>
                </div>
            </div>
            @endif

            {{-- ── Flash messages ── --}}
            @if(session('error'))
            <div class="ti-alert-error flex items-start gap-2 mb-6 reveal">
                <span>&#x26A0;</span> <span>{{ session('error') }}</span>
            </div>
            @endif
            @if($errors->any())
            <div class="ti-alert-error mb-6 reveal">
                <ul class="space-y-1">
                    @foreach($errors->all() as $e)<li>&#x2022; {{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- ════ FORM ════ --}}
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="template" value="{{ $template }}">
                @if($renewToken)
                <input type="hidden" name="renew_token" value="{{ $renewToken }}">
                @endif

                {{-- ── Template terpilih (display only) ── --}}
                <div class="form-box p-6 reveal">
                    <p class="ti-label text-xs mb-3">&#x1F3A8; Desain / Template Dipilih</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:14px;">
                            {{-- Thumbnail kecil --}}
                            @if(file_exists(public_path('images/templates/' . $template . '.svg')))
                            <img src="{{ asset('images/templates/' . $template . '.svg') }}" alt="{{ $templateInfo['label'] }}"
                                 style="width:64px;height:64px;object-fit:cover;border-radius:10px;border:1px solid rgba(255,255,255,.12);flex-shrink:0;">
                            @else
                            <div style="width:64px;height:64px;border-radius:10px;background:rgba(167,139,250,.15);border:1px solid rgba(167,139,250,.25);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                                {{ $templateInfo['icon'] ?? '🎨' }}
                            </div>
                            @endif
                            <div>
                                <div style="font-weight:700;font-size:1rem;color:#fff;margin-bottom:3px;">
                                    {{ $templateInfo['icon'] ?? '' }} {{ $templateInfo['label'] }}
                                </div>
                                <div style="font-size:.8rem;color:rgba(255,255,255,.45);">{{ $templateInfo['description'] ?? '' }}</div>
                                @if(!empty($templateInfo['vip_only']))
                                <div style="margin-top:5px;display:inline-flex;align-items:center;gap:4px;font-size:.7rem;font-weight:700;letter-spacing:.04em;padding:2px 8px;border-radius:99px;background:rgba(168,85,247,.2);color:#c4b5fd;border:1px solid rgba(168,85,247,.3);">&#x265B; VIP</div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('packages.index') }}"
                           style="font-size:.8rem;font-weight:600;color:rgba(167,139,250,.8);border:1px solid rgba(167,139,250,.25);padding:6px 14px;border-radius:99px;white-space:nowrap;text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.color='#c4b5fd';this.style.borderColor='rgba(167,139,250,.5)'"
                           onmouseout="this.style.color='rgba(167,139,250,.8)';this.style.borderColor='rgba(167,139,250,.25)'">
                            &#x21BA; Ganti Desain
                        </a>
                    </div>
                </div>

                {{-- ── Package selector ── --}}
                <div class="form-box p-6 reveal">
                    <p class="ti-label text-xs mb-4">&#x1F4E6; Pilih Paket <span class="text-rose-400">*</span></p>
                    @if($isVipTemplate)
                <div class="mb-3 px-4 py-2.5 rounded-xl text-sm flex items-center gap-2" style="background:rgba(124,58,237,.12);border:1px solid rgba(168,85,247,.25);color:rgba(192,132,252,.9);">
                    <span style="font-size:1.1em;">&#x265B;</span>
                    <span>Template <strong>VIP Royal</strong> hanya tersedia dengan paket VIP.</span>
                </div>
                @endif
                @php
                    $xShowVip = $isVipTemplate ? 'true' : ($isBirthday || $isGreeting ? 'false' : "showVip || pkg === 'vip'");
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ showVip: new URLSearchParams(location.search).get('pkg')==='vip' }">
                        {{-- Basic --}}
                        <label :class="pkg === 'basic' ? 'selected-basic' : ''"
                               @if($isVipTemplate) style="display:none" @endif
                               class="pkg-radio">
                            <input type="radio" name="package" value="basic" x-model="pkg" class="sr-only">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-blue-400">&#x1F499; Basic</span>
                                <span class="text-xl font-black text-white">{{ ($isBirthday || $isGreeting) ? '39' : '59' }}<span class="text-sm font-medium text-white/50">rb</span></span>
                            </div>
                            <div class="text-xs space-y-1.5 mt-1" style="color:rgba(255,255,255,.5)">
                                @if($isGreeting)
                                <div>&#x2714; 30 hari aktif</div>
                                <div>&#x2714; Kartu ucapan digital personal</div>
                                <div>&#x2714; Link unik per penerima</div>
                                <div>&#x2714; Animasi &amp; efek kartu</div>
                                <div style="color:rgba(255,255,255,.25)">&#x2718; Foto &amp; musik dalam kartu</div>
                                @elseif($isBirthday)
                                <div>&#x2714; 14 hari aktif</div>
                                <div>&#x2714; Maks 100 tamu</div>
                                <div>&#x2714; RSVP &amp; tracking buka undangan</div>
                                <div>&#x2714; Import tamu dari Excel</div>
                                <div style="color:rgba(255,255,255,.25)">&#x2718; Galeri foto &amp; musik latar</div>
                                @else
                                <div>&#x2714; 14 hari aktif</div>
                                <div>&#x2714; Maks 100 tamu</div>
                                <div>&#x2714; RSVP &amp; tracking buka undangan</div>
                                <div>&#x2714; Import tamu dari Excel</div>
                                <div style="color:rgba(255,255,255,.25)">&#x2718; Galeri foto &amp; musik latar</div>
                                @endif
                            </div>
                        </label>
                        {{-- Premium --}}
                        <label :class="pkg === 'premium' ? 'selected-premium' : ''"
                               @if($isVipTemplate) style="display:none" @endif
                               class="pkg-radio relative">
                            <input type="radio" name="package" value="premium" x-model="pkg" class="sr-only">
                            <span class="absolute -top-2.5 right-3 text-xs font-bold px-2.5 py-0.5 rounded-full"
                                  style="background:linear-gradient(135deg,#f59e0b,#ef4444);color:#fff;box-shadow:0 2px 8px rgba(245,158,11,.5);">
                                &#x1F525; Terlengkap
                            </span>
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-amber-400">&#x2B50; Premium</span>
                                <span class="text-xl font-black text-white">{{ ($isBirthday || $isGreeting) ? '49' : '89' }}<span class="text-sm font-medium text-white/50">rb</span></span>
                            </div>
                            <div class="text-xs space-y-1.5 mt-1" style="color:rgba(255,255,255,.5)">
                                @if($isGreeting)
                                <div>&#x2714; 90 hari aktif</div>
                                <div>&#x2714; Kartu ucapan digital personal</div>
                                <div>&#x2714; Link unik per penerima</div>
                                <div>&#x2714; Animasi &amp; efek kartu</div>
                                <div class="font-semibold text-amber-400">&#x2714; Foto &amp; musik dalam kartu</div>
                                <div class="font-semibold text-amber-400">&#x2714; Galeri foto kenangan</div>
                                @elseif($isBirthday)
                                <div>&#x2714; 30 hari aktif</div>
                                <div>&#x2714; Tamu tak terbatas</div>
                                <div>&#x2714; RSVP &amp; tracking buka undangan</div>
                                <div>&#x2714; Import tamu dari Excel</div>
                                <div class="font-semibold text-amber-400">&#x2714; Galeri foto &amp; musik latar</div>
                                @else
                                <div>&#x2714; 30 hari aktif</div>
                                <div>&#x2714; Tamu tak terbatas</div>
                                <div>&#x2714; RSVP &amp; tracking buka undangan</div>
                                <div>&#x2714; Import tamu dari Excel</div>
                                <div class="font-semibold text-amber-400">&#x2714; Galeri foto &amp; musik latar</div>
                                @endif
                            </div>
                        </label>

                        {{-- VIP --}}
                        <label :class="pkg === 'vip' ? 'selected-vip' : ''"
                               x-show="{{ $xShowVip }}"
                               class="pkg-radio relative sm:col-span-2">
                            <input type="radio" name="package" value="vip" x-model="pkg" class="sr-only">
                            <span class="absolute -top-2.5 right-3 text-xs font-bold px-2.5 py-0.5 rounded-full"
                                  style="background:linear-gradient(135deg,#7c3aed,#db2777);color:#fff;box-shadow:0 2px 8px rgba(124,58,237,.5);">
                                &#x1F451; Eksklusif
                            </span>
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold" style="color:#c084fc;">&#x1F48E; VIP</span>
                                <span class="text-xl font-black text-white">199<span class="text-sm font-medium text-white/50">rb</span></span>
                            </div>
                            <div class="grid grid-cols-2 gap-x-6 text-xs mt-1 space-y-0" style="color:rgba(255,255,255,.55)">
                                <div class="space-y-1.5">
                                    <div>&#x2714; 90 hari aktif</div>
                                    <div>&#x2714; Tamu tak terbatas</div>
                                    <div>&#x2714; Foto &amp; lagu favorit di undangan</div>
                                    <div>&#x2714; Video prewedding dalam undangan</div>
                                    <div>&#x2714; Foto sampul pilihan sendiri</div>
                                    <div>&#x2714; Tamu bisa titip ucapan</div>
                                </div>
                                <div class="space-y-1.5">
                                    <div>&#x2714; Scan tamu tanpa daftar ulang</div>
                                    <div>&#x2714; Pantau kehadiran secara langsung</div>
                                    <div>&#x2714; Notif masuk tiap ada konfirmasi</div>
                                    <div>&#x2714; Undangan bisa dikunci password</div>
                                    <div>&#x2714; Bisa lebih dari satu sesi acara</div>
                                    <div>&#x2714; Download rekap tamu lengkap</div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('package')
                        <p class="text-xs text-rose-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ── Guest data ── --}}
                <div class="form-box p-6 reveal">
                    @if($isBirthday)
                    {{-- ── ULANG TAHUN ── --}}
                    <input type="hidden" name="groom_name" value="">
                    <p class="ti-label text-xs mb-4">&#x1F382; Data Yang Berulang Tahun</p>
                    <div class="field-row field-row-2 mb-4">
                        <div>
                            <label class="ti-label">Nama Yang Berulang Tahun <span class="text-rose-400">*</span></label>
                            <input type="text" name="bride_name" value="{{ old('bride_name', $prefill['bride_name'] ?? '') }}"
                                   placeholder="cth: Aisyah Azzahra" class="ti-input" required>
                        </div>
                        <div>
                            <label class="ti-label">Usia (tahun)</label>
                            <input type="number" name="bride_age" value="{{ old('bride_age') }}"
                                   placeholder="cth: 7" class="ti-input" min="1" max="150">
                        </div>
                    </div>
                    <div class="field-row field-row-2">
                        <div>
                            <label class="ti-label">Tanggal Pesta</label>
                            <input type="date" name="event_date" value="{{ old('event_date', $prefill['event_date'] ?? '') }}" class="ti-input">
                        </div>
                        <div>
                            <label class="ti-label">Lokasi Pesta</label>
                            <input type="text" name="location" value="{{ old('location', $prefill['location'] ?? '') }}"
                                   placeholder="cth: Taman Ancol, Jakarta" class="ti-input">
                        </div>
                    </div>

                    @elseif($isGreeting)
                    {{-- ── GREETING CARD ── --}}
                    <input type="hidden" name="groom_name" value="">
                    <p class="ti-label text-xs mb-4">&#x1F4E9; Data Penerima Ucapan</p>
                    <div class="mb-4">
                        <label class="ti-label">Nama Penerima <span class="text-rose-400">*</span></label>
                        <input type="text" name="bride_name" value="{{ old('bride_name', $prefill['bride_name'] ?? '') }}"
                               placeholder="cth: Budi Santoso" class="ti-input" required>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,.3)">Nama orang yang akan menerima greeting card ini</p>
                    </div>
                    <div class="field-row field-row-2">
                        <div>
                            <label class="ti-label">Tanggal Acara</label>
                            <input type="date" name="event_date" value="{{ old('event_date', $prefill['event_date'] ?? '') }}" class="ti-input">
                        </div>
                        <div>
                            <label class="ti-label">Lokasi (opsional)</label>
                            <input type="text" name="location" value="{{ old('location', $prefill['location'] ?? '') }}"
                                   placeholder="cth: Jakarta" class="ti-input">
                        </div>
                    </div>

                    @else
                    {{-- ── PERNIKAHAN ── --}}
                    <p class="ti-label text-xs mb-4">&#x1F492; Data Pasangan</p>
                    <div class="field-row field-row-2 mb-4">
                        <div>
                            <label class="ti-label">Nama Pengantin Wanita <span class="text-rose-400">*</span></label>
                            <input type="text" name="bride_name" value="{{ old('bride_name', $prefill['bride_name'] ?? '') }}"
                                   placeholder="cth: Siti Rahayu" class="ti-input" required>
                        </div>
                        <div>
                            <label class="ti-label">Nama Pengantin Pria <span class="text-rose-400">*</span></label>
                            <input type="text" name="groom_name" value="{{ old('groom_name', $prefill['groom_name'] ?? '') }}"
                                   placeholder="cth: Ahmad Farhan" class="ti-input" required>
                        </div>
                    </div>
                    <div class="field-row field-row-2">
                        <div>
                            <label class="ti-label">Tanggal Acara</label>
                            <input type="date" name="event_date" value="{{ old('event_date', $prefill['event_date'] ?? '') }}" class="ti-input">
                        </div>
                        <div>
                            <label class="ti-label">Lokasi Acara</label>
                            <input type="text" name="location" value="{{ old('location', $prefill['location'] ?? '') }}"
                                   placeholder="cth: Gedung Graha Sana, Jakarta" class="ti-input">
                        </div>
                    </div>
                    @endif
                </div>

                {{-- ── Customer data ── --}}
                <div class="form-box p-6 reveal">
                    <p class="ti-label text-xs mb-4">&#x1F4CB; Data Pemesan</p>
                    <div class="field-row field-row-2 mb-4">
                        <div>
                            <label class="ti-label">Nama Anda <span class="text-rose-400">*</span></label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $prefill['customer_name'] ?? '') }}"
                                   placeholder="Nama lengkap Anda" class="ti-input" required>
                        </div>
                        <div>
                            <label class="ti-label">No WhatsApp <span class="text-rose-400">*</span></label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone', $prefill['customer_phone'] ?? '') }}"
                                   placeholder="cth: 08123456789" class="ti-input" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="ti-label">Email <span style="color:rgba(255,255,255,.3);font-weight:400;">(opsional — untuk notifikasi expiry undangan)</span></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $prefill['customer_email'] ?? '') }}"
                               placeholder="cth: nama@gmail.com" class="ti-input">
                        <p class="text-xs mt-1.5" style="color:rgba(255,255,255,.25);">&#x1F514; Kami akan kirim pengingat 7 hari &amp; 1 hari sebelum masa aktif undangan habis.</p>
                    </div>
                    <div>
                        <label class="ti-label">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" class="ti-input"
                                  placeholder="@if($isBirthday)Tema pesta, warna favorit, foto yang ingin digunakan, atau permintaan khusus lainnya...@elseif($isGreeting)Pesan khusus, tema, atau permintaan lainnya...@else Foto yang ingin digunakan, warna tema, atau permintaan khusus lainnya...@endif">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- ── Submit ── --}}
                <div class="reveal">
                    <button type="submit"
                            class="btn-glow w-full py-4 rounded-2xl font-bold text-lg text-center">
                        Kirim Pesanan &#x2192;
                    </button>
                    <p class="text-center text-xs mt-3" style="color:rgba(255,255,255,.3)">
                        Setelah mengirim, admin kami akan menghubungi Anda via WhatsApp untuk konfirmasi pesanan.
                    </p>
                </div>

            </form>
        </div>
    </div>

    {{-- ════ FOOTER ════ --}}
    <footer style="background:#070514;border-top:1px solid rgba(255,255,255,.05);" class="py-8 px-6 text-center relative z-10">
        <div class="flex items-center justify-center gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-sm font-black">T</div>
            <span class="font-bold text-white/70 text-sm"><span class="text-amber-400">Tretan</span>Invite</span>
        </div>
        <p class="text-white/20 text-xs">&copy; {{ date('Y') }} TretanInvite. Made with &#x1F49B; untuk keluarga Indonesia.</p>
    </footer>

</body>
</html>