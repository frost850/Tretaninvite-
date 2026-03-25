<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Pelanggan') — TretanInvite</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/css/tretaninvite.css">
    @stack('styles')
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .adm-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .adm-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,0.5); }

        .card, .stat-card, .card-modern {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.09) !important;
            border-radius: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.3) !important;
            backdrop-filter: blur(8px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover, .stat-card:hover, .card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.5) !important;
        }

        /* Text overrides */
        .text-gray-900, .text-gray-800 { color: #f1f5f9 !important; }
        .text-gray-700 { color: #e2e8f0 !important; }
        .text-gray-600 { color: #cbd5e1 !important; }
        .text-gray-500 { color: #94a3b8 !important; }
        .text-gray-400 { color: #64748b !important; }

        /* BG overrides */
        .bg-white { background-color: rgba(255,255,255,0.05) !important; }
        .bg-gray-50 { background-color: rgba(255,255,255,0.03) !important; }
        .bg-gray-100 { background-color: rgba(255,255,255,0.05) !important; }
        .bg-amber-50 { background-color: rgba(245,158,11,0.06) !important; }
        .bg-blue-50  { background-color: rgba(59,130,246,0.06) !important; }

        /* Border overrides */
        .border-gray-100, .border-gray-200 { border-color: rgba(255,255,255,0.08) !important; }
        .border-amber-100, .border-amber-200 { border-color: rgba(245,158,11,0.2) !important; }
        .border-blue-100, .border-blue-200   { border-color: rgba(59,130,246,0.2) !important; }

        /* Input dark */
        input[type="text"], input[type="email"], textarea, select {
            background: rgba(255,255,255,0.06) !important;
            border-color: rgba(255,255,255,0.12) !important;
            color: #f1f5f9 !important;
        }
        input[readonly] { opacity: 0.65; cursor: not-allowed; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 3px; }

        /* Nav link */
        .cust-nav-link {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            transition: background .2s, color .2s;
        }
        .cust-nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .cust-nav-link.active { background: rgba(245,158,11,0.15); color: #fcd34d; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up  { animation: fadeUp .45s ease forwards; }
        .stagger-1 { animation-delay: .05s; opacity: 0; }
        .stagger-2 { animation-delay: .10s; opacity: 0; }
        .stagger-3 { animation-delay: .15s; opacity: 0; }
        .stagger-4 { animation-delay: .20s; opacity: 0; }
        .stagger-5 { animation-delay: .25s; opacity: 0; }
        .stagger-6 { animation-delay: .30s; opacity: 0; }
        .stagger-7 { animation-delay: .35s; opacity: 0; }
        .stagger-8 { animation-delay: .40s; opacity: 0; }
    </style>
</head>
<body class="page-bg min-h-screen text-slate-200" x-data>

    {{-- Starfield --}}
    <div id="cust-stars" class="star-wrap fixed inset-0 pointer-events-none z-0"></div>

    {{-- Top Nav --}}
    <nav class="ti-navbar sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center gap-2 shrink-0">
                    <span class="text-base font-black bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent tracking-tight">TretanInvite</span>
                    @hasSection('portal_badge')
                        @yield('portal_badge')
                    @else
                        <span class="text-xs text-yellow-400 font-semibold bg-yellow-500/10 px-2 py-0.5 rounded-md border border-yellow-500/20">VIP</span>
                    @endif
                </a>
                @hasSection('nav')
                <div class="hidden sm:flex items-center gap-1">
                    @yield('nav')
                </div>
                @endif
            </div>
            <div class="text-xs text-slate-500">Portal Pelanggan</div>
        </div>
    </nav>

    {{-- Content --}}
    <div class="relative z-10 max-w-5xl mx-auto px-5 py-8">
        @yield('content')
    </div>

    <p class="relative z-10 text-center text-slate-700 text-xs py-6">TretanInvite · Portal Pelanggan</p>

    <script src="/js/tretaninvite.js"></script>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.TI) TI.spawnStars('cust-stars', 40);
        });
    </script>
</body>
</html>
