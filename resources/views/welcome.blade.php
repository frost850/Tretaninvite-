<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TretanInvite – Undangan Digital</title>
    <meta name="description" content="TretanInvite – Undangan digital modern kanggo tretan sejati. Sebar kabungaan ka sakabbi' dulur ben kerabat tercinta.">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/css/tretaninvite.css">
    <style>
        /* ─── Welcome-page specific ─── */
        .card-3d {
            transform-style: preserve-3d;
            transition: transform .06s linear;
            will-change: transform;
        }
        .wave-divider svg { display: block; }
        .feat-bg-purple { background: linear-gradient(140deg,rgba(124,58,237,.15),rgba(88,28,135,.2)); }
        .feat-bg-blue    { background: linear-gradient(140deg,rgba(37,99,235,.15),rgba(29,78,216,.2)); }
        .feat-bg-green   { background: linear-gradient(140deg,rgba(22,163,74,.15),rgba(20,83,45,.2)); }
        .feat-bg-rose    { background: linear-gradient(140deg,rgba(225,29,72,.15),rgba(159,18,57,.2)); }
        .feat-bg-amber   { background: linear-gradient(140deg,rgba(217,119,6,.15),rgba(146,64,14,.2)); }
        .feat-bg-pink    { background: linear-gradient(140deg,rgba(219,39,119,.15),rgba(157,23,77,.2)); }
        .step-bg-amber  { background: #1a140020; }
        .step-bg-purple { background: #120e1a20; }
        .step-bg-blue   { background: #0e122020; }
        .step-bg-green  { background: #0a1a1020; }
    </style>
</head>
<body class="antialiased text-white" x-data="heroApp()" x-init="init()">

    {{-- ════ NAVBAR ════ --}}
    <nav class="navbar px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 select-none">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-lg font-black shadow-lg">T</div>
                <span class="font-bold text-lg tracking-tight"><span class="text-amber-400">Tretan</span>Invite</span>
            </a>
            <div class="hidden sm:flex items-center gap-6 text-sm text-white/70">
                <a href="#fitur" class="hover:text-white transition">Fitur</a>
                <a href="#paket" class="hover:text-white transition">Paket</a>
                <a href="#cara" class="hover:text-white transition">Cara Kerja</a>
            </div>
            <a href="{{ route('packages.index') }}"
               class="text-sm font-semibold px-4 py-2 rounded-full bg-amber-500 hover:bg-amber-400 text-black transition shadow-lg">
                Mulai Sekarang
            </a>
        </div>
    </nav>

    {{-- ════ HERO ════ --}}
    <section class="bg-hero min-h-screen flex flex-col items-center justify-center relative overflow-hidden pt-20">

        {{-- Stars --}}
        <div class="stars" id="stars-container"></div>

        {{-- Floating invitation cards (decorative) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            {{-- Card left --}}
            <div class="card-float absolute left-[5%] top-[20%] hidden lg:block" style="--dur:7s;--dl:0s;--r:-8deg;">
                <div class="w-44 rounded-2xl overflow-hidden shadow-2xl opacity-70 border border-white/10"
                     style="background:linear-gradient(160deg,#1a1040,#2d1b69);">
                    <div class="p-4">
                        <div class="text-xs text-purple-300 font-semibold mb-1">💍 Undangan Pernikahan</div>
                        <div class="text-sm font-bold text-white/90 leading-snug">Dewanata & Anny</div>
                        <div class="text-[10px] text-white/50 mt-1">30 Maret 2026 · Surabaya</div>
                        <div class="mt-3 h-[1px] bg-white/10"></div>
                        <div class="mt-3 flex gap-1">
                            <div class="h-1.5 flex-1 rounded-full bg-purple-500/60"></div>
                            <div class="h-1.5 flex-1 rounded-full bg-purple-500/30"></div>
                            <div class="h-1.5 w-6 rounded-full bg-purple-500/20"></div>
                        </div>
                        <div class="mt-4 text-center text-[10px] text-amber-300 font-semibold">Buka Undangan ✨</div>
                    </div>
                </div>
            </div>
            {{-- Card right --}}
            <div class="card-float absolute right-[6%] top-[15%] hidden lg:block" style="--dur:8s;--dl:1.5s;--r:6deg;">
                <div class="w-44 rounded-2xl overflow-hidden shadow-2xl opacity-70 border border-white/10"
                     style="background:linear-gradient(160deg,#1a103a,#3b1547);">
                    <div class="p-4">
                        <div class="text-xs text-pink-300 font-semibold mb-1">🎂 Ulang Tahun</div>
                        <div class="text-sm font-bold text-white/90 leading-snug">Happy 26th Anny Msy!</div>
                        <div class="text-[10px] text-white/50 mt-1">05 Maret 2026 · Pamekasan</div>
                        <div class="mt-3 h-[1px] bg-white/10"></div>
                        <div class="mt-3 grid grid-cols-3 gap-1">
                            <div class="h-8 rounded-lg bg-pink-500/20"></div>
                            <div class="h-8 rounded-lg bg-rose-500/20"></div>
                            <div class="h-8 rounded-lg bg-purple-500/20"></div>
                        </div>
                        <div class="mt-3 text-center text-[10px] text-pink-300 font-semibold">RSVP Sekarang 🎉</div>
                    </div>
                </div>
            </div>
            {{-- Card bottom left --}}
            <div class="card-float absolute left-[12%] bottom-[20%] hidden xl:block" style="--dur:9s;--dl:3s;--r:5deg;">
                <div class="w-36 rounded-2xl overflow-hidden shadow-2xl opacity-50 border border-white/10"
                     style="background:linear-gradient(160deg,#0f2027,#1a3a2a);">
                    <div class="p-3">
                        <div class="text-[10px] text-emerald-300 font-semibold mb-1">🌿 Template Garden</div>
                        <div class="h-14 rounded-lg bg-emerald-900/60 flex items-center justify-center text-2xl">🌸</div>
                        <div class="mt-2 text-[10px] text-white/50 text-center">12 template pilihan</div>
                    </div>
                </div>
            </div>
            {{-- Card bottom right --}}
            <div class="card-float absolute right-[10%] bottom-[18%] hidden xl:block" style="--dur:6.5s;--dl:2s;--r:-4deg;">
                <div class="w-36 rounded-2xl overflow-hidden shadow-2xl opacity-50 border border-white/10"
                     style="background:linear-gradient(160deg,#1a0f20,#2d1b35);">
                    <div class="p-3">
                        <div class="text-[10px] text-amber-300 font-semibold mb-1">📊 Tracking Tamu</div>
                        <div class="mt-2 space-y-1.5">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                <div class="h-1.5 flex-1 rounded-full bg-green-400/60"></div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                                <div class="h-1.5 w-2/3 rounded-full bg-amber-400/60"></div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full bg-purple-400"></div>
                                <div class="h-1.5 w-1/3 rounded-full bg-purple-400/60"></div>
                            </div>
                        </div>
                        <div class="mt-2 text-[10px] text-white/50 text-center">Real-time RSVP</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hero content --}}
        <div class="relative z-10 text-center px-6 max-w-4xl mx-auto" id="hero-content">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-amber-400/30 bg-amber-400/10 backdrop-blur-sm text-amber-300 text-sm font-medium mb-8 reveal" style="transition-delay:.1s">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                Undangan Digital Terpercaya
            </div>

            {{-- Brand name --}}
            <h1 class="reveal font-black leading-[1.05] mb-2" style="transition-delay:.2s;font-family:'Plus Jakarta Sans',sans-serif;">
                <span class="block text-5xl sm:text-7xl lg:text-8xl text-white">Tretan</span>
                <span class="block text-5xl sm:text-7xl lg:text-8xl"
                      style="background:linear-gradient(135deg,#f59e0b,#ef4444,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                    Invite
                </span>
            </h1>

            {{-- Tagline --}}
            <p class="reveal text-lg sm:text-xl text-white/60 font-medium mt-4 mb-2" style="transition-delay:.3s">
                Undangan Digital <em>kanggo</em> Tretan Sejati
            </p>
            <p class="reveal text-base sm:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed mb-10" style="transition-delay:.4s">
                Sebar kabhunga an sakabbhina' <span class="text-amber-300 font-semibold">tretan</span>,
                dulur, ben kerabat tercinta.<br class="hidden sm:block">
                Tak perlu cetak-cetak, tak perlu ribet nganter.<br class="hidden sm:block">
                <span class="text-white">Cukup satu link</span>, undangan langsung sampe ka kabeh —
                <span class="text-purple-300 font-semibold">modern, praktis, ben tetep rasa kekeluargaan.</span>
            </p>

            {{-- CTA Buttons --}}
            <div class="reveal flex flex-col sm:flex-row gap-4 justify-center items-center mb-14" style="transition-delay:.5s">
                <a href="{{ route('packages.index') }}"
                   class="btn-glow relative inline-flex items-center gap-3 px-8 py-4 rounded-2xl text-white font-bold text-lg z-10">
                    <span class="text-xl">💌</span>
                    Pilih Paket Undangan Sekarang
                    <span class="text-base">→</span>
                </a>
                <a href="{{ route('packages.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-4 rounded-2xl border border-white/20 text-white/80 font-semibold hover:bg-white/10 hover:border-white/40 transition text-sm backdrop-blur-sm">
                    👁 Lihat Contoh
                </a>
            </div>

            {{-- Scroll indicator --}}
            <div class="reveal flex flex-col items-center gap-2 opacity-50" style="transition-delay:.7s">
                <div class="scroll-mouse"><div class="scroll-dot"></div></div>
                <span class="text-xs text-white/50">scroll</span>
            </div>
        </div>
    </section>

    {{-- ════ TRUST STRIP ════ --}}
    <div style="background:#0d0a1e;border-top:1px solid rgba(255,255,255,.05);border-bottom:1px solid rgba(255,255,255,.05);">
        <div class="max-w-5xl mx-auto py-5 px-6 overflow-hidden">
            <div class="marquee-inner whitespace-nowrap">
                @foreach(['💒 Pernikahan','🎂 Ulang Tahun','🌸 Template Floral','🌿 Template Garden','💍 Template Classic','⭐ Template Luxury','📊 RSVP Real-time','🔗 Link Personal','📥 Import Tamu','📤 Export Data','🎵 Musik Pengiring','📸 Galeri Foto','🆓 Coba Gratis','💙 Basic Rp59rb','⭐ Premium Rp89rb'] as $item)
                <span class="text-white/40 text-sm font-medium">{{ $item }}</span>
                <span class="text-white/15 mx-4">·</span>
                @endforeach
                @foreach(['💒 Pernikahan','🎂 Ulang Tahun','🌸 Template Floral','🌿 Template Garden','💍 Template Classic','⭐ Template Luxury','📊 RSVP Real-time','🔗 Link Personal','📥 Import Tamu','📤 Export Data','🎵 Musik Pengiring','📸 Galeri Foto','🆓 Coba Gratis','💙 Basic Rp59rb','⭐ Premium Rp89rb'] as $item)
                <span class="text-white/40 text-sm font-medium">{{ $item }}</span>
                <span class="text-white/15 mx-4">·</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ════ FEATURES ════ --}}
    <section id="fitur" style="background:linear-gradient(180deg,#0d0a1e 0%,#12091f 100%);" class="py-24 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16 reveal">
                <div class="text-amber-400 font-semibold text-sm uppercase tracking-widest mb-3">Kenapa TretanInvite?</div>
                <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight">
                    Semua yang Kamu Butuhkan<br>
                    <span style="background:linear-gradient(135deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Ada di Sini</span>
                </h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @php
                $features = [
                    ['🔗','Link Personal per Tamu','Setiap tamu dapat link unik – nama mereka langsung muncul di undangan.','feat-bg-purple','bg-purple-600'],
                    ['📊','Tracking Buka Undangan','Lihat siapa saja yang sudah buka undangan, kapan, dan berapa kali.','feat-bg-blue','bg-blue-600'],
                    ['✅','RSVP Real-time','Tamu bisa konfirmasi hadir langsung dari undangan. Datanya langsung masuk ke admin.','feat-bg-green','bg-green-600'],
                    ['📸','Galeri & Foto Pasangan','Upload galeri foto dan foto profil langsung dari dashboard.','feat-bg-rose','bg-rose-500'],
                    ['📥','Import Tamu via Excel','Upload ratusan nama tamu sekaligus. Support import massal dengan deteksi duplikat.','feat-bg-amber','bg-amber-500'],
                    ['🎵','Template Beragam','12+ pilihan template undangan pernikahan & ulang tahun yang elegan dan modern.','feat-bg-pink','bg-pink-500'],
                ];
                @endphp
                @foreach($features as $f)
                <div class="feat-card reveal {{ $f[3] }}">
                    <div class="icon-3d {{ $f[4] }}">{{ $f[0] }}</div>
                    <h3 class="text-white font-bold text-lg mb-2">{{ $f[1] }}</h3>
                    <p class="text-white/55 text-sm leading-relaxed">{{ $f[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════ HOW IT WORKS ════ --}}
    <section id="cara" style="background:#0a0818;" class="py-24 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16 reveal">
                <div class="text-purple-400 font-semibold text-sm uppercase tracking-widest mb-3">Cara Kerja</div>
                <h2 class="text-3xl sm:text-5xl font-black text-white">Mudah, Cepat, Langsung Jadi</h2>
            </div>
            <div class="relative">
                {{-- Connector line --}}
                <div class="absolute left-6 top-12 bottom-12 w-px bg-gradient-to-b from-amber-500 via-purple-500 to-pink-500 hidden sm:block ml-[calc(theme(spacing.6)-0.5px)]" style="left:44px"></div>
                <div class="space-y-8">
                    @php
                    $steps = [
                        ['1','Pilih Paket','Pilih paket sesuai kebutuhan – Trial gratis, Basic, atau Premium.','bg-amber-500','step-bg-amber'],
                        ['2','Isi Data Undangan','Hubungi admin via WhatsApp & isi data acara, foto, dan daftar tamu.','bg-purple-500','step-bg-purple'],
                        ['3','Admin Buatkan','Admin membuat undangan digital milikmu sesuai template pilihan.','bg-blue-500','step-bg-blue'],
                        ['4','Sebar ke Tretan','Bagikan link personal ke setiap tamu. Mereka langsung bisa RSVP!','bg-green-500','step-bg-green'],
                    ];
                    @endphp
                    @foreach($steps as $s)
                    <div class="reveal flex items-start gap-6 relative">
                        <div class="w-12 h-12 rounded-2xl {{ $s[3] }} text-white font-black text-xl flex items-center justify-center shrink-0 shadow-lg z-10 relative">{{ $s[0] }}</div>
                        <div class="flex-1 rounded-2xl p-5 border border-white/06 {{ $s[4] }}">
                            <h3 class="text-white font-bold text-lg mb-1">{{ $s[1] }}</h3>
                            <p class="text-white/55 text-sm leading-relaxed">{{ $s[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ════ PRICING ════ --}}
    <section id="paket" style="background:linear-gradient(180deg,#0a0818 0%,#0f0c29 100%);" class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16 reveal">
                <div class="text-green-400 font-semibold text-sm uppercase tracking-widest mb-3">Harga</div>
                <h2 class="text-3xl sm:text-5xl font-black text-white">Pilih yang Pas<br>
                    <span style="background:linear-gradient(135deg,#34d399,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Buat Tretan</span>
                </h2>
                <p class="text-white/50 mt-4">Semua paket sudah termasuk pembuatan undangan oleh admin.</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-6 items-end">

                {{-- Trial --}}
                <div class="pkg-card reveal p-7 border border-white/08"
                     style="background:rgba(255,255,255,.04);backdrop-filter:blur(12px);">
                    <div class="text-white/40 text-sm font-semibold mb-4">🆓 Trial</div>
                    <div class="text-4xl font-black text-white mb-1">Gratis</div>
                    <div class="text-white/40 text-xs mb-6">Coba 1 hari</div>
                    <ul class="space-y-3 text-sm text-white/60 mb-8">
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Maks 3 tamu</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> 1 template pilihan</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Link personal tamu</li>
                        <li class="flex items-center gap-2"><span class="text-white/20">✗</span> <span class="line-through">Galeri foto</span></li>
                        <li class="flex items-center gap-2"><span class="text-white/20">✗</span> <span class="line-through">Musik</span></li>
                    </ul>
                    <a href="{{ route('packages.index') }}"
                       class="block text-center py-3 rounded-xl border border-white/20 text-white/70 font-semibold hover:bg-white/10 transition text-sm">
                        Coba Gratis
                    </a>
                </div>

                {{-- Basic --}}
                <div class="pkg-card reveal p-7 border border-blue-500/30" style="transition-delay:.1s;
                     background:linear-gradient(160deg,rgba(59,130,246,.08),rgba(37,99,235,.12));backdrop-filter:blur(12px);">
                    <div class="text-blue-400 text-sm font-semibold mb-4">💙 Basic</div>
                    <div class="flex items-end gap-1 mb-1">
                        <span class="text-4xl font-black text-white">59</span>
                        <span class="text-white/60 text-base mb-1">rb</span>
                    </div>
                    <div class="text-white/40 text-xs mb-6">Aktif 14 hari</div>
                    <ul class="space-y-3 text-sm text-white/70 mb-8">
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Maks 100 tamu</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Semua template</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Link personal tamu</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Tracking & RSVP</li>
                        <li class="flex items-center gap-2"><span class="text-white/20">✗</span> <span class="line-through">Galeri & Musik</span></li>
                    </ul>
                    <a href="{{ route('packages.index') }}"
                       class="block text-center py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold transition text-sm">
                        Pilih Basic
                    </a>
                </div>

                {{-- Premium --}}
                <div class="pkg-card featured reveal p-7 relative overflow-hidden" style="transition-delay:.2s;
                     background:linear-gradient(160deg,rgba(245,158,11,.1),rgba(217,70,239,.1),rgba(99,102,241,.1));backdrop-filter:blur(12px);">
                    <div class="absolute top-4 right-4 text-xs font-bold px-3 py-1 rounded-full"
                         style="background:linear-gradient(135deg,#f59e0b,#ef4444);color:#fff;">
                        TERPOPULER
                    </div>
                    <div class="text-amber-400 text-sm font-semibold mb-4">⭐ Premium</div>
                    <div class="flex items-end gap-1 mb-1">
                        <span class="text-4xl font-black text-white">89</span>
                        <span class="text-white/60 text-base mb-1">rb</span>
                    </div>
                    <div class="text-white/40 text-xs mb-6">Aktif 30 hari</div>
                    <ul class="space-y-3 text-sm text-white/80 mb-8">
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Tamu tak terbatas</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Semua template</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Link personal tamu</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Tracking & RSVP</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> <strong>Galeri foto</strong></li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> <strong>Musik pengiring</strong></li>
                    </ul>
                    <a href="{{ route('packages.index') }}"
                       class="btn-glow block text-center py-3 rounded-xl text-white font-bold transition text-sm">
                        Pilih Premium ✨
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- ════ CTA FINAL ════ --}}
    <section style="background:linear-gradient(135deg,#0f0c29,#302b63);" class="py-24 px-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none"
             style="background:radial-gradient(ellipse at 50% 50%, rgba(167,139,250,.15) 0%,transparent 70%);"></div>
        <div class="max-w-3xl mx-auto text-center relative z-10 reveal">
            <div class="text-6xl mb-6">💌</div>
            <h2 class="text-3xl sm:text-5xl font-black text-white mb-4">
                Siap Sebar Undangan<br>ke Tretan Sakabbi'?
            </h2>
            <p class="text-white/60 text-lg mb-10">
                Mulai sekarang dan biarkan TretanInvite yang bikin momen spesialmu makin berkesan.
            </p>
            <a href="{{ route('packages.index') }}"
               class="btn-glow inline-flex items-center gap-3 px-10 py-5 rounded-2xl text-white font-bold text-xl z-10 relative">
                <span>💌</span> Pilih Paket Sekarang <span>→</span>
            </a>
            <p class="text-white/30 text-sm mt-6">Coba gratis dulu, tanpa kartu kredit.</p>
        </div>
    </section>

    {{-- ════ FOOTER ════ --}}
    <footer style="background:#070514;border-top:1px solid rgba(255,255,255,.05);" class="py-10 px-6 text-center">
        <div class="flex items-center justify-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-sm font-black">T</div>
            <span class="font-bold text-white/80"><span class="text-amber-400">Tretan</span>Invite</span>
        </div>
        <p class="text-white/30 text-sm">Undangan digital kanggo tretan sejati. Sebar kabungaan ka sakabbi'.</p>
        <p class="text-white/15 text-xs mt-4">&copy; {{ date('Y') }} TretanInvite &mdash; by Anni &#x1F49B;<br>Dibuat dengan cinta dari Pamekasan, untuk Keluarga Indonesia.</p>
    </footer>

    <script src="/js/tretaninvite.js"></script>
    <script>
        // Fallback: ensure reveal & stars run even if Alpine.js fails
        document.addEventListener('DOMContentLoaded', function () {
            if (window.TI) {
                window.TI.spawnStars('stars-container', 130);
                window.TI.startReveal();
            }
        });
    </script>
</body>
</html>
