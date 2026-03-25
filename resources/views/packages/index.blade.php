<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Paket – TretanInvite</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/css/tretaninvite.css">
    <script src="/js/tretaninvite.js"></script>
    <style>
        /* ─── packages/index specific ─── */
        .btn-outline-w {
            flex:1; text-align:center; padding: 9px 0; border-radius:12px;
            border: 1px solid rgba(255,255,255,.15); color:rgba(255,255,255,.7);
            font-size:13px; font-weight:600; transition:all .2s;
        }
        .btn-outline-w:hover { background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.3); color:#fff; }
        .btn-trial-w {
            flex:1; text-align:center; padding: 9px 0; border-radius:12px;
            border: 1px solid rgba(167,139,250,.4);
            background: rgba(167,139,250,.1); color:#c4b5fd;
            font-size:13px; font-weight:600; transition:all .2s;
        }
        .btn-trial-w:hover { background:rgba(167,139,250,.2); }
        .btn-trial-b {
            flex:1; text-align:center; padding: 9px 0; border-radius:12px;
            border: 1px solid rgba(244,114,182,.4);
            background: rgba(244,114,182,.1); color:#f9a8d4;
            font-size:13px; font-weight:600; transition:all .2s;
        }
        .btn-trial-b:hover { background:rgba(244,114,182,.2); }
        .btn-order-w {
            display:block; width:100%; text-align:center; padding:10px 0; border-radius:12px;
            background: linear-gradient(135deg,rgba(167,139,250,.25),rgba(99,102,241,.25));
            border: 1px solid rgba(167,139,250,.35); color:#c4b5fd;
            font-size:13px; font-weight:700; transition:all .2s;
        }
        .btn-order-w:hover { background: linear-gradient(135deg,rgba(167,139,250,.4),rgba(99,102,241,.4)); color:#fff; }
        .btn-order-b {
            display:block; width:100%; text-align:center; padding:10px 0; border-radius:12px;
            background: linear-gradient(135deg,rgba(244,114,182,.25),rgba(236,72,153,.25));
            border: 1px solid rgba(244,114,182,.35); color:#f9a8d4;
            font-size:13px; font-weight:700; transition:all .2s;
        }
        .btn-order-b:hover { background: linear-gradient(135deg,rgba(244,114,182,.4),rgba(236,72,153,.4)); color:#fff; }
        .btn-trial-g {
            flex:1; text-align:center; padding: 9px 0; border-radius:12px;
            border: 1px solid rgba(167,139,250,.4);
            background: rgba(139,92,246,.1); color:#c4b5fd;
            font-size:13px; font-weight:600; transition:all .2s;
        }
        .btn-trial-g:hover { background:rgba(139,92,246,.2); }
        .btn-order-g {
            display:block; width:100%; text-align:center; padding:10px 0; border-radius:12px;
            background: linear-gradient(135deg,rgba(139,92,246,.25),rgba(168,85,247,.25));
            border: 1px solid rgba(139,92,246,.35); color:#c4b5fd;
            font-size:13px; font-weight:700; transition:all .2s;
        }
        .btn-order-g:hover { background: linear-gradient(135deg,rgba(139,92,246,.4),rgba(168,85,247,.4)); color:#fff; }
        .tr-stripe { background: rgba(255,255,255,.02); }
        .btn-order-vip {
            display:block; width:100%; text-align:center; padding:10px 0; border-radius:12px;
            background: linear-gradient(135deg,rgba(124,58,237,.35),rgba(219,39,119,.25));
            border: 1px solid rgba(168,85,247,.45); color:#d8b4fe;
            font-size:13px; font-weight:700; transition:all .2s;
        }
        .btn-order-vip:hover { background: linear-gradient(135deg,rgba(124,58,237,.55),rgba(219,39,119,.45)); color:#fff; }
    </style>
</head>
<body class="text-white antialiased"
      x-data="{ activeCategory: 'wedding', showCompare: false }"
      x-init="TI.initPage({ starsId:'stars-wrap', tiltSel:'.tmpl-card', watchTilt:true })">

    <div id="stars-wrap" style="position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;"></div>

    {{-- ════ NAVBAR ════ --}}
    <nav class="navbar px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2 select-none">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-lg font-black shadow-lg">T</div>
                <span class="font-bold text-lg tracking-tight"><span class="text-amber-400">Tretan</span>Invite</span>
            </a>
            <div class="hidden sm:flex items-center gap-6 text-sm text-white/60">
                <a href="{{ route('welcome') }}#fitur" class="hover:text-white transition">Fitur</a>
                <a href="#" class="text-white font-semibold">Paket</a>
                <a href="{{ route('welcome') }}#cara" class="hover:text-white transition">Cara Kerja</a>
            </div>
            <a href="{{ route('orders.create') }}"
               class="text-sm font-semibold px-4 py-2 rounded-full bg-amber-500 hover:bg-amber-400 text-black transition shadow-lg">
                Pesan Sekarang
            </a>
        </div>
    </nav>

    {{-- ════ Flash messages ════ --}}
    @if(session('error'))
    <div id="pkg-flash-error"
         class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] max-w-lg w-full flex items-start gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-sm"
         style="background:rgba(30,10,10,.95);border:1px solid rgba(239,68,68,.35);color:#fca5a5;">
        <span class="text-base mt-0.5 shrink-0">⚠️</span>
        <span class="flex-1">{{ session('error') }}</span>
        <button onclick="document.getElementById('pkg-flash-error').remove()" class="ml-1 text-red-400/60 hover:text-red-300 text-lg leading-none">&times;</button>
    </div>
    <script>setTimeout(()=>{var e=document.getElementById('pkg-flash-error');if(e){e.style.transition='opacity .5s';e.style.opacity='0';setTimeout(()=>e.remove(),500);}},6000);</script>
    @endif
    @if(session('success'))
    <div id="pkg-flash-ok"
         class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] max-w-lg w-full flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-sm"
         style="background:rgba(10,25,15,.95);border:1px solid rgba(34,197,94,.3);color:#86efac;">
        <span class="text-base shrink-0">✅</span>
        <span class="flex-1">{{ session('success') }}</span>
        <button onclick="document.getElementById('pkg-flash-ok').remove()" class="ml-1 text-emerald-400/60 hover:text-emerald-300 text-lg leading-none">&times;</button>
    </div>
    <script>setTimeout(()=>{var e=document.getElementById('pkg-flash-ok');if(e){e.style.transition='opacity .5s';e.style.opacity='0';setTimeout(()=>e.remove(),500);}},5000);</script>
    @endif

    {{-- ════ MAIN CONTENT ════ --}}
    <div class="page-bg pt-24 pb-20 px-4 sm:px-6">
        <div class="inner max-w-6xl mx-auto">

            {{-- Hero header --}}
            <div class="text-center mb-14 reveal">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-purple-400/30 bg-purple-400/10 text-purple-300 text-sm font-medium mb-6">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                    Pilih Desain & Paket
                </div>
                <h1 class="text-4xl sm:text-6xl font-black text-white leading-tight mb-4">
                    Pilih Paket<br>
                    <span style="background:linear-gradient(135deg,#f59e0b,#ef4444,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Undangan Saiki</span>
                </h1>
                <p class="text-white/55 text-base sm:text-lg max-w-xl mx-auto leading-relaxed">
                    Pilih template favoritmu. Admin kami yang buatkan & atur daftar tamu — kamu tinggal sebar ke tretan dan dulur semua.
                </p>
            </div>

            {{-- Pricing mini summary --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-10 reveal">
                <div class="pkg-mini">
                    <div class="text-xs text-white/40 font-semibold mb-2">&#x1F193; Trial</div>
                    <div class="text-2xl sm:text-3xl font-black text-white">Gratis</div>
                    <div class="text-xs text-white/35 mt-2 leading-relaxed">1 hari &middot; maks 3 tamu</div>
                </div>
                <div class="pkg-mini" style="border-color:rgba(96,165,250,.35);background:linear-gradient(160deg,rgba(59,130,246,.07),rgba(37,99,235,.1));">
                    <div class="text-xs text-blue-400 font-semibold mb-2">&#x1F499; Basic</div>
                    <div class="text-2xl sm:text-3xl font-black text-white">59<span class="text-base font-semibold text-white/50">rb</span></div>
                    <div class="text-xs text-white/35 mt-2 leading-relaxed">14 hari &middot; maks 100 tamu</div>
                </div>
                <div class="pkg-mini" style="border-color:rgba(245,158,11,.4);background:linear-gradient(160deg,rgba(245,158,11,.08),rgba(139,92,246,.08));box-shadow:0 0 24px rgba(245,158,11,.12);">
                    <div class="text-xs text-amber-400 font-semibold mb-2">&#x2B50; Premium</div>
                    <div class="text-2xl sm:text-3xl font-black text-white">89<span class="text-base font-semibold text-white/50">rb</span></div>
                    <div class="text-xs text-white/35 mt-2 leading-relaxed">30 hari &middot; galeri & musik</div>
                </div>
                <div class="pkg-mini col-span-2 sm:col-span-1" style="border-color:rgba(168,85,247,.5);background:linear-gradient(160deg,rgba(124,58,237,.12),rgba(219,39,119,.1));box-shadow:0 0 28px rgba(168,85,247,.18);">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:linear-gradient(135deg,#7c3aed,#db2777);color:#fff;font-size:10px;">&#x1F451; VIP</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-white">199<span class="text-base font-semibold text-white/50">rb</span></div>
                    <div class="text-xs mt-2 leading-relaxed" style="color:rgba(216,180,254,.6);">90 hari &middot; semua fitur</div>
                </div>
            </div>

            {{-- VIP highlight banner --}}
            <div class="mb-10 reveal" style="background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(219,39,119,.08));border:1px solid rgba(168,85,247,.25);border-radius:24px;padding:28px 24px;">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg font-black px-3 py-1 rounded-full" style="background:linear-gradient(135deg,#7c3aed,#db2777);color:#fff;">&#x1F48E; VIP</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(168,85,247,.2);color:#d8b4fe;border:1px solid rgba(168,85,247,.3);">Paket Terlengkap</span>
                        </div>
                        <h3 class="text-white font-bold text-lg mb-1">Rp 199.000 &mdash; 90 hari aktif</h3>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-xs mt-3" style="color:rgba(216,180,254,.75);">
                            <div>&#x2726; Video prewedding dalam undangan</div>
                            <div>&#x2726; Foto &amp; lagu favorit tamu</div>
                            <div>&#x2726; Tamu bisa titip ucapan &amp; doa</div>
                            <div>&#x2726; Lebih dari satu sesi acara</div>
                            <div>&#x2726; Undangan bisa dikunci password</div>
                            <div>&#x2726; Foto sampul pilihan sendiri</div>
                        </div>
                    </div>
                    <div class="shrink-0 sm:w-48 w-full">
                        <a href="{{ route('orders.create') }}?pkg=vip"
                           class="block text-center py-3 px-6 rounded-2xl font-bold text-sm sm:text-base transition"
                           style="background:linear-gradient(135deg,#7c3aed,#db2777);color:#fff;box-shadow:0 4px 20px rgba(124,58,237,.4);">
                            Pesan VIP &rarr;
                        </a>
                        <p class="text-center text-xs mt-2" style="color:rgba(216,180,254,.45);">Hubungi admin untuk info lebih lanjut</p>
                    </div>
                </div>
            </div>

            {{-- Compare button --}}
            <div class="text-center mb-10 reveal">
                <button @click="showCompare = true"
                        class="inline-flex items-center gap-2 px-7 py-3 rounded-2xl font-bold text-sm transition"
                        style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.8);">
                    &#x1F4CB; Bandingkan Fitur Paket
                    <span class="text-xs px-2 py-0.5 rounded-full bg-white/10 text-white/60">Detail lengkap</span>
                </button>
            </div>

            {{-- Category tabs --}}
            <div class="flex justify-center gap-3 mb-10 reveal">
                <button @click="activeCategory = 'wedding'"
                        :class="activeCategory === 'wedding' ? 'active-wedding' : 'inactive'"
                        class="tab-pill">
                    &#x1F492; Undangan Pernikahan
                </button>
                <button @click="activeCategory = 'birthday'"
                        :class="activeCategory === 'birthday' ? 'active-birthday' : 'inactive'"
                        class="tab-pill">
                    &#x1F382; Undangan Ulang Tahun
                </button>
                <button @click="activeCategory = 'greeting'"
                        :class="activeCategory === 'greeting' ? 'bg-violet-600/30 border-violet-400/50 text-violet-200 font-bold' : 'inactive'"
                        class="tab-pill">
                    &#x1F48C; Kartu Ucapan
                </button>
                <button @click="activeCategory = 'anniversary'"
                        :class="activeCategory === 'anniversary' ? 'bg-rose-600/30 border-rose-400/50 text-rose-200 font-bold' : 'inactive'"
                        class="tab-pill">
                    &#x1F495; Anniversary
                </button>
            </div>

            {{-- Wedding Templates --}}
            <div x-show="activeCategory === 'wedding'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">

                {{-- ── VIP Templates (horizontal card) ── --}}
                @php $hasVip = collect($packages)->filter(fn($i) => !empty($i['vip_only']) && ($i['category'] ?? 'wedding') === 'wedding')->isNotEmpty(); @endphp
                @if($hasVip)
                <div class="mb-4">
                    <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color:rgba(201,168,76,.6);">&#x1F451; Template VIP Eksklusif</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($packages as $key => $info)
                        @if(($info['category'] ?? 'wedding') === 'wedding' && !empty($info['vip_only']))
                        <div class="reveal rounded-2xl overflow-hidden flex flex-col"
                             style="background:linear-gradient(160deg,#080c1e,#100520);border:1px solid rgba(201,168,76,.3);box-shadow:0 0 40px rgba(201,168,76,.07),0 4px 24px rgba(0,0,0,.45);">
                            {{-- Preview image --}}
                            <a href="{{ route('preview.template', $key) }}" target="_blank"
                               class="relative block overflow-hidden shrink-0"
                               style="height:220px;background:linear-gradient(135deg,#050818,#0d1040,#1a0533);">
                                <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(201,168,76,.2),transparent 70%);pointer-events:none;z-index:1;"></div>
                                @if(isset($info['preview_image']))
                                    <img src="{{ asset($info['preview_image']) }}" alt="{{ $info['label'] }}"
                                         class="w-full h-full object-cover object-top relative z-0">
                                @else
                                    <span class="absolute inset-0 flex items-center justify-center text-6xl z-0">{{ $info['icon'] ?? '&#x1F492;' }}</span>
                                @endif
                                <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 55%,rgba(8,12,30,.9));z-index:2;pointer-events:none;"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4 z-10">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full"
                                          style="background:linear-gradient(135deg,rgba(124,58,237,.5),rgba(201,168,76,.3));color:#e8d08a;border:1px solid rgba(201,168,76,.4);backdrop-filter:blur(4px);">
                                        &#x265B; VIP Eksklusif
                                    </span>
                                </div>
                            </a>
                            {{-- Info --}}
                            <div class="p-5 flex flex-col flex-1">
                                <h2 class="font-bold text-white text-base mb-1">{{ $info['label'] }}</h2>
                                <p class="text-xs mb-4 leading-relaxed" style="color:rgba(255,255,255,.45);">{{ $info['description'] }}</p>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs mb-5" style="color:rgba(232,208,138,.6);">
                                    <div>&#x2726; Video prewedding</div>
                                    <div>&#x2726; Lagu favorit tamu</div>
                                    <div>&#x2726; Buku tamu realtime</div>
                                    <div>&#x2726; Multi sesi acara</div>
                                    <div>&#x2726; Password undangan</div>
                                    <div>&#x2726; Foto sampul custom</div>
                                </div>
                                <div class="mt-auto flex flex-col gap-2">
                                    <a href="{{ route('preview.template', $key) }}" target="_blank"
                                       class="block text-center py-2 rounded-xl text-xs font-semibold transition"
                                       style="border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.65);">
                                        &#x1F441; Lihat Contoh
                                    </a>
                                    <a href="{{ route('orders.create', ['template' => $key, 'pkg' => 'vip']) }}"
                                       class="block text-center py-2.5 rounded-xl text-sm font-bold transition"
                                       style="background:linear-gradient(135deg,#7c3aed,#c9a84c);color:#fff;letter-spacing:.03em;box-shadow:0 4px 16px rgba(124,58,237,.35);">
                                        Pesan {{ $info['label'] }} &rarr; Rp 199.000
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    </div>
                </div>
                <div class="my-8 border-t" style="border-color:rgba(255,255,255,.06);"></div>
                @endif

                {{-- ── Non-VIP Wedding Templates ── --}}
                <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color:rgba(255,255,255,.3);">&#x1F492; Template Pernikahan</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($packages as $key => $info)
                    @if(($info['category'] ?? 'wedding') === 'wedding' && empty($info['vip_only']))
                    <div class="tmpl-card reveal">
                        <a href="{{ route('preview.template', $key) }}" target="_blank"
                           class="preview-area block"
                           style="background:linear-gradient(135deg,#111827,#1f2937);">
                            @if(isset($info['preview_image']))
                                <img src="{{ asset($info['preview_image']) }}" alt="{{ $info['label'] }}"
                                     style="width:100%;height:100%;object-fit:cover;object-position:top;display:block;">
                            @else
                                <span style="font-size:52px;">{{ $info['icon'] ?? '&#x1F492;' }}</span>
                            @endif
                        </a>
                        <div class="card-body">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h2 class="font-bold text-white text-base">{{ $info['label'] }}</h2>
                                <span class="text-xs px-2 py-0.5 rounded-full shrink-0 mt-0.5"
                                      style="background:rgba(167,139,250,.15);color:#c4b5fd;border:1px solid rgba(167,139,250,.2);">
                                    Nikah
                                </span>
                            </div>
                            <p class="text-white/45 text-xs mb-4 leading-relaxed">{{ $info['description'] }}</p>
                            <div class="flex gap-2 mb-2">
                                <a href="{{ route('preview.template', $key) }}" target="_blank" class="btn-outline-w">&#x1F441; Lihat</a>
                                <a href="{{ route('trial.create', $key) }}" class="btn-trial-w">&#x1F193; Coba</a>
                            </div>
                            <a href="{{ route('orders.create', ['template' => $key]) }}" class="btn-order-w">
                                Pesan Sekarang &rarr;
                            </a>
                            <div class="flex gap-6 text-xs mt-3 px-0.5" style="color:rgba(255,255,255,.28)">
                                <span>Basic <strong style="color:rgba(255,255,255,.55)">59rb</strong></span>
                                <span>Premium <strong style="color:rgba(255,255,255,.55)">89rb</strong></span>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
                </div>
            </div>

            {{-- Birthday Templates --}}
            <div x-show="activeCategory === 'birthday'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-5">

                {{-- Template cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($packages as $key => $info)
                    @if(($info['category'] ?? 'wedding') === 'birthday')
                    <div class="tmpl-card reveal">
                        <a href="{{ route('preview.template', $key) }}" target="_blank"
                           class="preview-area block"
                           style="background:linear-gradient(135deg,#1a0818,#2d1020);">
                            @if(isset($info['preview_image']))
                                <img src="{{ asset($info['preview_image']) }}" alt="{{ $info['label'] }}"
                                     style="position:relative;z-index:1;width:100%;height:100%;object-fit:cover;object-position:top;display:block;">
                            @else
                                <span style="position:relative;z-index:1;font-size:52px;">{{ $info['icon'] ?? '&#x1F382;' }}</span>
                            @endif
                        </a>
                        <div class="card-body">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h2 class="font-bold text-white text-base">{{ $info['label'] }}</h2>
                                <span class="text-xs px-2 py-0.5 rounded-full shrink-0 mt-0.5"
                                      style="background:rgba(244,114,182,.15);color:#f9a8d4;border:1px solid rgba(244,114,182,.2);">
                                    Ultah
                                </span>
                            </div>
                            <p class="text-white/45 text-xs mb-4 leading-relaxed">{{ $info['description'] }}</p>
                            <a href="{{ route('preview.template', $key) }}" target="_blank" class="btn-outline-w mb-2 block text-center">&#x1F441; Lihat Preview</a>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('orders.create', ['template' => $key, 'pkg' => 'basic']) }}"
                                   class="text-center py-2 px-3 rounded-xl text-xs font-semibold transition"
                                   style="background:rgba(96,165,250,.18);border:1px solid rgba(96,165,250,.3);color:#93c5fd;">
                                    Basic<br><span class="font-bold">Rp 39.999</span>
                                </a>
                                <a href="{{ route('orders.create', ['template' => $key, 'pkg' => 'premium']) }}"
                                   class="btn-glow text-center py-2 px-3 rounded-xl text-xs font-semibold">
                                    Premium<br><span class="font-bold">Rp 49.999</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
                </div>
            </div>

            {{-- Greeting Templates --}}
            <div x-show="activeCategory === 'greeting'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($packages as $key => $info)
                    @if(($info['category'] ?? 'wedding') === 'greeting')
                    <div class="tmpl-card reveal">
                        <a href="{{ route('preview.template', $key) }}" target="_blank"
                           class="preview-area block"
                           style="background:linear-gradient(135deg,#1a0533,#2d1050);">
                            @if(isset($info['preview_image']))
                                <img src="{{ asset($info['preview_image']) }}" alt="{{ $info['label'] }}"
                                     style="position:relative;z-index:1;width:100%;height:100%;object-fit:cover;object-position:top;display:block;">
                            @else
                                <span style="position:relative;z-index:1;font-size:52px;">{{ $info['icon'] ?? '&#x1F48C;' }}</span>
                            @endif
                        </a>
                        <div class="card-body">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h2 class="font-bold text-white text-base">{{ $info['label'] }}</h2>
                                <span class="text-xs px-2 py-0.5 rounded-full shrink-0 mt-0.5"
                                      style="background:rgba(139,92,246,.15);color:#c4b5fd;border:1px solid rgba(139,92,246,.2);">
                                    Ucapan
                                </span>
                            </div>
                            <p class="text-white/45 text-xs mb-4 leading-relaxed">{{ $info['description'] }}</p>
                            <div class="flex gap-2 mb-2">
                                <a href="{{ route('preview.template', $key) }}" target="_blank" class="btn-outline-w">&#x1F441; Lihat</a>
                                <a href="{{ route('orders.create', ['template' => $key]) }}" class="btn-trial-g">&#x1F4E8; Pesan</a>
                            </div>
                            <a href="{{ route('orders.create', ['template' => $key]) }}" class="btn-order-g">
                                Pesan Sekarang &rarr;
                            </a>
                            <div class="flex justify-between text-xs mt-3 px-0.5" style="color:rgba(255,255,255,.28)">
                                <span>Basic <strong style="color:rgba(255,255,255,.55)">Rp 39.000</strong></span>
                                <span>Premium <strong style="color:rgba(255,255,255,.55)">Rp 49.000</strong></span>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- Anniversary — Coming Soon --}}
            <div x-show="activeCategory === 'anniversary'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <div class="reveal mx-auto max-w-2xl rounded-3xl overflow-hidden"
                     style="background:linear-gradient(160deg,#1a0510 0%,#3b0f22 50%,#1a0510 100%);border:1px solid rgba(251,113,133,.25);box-shadow:0 0 60px rgba(225,29,72,.12),0 8px 40px rgba(0,0,0,.5);">

                    {{-- Decorative top glow --}}
                    <div style="height:3px;background:linear-gradient(90deg,transparent,#e11d48,#d4a04a,#e11d48,transparent);"></div>

                    <div class="px-8 py-14 text-center">
                        {{-- Badge --}}
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-8 text-xs font-bold tracking-widest uppercase"
                             style="background:rgba(212,160,74,.12);border:1px solid rgba(212,160,74,.3);color:#d4a04a;">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping inline-block"></span>
                            Segera Hadir
                        </div>

                        {{-- Icon --}}
                        <div class="text-7xl mb-6 select-none" style="filter:drop-shadow(0 0 24px rgba(225,29,72,.4));">�</div>

                        {{-- Headline --}}
                        <h2 class="font-black text-white mb-4 leading-tight"
                            style="font-size:clamp(1.6rem,5vw,2.6rem);font-family:'Plus Jakarta Sans',sans-serif;">
                            Undangan <span style="background:linear-gradient(135deg,#fda4af,#d4a04a);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Anniversary</span><br>Sedang Disiapkan
                        </h2>

                        {{-- Subtext --}}
                        <p class="text-white/55 leading-relaxed mb-8 mx-auto" style="max-width:480px;font-size:.97rem;">
                            Kami sedang merancang undangan hari jadi pernikahan yang <strong class="text-rose-300">paling romantis</strong> yang pernah ada —
                            elegan, penuh kenangan, dan bikin tamu terkesima. Segera hadir untuk merayakan tahun-tahun indah bersama.
                        </p>

                        {{-- Feature teaser --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-10 text-xs">
                            @foreach([
                                ['�','Profil pasangan romantis'],
                                ['🥂','Countdown hari perayaan'],
                                ['📸','Galeri kenangan bersama'],
                                ['🎵','Musik lagu favorit kalian'],
                                ['📍','Peta & detail venue'],
                                ['✉️','RSVP tamu undangan'],
                            ] as [$ico, $txt])
                            <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-left"
                                 style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);color:rgba(255,255,255,.55);">
                                <span class="text-base shrink-0">{{ $ico }}</span>
                                <span>{{ $txt }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- CTA --}}
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', config('admin.whatsapp', '6282139069782')) }}?text={{ rawurlencode('Halo, saya ingin tahu lebih lanjut tentang undangan Anniversary yang akan segera hadir di TretanInvite. Bisa beritahu saya saat sudah tersedia?') }}"
                               target="_blank"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-sm transition hover:scale-105"
                               style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;box-shadow:0 4px 20px rgba(22,163,74,.35);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Notifikasi via WhatsApp
                            </a>
                            <a href="{{ route('orders.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-sm transition"
                               style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.7);">
                                Lihat Paket Lain &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Decorative bottom glow --}}
                    <div style="height:3px;background:linear-gradient(90deg,transparent,#d4a04a,#e11d48,#d4a04a,transparent);"></div>
                </div>
            </div>

            {{-- Admin Contact --}}
            <div class="mt-12 reveal">
                <div class="rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6"
                     style="background:linear-gradient(135deg,rgba(34,197,94,.08),rgba(16,185,129,.06));border:1px solid rgba(34,197,94,.2);">
                    <div class="flex-1 text-center sm:text-left">
                        <div class="text-green-400 font-bold text-sm uppercase tracking-widest mb-2">💬 Hubungi Admin</div>
                        <h3 class="text-white font-black text-xl mb-1">Butuh bantuan atau info lebih lanjut?</h3>
                        <p class="text-white/50 text-sm leading-relaxed">
                            Setelah pesan, admin kami siap membuatkan undangan & mengatur semua keperluan.
                            Chat kami via WhatsApp — biasanya fast respon!
                        </p>
                    </div>
                    @php $adminWa = config('admin.whatsapp', '6282139069782'); @endphp
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $adminWa) }}?text={{ rawurlencode('Assalamualaikum, saya tertarik dengan layanan undangan digital TretanInvite. Boleh minta info lebih lanjut mengenai paket yang tersedia?') }}"
                       target="_blank"
                       class="shrink-0 flex items-center gap-3 px-7 py-4 rounded-2xl font-bold text-base transition"
                       style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;box-shadow:0 4px 20px rgba(22,163,74,.35);white-space:nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Chat Admin WA
                    </a>
                </div>
            </div>

            {{-- Footer note --}}
            <div class="mt-8 text-center reveal">
                <div class="inline-block px-8 py-5 rounded-2xl" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);">
                    <p class="text-white/50 text-sm mb-2">
                        Setelah memilih paket, admin akan membuatkan undangan & mengatur daftar tamu untukmu.
                    </p>
                    <a href="{{ route('welcome') }}" class="text-amber-400 hover:text-amber-300 text-sm font-semibold transition">
                        &larr; Kembali ke beranda
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- ════ FOOTER ════ --}}
    <footer style="background:#070514;border-top:1px solid rgba(255,255,255,.05);" class="py-8 px-6 text-center relative z-10">
        <div class="flex items-center justify-center gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-sm font-black">T</div>
            <span class="font-bold text-white/70 text-sm"><span class="text-amber-400">Tretan</span>Invite</span>
        </div>
        <p class="text-white/20 text-xs">&copy; {{ date('Y') }} TretanInvite &mdash; by Anni. &#x1F49B; Dibuat dengan cinta dari Pamekasan, untuk keluarga Indonesia.</p>
    </footer>

    {{-- ════ COMPARE MODAL ════ --}}
    <div x-show="showCompare"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-bg"
         style="display:none;">
        <div class="modal-box w-full max-w-2xl max-h-[90vh] flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.outside="showCompare = false">
            <div class="flex items-center justify-between px-6 py-4"
                 style="border-bottom:1px solid rgba(255,255,255,.08);">
                <h2 class="font-bold text-white">
                    <span x-show="activeCategory === 'wedding'">&#x1F4CB; Perbandingan Fitur — Undangan Pernikahan</span>
                    <span x-show="activeCategory === 'birthday'">&#x1F382; Perbandingan Fitur — Undangan Ulang Tahun</span>
                    <span x-show="activeCategory === 'greeting'">&#x1F48C; Perbandingan Fitur — Kartu Ucapan</span>
                    <span x-show="activeCategory === 'anniversary'">&#x1F495; Undangan Anniversary — Segera Hadir</span>
                </h2>
                <button @click="showCompare = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-white/40 hover:bg-white/10 hover:text-white transition text-xl leading-none">&times;</button>
            </div>
            <div class="overflow-y-auto flex-1">

                {{-- ── Wedding compare table ── --}}
                <div x-show="activeCategory === 'wedding'">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10" style="background:#1a1040;">
                        <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
                            <th class="text-left px-6 py-4 text-white/40 font-medium w-2/5">Fitur</th>
                            <th class="px-4 py-4 text-center text-white/40 font-medium">&#x1F193; Coba</th>
                            <th class="px-4 py-4 text-center font-semibold" style="color:#93c5fd;">&#x1F499; Basic<br><span class="text-xs font-normal text-blue-400/70">Rp 59.000</span></th>
                            <th class="px-4 py-4 text-center font-semibold" style="color:#fcd34d;">&#x2B50; Premium<br><span class="text-xs font-normal text-amber-400/70">Rp 89.000</span></th>
                            <th class="px-4 py-4 text-center font-semibold" style="color:#d8b4fe;">&#x1F48E; VIP<br><span class="text-xs font-normal" style="color:rgba(216,180,254,.6);">Rp 199.000</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $weddingRows = [
                            ['Masa aktif undangan',        '1 hari',        '14 hari',        '30 hari',        '90 hari'],
                            ['Jumlah tamu',                'Maks 3',        'Maks 100',       'Tak terbatas',   'Tak terbatas'],
                            ['Link undangan personal',     '&#x2713;',      '&#x2713;',       '&#x2713;',       '&#x2713;'],
                            ['RSVP dari tamu',             '&#x2713;',      '&#x2713;',       '&#x2713;',       '&#x2713;'],
                            ['Tracking buka undangan',     '&#x2717;',      '&#x2713;',       '&#x2713;',       '&#x2713;'],
                            ['Galeri foto',                '&#x2717;',      '&#x2717;',       '&#x2713;',       '&#x2713;'],
                            ['Musik latar',                '&#x2717;',      '&#x2717;',       '&#x2713;',       '&#x2713;'],
                            ['Import tamu dari Excel',     '&#x2717;',      '&#x2713;',       '&#x2713;',       '&#x2713;'],
                            ['Video prewedding dalam undangan',  '&#x2717;', '&#x2717;',      '&#x2717;',       '&#x2713;'],
                            ['Scan tamu tanpa daftar ulang',     '&#x2717;', '&#x2717;',      '&#x2717;',       '&#x2713;'],
                            ['Pantau kehadiran secara langsung', '&#x2717;', '&#x2717;',      '&#x2717;',       '&#x2713;'],
                            ['Undangan bisa dikunci password',   '&#x2717;', '&#x2717;',      '&#x2717;',       '&#x2713;'],
                            ['Tamu bisa titip ucapan',           '&#x2717;', '&#x2717;',      '&#x2717;',       '&#x2713;'],
                            ['Lebih dari satu sesi acara',       '&#x2717;', '&#x2717;',      '&#x2717;',       '&#x2713;'],
                            ['Download rekap tamu lengkap',      '&#x2717;', '&#x2717;',      '&#x2717;',       '&#x2713;'],
                            ['Banner Mode Percobaan',      'Tampil',        'Tidak ada',      'Tidak ada',      'Tidak ada'],
                        ];
                        @endphp
                        @foreach($weddingRows as $i => [$feature, $trial, $basic, $premium, $vip])
                        <tr style="border-bottom:1px solid rgba(255,255,255,.05)" @class(['tr-stripe' => $i % 2 === 1])>
                            <td class="px-6 py-3 text-white/70 font-medium">{{ $feature }}</td>
                            <td class="px-4 py-3 text-center text-xs text-white/35">{!! $trial !!}</td>
                            <td class="px-4 py-3 text-center text-xs {{ str_contains($basic,'2713') ? 'text-green-400 font-bold text-base' : (str_contains($basic,'2717') ? 'text-white/20' : 'text-blue-300') }}">{!! $basic !!}</td>
                            <td class="px-4 py-3 text-center text-xs {{ str_contains($premium,'2713') ? 'text-green-400 font-bold text-base' : (str_contains($premium,'2717') ? 'text-white/20' : 'text-amber-300') }}">{!! $premium !!}</td>
                            <td class="px-4 py-3 text-center text-xs {{ str_contains($vip,'2713') ? 'font-bold text-base' : (str_contains($vip,'2717') ? 'text-white/20' : '') }}" style="{{ str_contains($vip,'2713') ? 'color:#c084fc;' : '' }}">{!! $vip !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                {{-- ── Birthday compare table ── --}}
                <div x-show="activeCategory === 'birthday'">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10" style="background:#1a1040;">
                        <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
                            <th class="text-left px-6 py-4 text-white/40 font-medium w-1/2">Fitur</th>
                            <th class="px-4 py-4 text-center font-semibold" style="color:#93c5fd;">&#x1F499; Basic<br><span class="text-xs font-normal text-blue-400/70">Rp 39.999</span></th>
                            <th class="px-4 py-4 text-center font-semibold" style="color:#fcd34d;">&#x2B50; Premium<br><span class="text-xs font-normal text-amber-400/70">Rp 49.999</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $birthdayRows = [
                            ['Masa aktif',                    '30 hari',       '90 hari'],
                            ['Jumlah tamu',                   'Maks 50',       'Tak terbatas'],
                            ['Link undangan personal per tamu','&#x2713;',      '&#x2713;'],
                            ['Countdown ke hari ulang tahun', '&#x2713;',      '&#x2713;'],
                            ['RSVP — tamu konfirmasi hadir',  '&#x2713;',      '&#x2713;'],
                            ['Cover foto pilihan sendiri',    '&#x2717;',      '&#x2713;'],
                            ['Galeri foto kenangan',          '&#x2717;',      '&#x2713;'],
                            ['Musik / lagu favorit latar',    '&#x2717;',      '&#x2713;'],
                            ['Halaman ucapan & doa tamu',     '&#x2717;',      '&#x2713;'],
                        ];
                        @endphp
                        @foreach($birthdayRows as $i => [$feature, $basic, $premium])
                        <tr style="border-bottom:1px solid rgba(255,255,255,.05)" @class(['tr-stripe' => $i % 2 === 1])>
                            <td class="px-6 py-3 text-white/70 font-medium">{{ $feature }}</td>
                            <td class="px-4 py-3 text-center text-xs {{ str_contains($basic,'2713') ? 'text-green-400 font-bold text-base' : (str_contains($basic,'2717') ? 'text-white/20' : 'text-blue-300') }}">{!! $basic !!}</td>
                            <td class="px-4 py-3 text-center text-xs {{ str_contains($premium,'2713') ? 'text-green-400 font-bold text-base' : (str_contains($premium,'2717') ? 'text-white/20' : 'text-amber-300') }}">{!! $premium !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                {{-- ── Greeting compare table ── --}}
                <div x-show="activeCategory === 'greeting'">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10" style="background:#1a1040;">
                        <tr style="border-bottom:1px solid rgba(255,255,255,.08);">
                            <th class="text-left px-6 py-4 text-white/40 font-medium w-1/2">Fitur</th>
                            <th class="px-4 py-4 text-center font-semibold" style="color:#93c5fd;">&#x1F499; Basic<br><span class="text-xs font-normal text-blue-400/70">Rp 39.000</span></th>
                            <th class="px-4 py-4 text-center font-semibold" style="color:#fcd34d;">&#x2B50; Premium<br><span class="text-xs font-normal text-amber-400/70">Rp 49.000</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $greetingRows = [
                            ['Masa aktif',                       '30 hari',   '90 hari'],
                            ['Kartu ucapan digital personal',    '&#x2713;',  '&#x2713;'],
                            ['Link unik per penerima',           '&#x2713;',  '&#x2713;'],
                            ['Animasi & efek kartu',             '&#x2713;',  '&#x2713;'],
                            ['Foto di dalam kartu',              '&#x2717;',  '&#x2713;'],
                            ['Musik latar',                      '&#x2717;',  '&#x2713;'],
                            ['Video dalam kartu',                '&#x2717;',  '&#x2713;'],
                            ['Galeri foto kenangan',             '&#x2717;',  '&#x2713;'],
                        ];
                        @endphp
                        @foreach($greetingRows as $i => [$feature, $basic, $premium])
                        <tr style="border-bottom:1px solid rgba(255,255,255,.05)" @class(['tr-stripe' => $i % 2 === 1])>
                            <td class="px-6 py-3 text-white/70 font-medium">{{ $feature }}</td>
                            <td class="px-4 py-3 text-center text-xs {{ str_contains($basic,'2713') ? 'text-green-400 font-bold text-base' : (str_contains($basic,'2717') ? 'text-white/20' : 'text-blue-300') }}">{!! $basic !!}</td>
                            <td class="px-4 py-3 text-center text-xs {{ str_contains($premium,'2713') ? 'text-green-400 font-bold text-base' : (str_contains($premium,'2717') ? 'text-white/20' : 'text-amber-300') }}">{!! $premium !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                {{-- ── Anniversary coming soon (no table yet) ── --}}
                <div x-show="activeCategory === 'anniversary'" class="px-6 py-10 text-center">
                    <div class="text-5xl mb-4">�</div>
                    <p class="text-white/60 text-sm">Fitur undangan anniversary sedang dalam pengerjaan.<br>Nantikan peluncurannya — akan jadi yang paling romantis! 🌹</p>
                </div>

            </div>
            <div class="px-6 py-4 flex flex-wrap gap-3 justify-center"
                 style="border-top:1px solid rgba(255,255,255,.08);">
                <template x-if="activeCategory === 'wedding'">
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="{{ route('orders.create', ['template' => 'classic', 'pkg' => 'basic']) }}" @click="showCompare = false"
                           class="px-5 py-2.5 rounded-xl font-bold text-sm transition"
                           style="background:rgba(59,130,246,.25);border:1px solid rgba(96,165,250,.3);color:#93c5fd;">
                           Pesan Basic &mdash; Rp 59.000
                        </a>
                        <a href="{{ route('orders.create', ['template' => 'classic', 'pkg' => 'premium']) }}" @click="showCompare = false"
                           class="btn-glow px-5 py-2.5 rounded-xl font-bold text-sm">
                           Pesan Premium &mdash; Rp 89.000 &#x2728;
                        </a>
                        <a href="{{ route('orders.create', ['template' => 'classic', 'pkg' => 'vip']) }}" @click="showCompare = false"
                           class="px-5 py-2.5 rounded-xl font-bold text-sm transition"
                           style="background:linear-gradient(135deg,rgba(124,58,237,.35),rgba(219,39,119,.25));border:1px solid rgba(168,85,247,.4);color:#d8b4fe;">
                           &#x1F48E; Pesan VIP &mdash; Rp 199.000
                        </a>
                    </div>
                </template>
                <template x-if="activeCategory === 'birthday'">
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="{{ route('orders.create', ['template' => 'birthday-patisserie', 'pkg' => 'basic']) }}" @click="showCompare = false"
                           class="px-5 py-2.5 rounded-xl font-bold text-sm transition"
                           style="background:rgba(59,130,246,.25);border:1px solid rgba(96,165,250,.3);color:#93c5fd;">
                           Pesan Basic &mdash; Rp 39.999
                        </a>
                        <a href="{{ route('orders.create', ['template' => 'birthday-patisserie', 'pkg' => 'premium']) }}" @click="showCompare = false"
                           class="btn-glow px-5 py-2.5 rounded-xl font-bold text-sm">
                           Pesan Premium &mdash; Rp 49.999 &#x2728;
                        </a>
                    </div>
                </template>
                <template x-if="activeCategory === 'greeting'">
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="{{ route('orders.create', ['template' => 'greeting-birthday', 'pkg' => 'basic']) }}" @click="showCompare = false"
                           class="px-5 py-2.5 rounded-xl font-bold text-sm transition"
                           style="background:rgba(59,130,246,.25);border:1px solid rgba(96,165,250,.3);color:#93c5fd;">
                           Pesan Basic &mdash; Rp 39.999
                        </a>
                        <a href="{{ route('orders.create', ['template' => 'greeting-birthday', 'pkg' => 'premium']) }}" @click="showCompare = false"
                           class="btn-glow px-5 py-2.5 rounded-xl font-bold text-sm">
                           Pesan Premium &mdash; Rp 49.999 &#x2728;
                        </a>
                    </div>
                </template>
                <template x-if="activeCategory === 'anniversary'">
                    <div>
                        <p class="text-white/40 text-sm">Segera hadir — nantikan peluncurannya! �</p>
                    </div>
                </template>
                <button @click="showCompare = false"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition"
                        style="border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.4);">
                    Tutup
                </button>
            </div>
        </div>
    </div>

<script>
(function () {
    var KEY = 'paket-scroll-y';

    // Saat navigasi keluar, simpan posisi scroll
    window.addEventListener('pagehide', function () {
        sessionStorage.setItem(KEY, window.scrollY);
    });

    // Saat kembali (back/forward), restore posisi scroll
    window.addEventListener('pageshow', function (e) {
        var saved = sessionStorage.getItem(KEY);
        if (saved !== null) {
            // Pakai requestAnimationFrame agar tunggu render selesai
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    window.scrollTo({ top: parseInt(saved, 10), behavior: 'instant' });
                    // Hapus setelah dipakai supaya fresh load tetap mulai dari atas
                    if (e.persisted || performance.getEntriesByType('navigation')[0]?.type === 'back_forward') {
                        // Keep untuk back_forward, hapus untuk navigasi biasa
                    } else {
                        sessionStorage.removeItem(KEY);
                    }
                });
            });
        }
    });

    // Preview link — tandai bahwa kita pergi ke preview (bukan navigasi biasa)
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href*="/preview/"]');
        if (a) {
            sessionStorage.setItem(KEY, window.scrollY);
        }
    });
})();
</script>

    {{-- Floating WhatsApp Button --}}
    @php $adminWaFloat = config('admin.whatsapp', '6282139069782'); @endphp
    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $adminWaFloat) }}?text={{ rawurlencode('Assalamualaikum, saya tertarik dengan layanan undangan digital TretanInvite. Boleh minta info lebih lanjut mengenai paket yang tersedia?') }}"
       target="_blank"
       class="fixed bottom-6 right-6 z-50 flex items-center gap-3 pl-4 pr-5 py-3 rounded-full shadow-2xl transition-all hover:scale-105"
       style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;box-shadow:0 4px 24px rgba(22,163,74,.45);">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <span class="text-sm font-bold hidden sm:inline">Hubungi Admin</span>
    </a>

</body>
</html>