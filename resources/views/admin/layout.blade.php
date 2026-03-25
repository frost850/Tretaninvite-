<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — TretanInvite</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="/css/tretaninvite.css">
    @stack('styles')
    <style @nonce>
        /* ── Admin base ── */
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ── Dark card component ── */
        .adm-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .adm-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,0.5); }

        /* ── Override legacy card classes used by the pages ── */
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

        /* ── Form sections ── */
        .form-section {
            background: rgba(255,255,255,0.04) !important;
            border: 1px solid rgba(255,255,255,0.09) !important;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
        }
        .section-title { font-size: 1rem; font-weight: 600; color: #f1f5f9; margin-bottom: 16px; }
        .field-label { display: block; font-size: .8rem; font-weight: 500; color: #94a3b8; margin-bottom: 6px; }
        .field-input {
            width: 100%;
            background: rgba(255,255,255,0.07) !important;
            border: 1px solid rgba(255,255,255,0.13) !important;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: .875rem;
            color: #f1f5f9 !important;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .field-input:focus { border-color: #f59e0b !important; box-shadow: 0 0 0 3px rgba(245,158,11,0.18) !important; }
        .field-hint { font-size: .72rem; color: #64748b; margin-top: 4px; }
        .field-input.bg-stone-50, .field-input.cursor-not-allowed { opacity: 0.6; }

        /* ── Global dark overrides (Tailwind utility class darkness) ── */
        .bg-white    { background-color: rgba(255,255,255,0.05) !important; }
        .bg-stone-100, .bg-\[#f8f9fa\], .bg-\[#f5f6fa\] { background-color: transparent !important; }
        .bg-stone-50, .bg-gray-50  { background-color: rgba(255,255,255,0.03) !important; }
        .bg-gray-100               { background-color: rgba(255,255,255,0.05) !important; }
        .bg-gray-200               { background-color: rgba(255,255,255,0.08) !important; }

        /* ── Text colors — light-to-dark mapping ── */
        .text-stone-800, .text-stone-700, .text-gray-900, .text-gray-800 { color: #f1f5f9 !important; }
        .text-stone-600, .text-gray-700 { color: #e2e8f0 !important; }
        .text-stone-500, .text-gray-600 { color: #cbd5e1 !important; }
        .text-stone-400, .text-gray-500 { color: #94a3b8 !important; }
        /* gray-400 and lighter are already visible on dark bg — no override needed */

        /* ── Border colors ── */
        .border-stone-200, .border-stone-300, .border-stone-100,
        .border-gray-200, .border-gray-100, .border-gray-300 { border-color: rgba(255,255,255,0.1) !important; }
        .divide-gray-50, .divide-stone-100 { border-color: rgba(255,255,255,0.05) !important; }

        /* ── Form input global dark override ── */
        input[type="text"], input[type="email"], input[type="password"],
        input[type="number"], input[type="date"], input[type="tel"],
        textarea, select {
            background: rgba(255,255,255,0.06) !important;
            border-color: rgba(255,255,255,0.12) !important;
            color: #f1f5f9 !important;
        }
        input::placeholder, textarea::placeholder { color: #475569 !important; }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus,
        input[type="number"]:focus, input[type="date"]:focus, input[type="tel"]:focus,
        textarea:focus, select:focus {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 3px rgba(245,158,11,0.15) !important;
            outline: none !important;
        }
        /* readonly inputs */
        input[readonly] { opacity: 0.65; cursor: not-allowed; }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up                  { animation: fadeUp 0.5s ease forwards; }
        .stagger-1                { animation-delay: 0.05s; opacity: 0; }
        .stagger-2                { animation-delay: 0.10s; opacity: 0; }
        .stagger-3                { animation-delay: 0.15s; opacity: 0; }
        .stagger-4                { animation-delay: 0.20s; opacity: 0; }
        .stagger-5                { animation-delay: 0.25s; opacity: 0; }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.5; transform: scale(1.4); }
        }
        .pulse-dot { animation: pulse-dot 1.8s infinite; }

        /* ── Bar chart ── */
        .bar-fill { transition: height 0.8s cubic-bezier(0.4,0,0.2,1); }

        /* ── Stat bars ── */
        .bg-gray-100.rounded-full { background: rgba(255,255,255,0.06) !important; }
        .bg-blue-400  { background-color: #60a5fa !important; }
        .bg-violet-400{ background-color: #a78bfa !important; }
        .bg-emerald-400{ background-color: #34d399 !important; }
        .bg-violet-500 { background-color: #8b5cf6 !important; }
        .bg-gray-200   { background: rgba(255,255,255,0.12) !important; }

        /* ── Sidebar layout ── */
        .adm-sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: 240px;
            background: linear-gradient(180deg, rgba(12,12,22,0.98) 0%, rgba(9,9,18,0.99) 100%);
            border-right: 1px solid rgba(255,255,255,0.07);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            z-index: 50;
            display: flex; flex-direction: column;
            transition: width 0.28s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
        }
        .adm-sidebar.collapsed { width: 68px; }
        .adm-sidebar::-webkit-scrollbar { width: 3px; }
        .adm-main {
            margin-left: 240px;
            transition: margin-left 0.28s cubic-bezier(0.4,0,0.2,1);
            min-height: 100vh;
        }
        .adm-main.collapsed { margin-left: 68px; }

        /* ── Sidebar header ── */
        .sidebar-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 14px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; min-width: 0; }
        .logo-icon { font-size: 1.2rem; flex-shrink: 0; }
        .logo-text {
            font-size: .88rem; font-weight: 900;
            background: linear-gradient(135deg,#fbbf24,#f97316);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            white-space: nowrap;
            transition: opacity 0.2s, max-width 0.28s;
            overflow: hidden; max-width: 160px;
        }
        .adm-sidebar.collapsed .logo-text { opacity: 0; max-width: 0; }
        .adm-sidebar.collapsed .nav-label {
            opacity: 0; max-width: 0; overflow: hidden; pointer-events: none;
            transition: opacity 0.15s, max-width 0.28s;
        }
        .nav-label { white-space: nowrap; max-width: 160px; transition: opacity 0.2s, max-width 0.28s; }
        .adm-sidebar.collapsed .nav-badge { display: none; }
        .adm-sidebar.collapsed .sidebar-section-label { opacity: 0; max-height: 0; overflow: hidden; margin: 0; padding: 0; }
        .adm-sidebar.collapsed .sidebar-user-name { opacity: 0; max-width: 0; overflow: hidden; }
        .adm-sidebar.collapsed .sidebar-role-badge { display: none; }

        /* ── Sidebar toggle button ── */
        .sidebar-toggle-btn {
            width: 26px; height: 26px; border-radius: 7px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04);
            color: #64748b;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0;
            transition: background 0.2s, color 0.2s;
            font-size: .75rem;
        }
        .sidebar-toggle-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .toggle-arrow { transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); display: inline-block; }
        .adm-sidebar.collapsed .toggle-arrow { transform: rotate(180deg); }

        /* ── Sidebar nav ── */
        .sidebar-nav {
            flex: 1; padding: 10px 8px;
            display: flex; flex-direction: column; gap: 1px;
            overflow-y: auto; overflow-x: hidden;
        }
        .sidebar-section-label {
            font-size: .6rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            color: #3f4e62; padding: 10px 10px 3px;
            transition: opacity 0.2s, max-height 0.28s;
            max-height: 30px;
            white-space: nowrap;
        }
        /* ── Nav links ── */
        .adm-nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: 10px;
            font-size: 0.82rem; font-weight: 600;
            color: rgba(255,255,255,0.48);
            transition: background 0.2s, color 0.2s;
            cursor: pointer; text-decoration: none;
            white-space: nowrap; position: relative;
            overflow: hidden;
        }
        .adm-nav-link:hover  { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.88); }
        .adm-nav-link.active { background: rgba(245,158,11,0.13); color: #fcd34d; }
        .adm-nav-link.active::before {
            content: ''; position: absolute; left: 0; top: 18%; bottom: 18%;
            width: 3px; background: #fbbf24; border-radius: 0 3px 3px 0;
        }
        .nav-icon { font-size: 1rem; flex-shrink: 0; width: 22px; text-align: center; line-height: 1; }
        .nav-badge {
            margin-left: auto; background: #ef4444; color: #fff;
            font-size: .62rem; font-weight: 700;
            padding: 1px 5px; border-radius: 99px; flex-shrink: 0;
        }

        /* ── Sidebar footer ── */
        .sidebar-footer {
            padding: 10px 8px 14px;
            border-top: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 10px; border-radius: 10px;
            background: rgba(255,255,255,0.04);
            margin-bottom: 6px; overflow: hidden;
        }
        .sidebar-user-name {
            font-size: .78rem; font-weight: 600; color: #cbd5e1;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: opacity 0.2s, max-width 0.28s; max-width: 140px;
        }
        .sidebar-role-badge {
            font-size: .6rem; font-weight: 700; padding: 1px 5px;
            border-radius: 99px; flex-shrink: 0;
        }

        /* ── Mobile ── */
        .mobile-topbar {
            display: none; position: sticky; top: 0; z-index: 40;
            padding: 10px 16px; align-items: center; gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            background: rgba(12,12,22,0.95); backdrop-filter: blur(16px);
        }
        .adm-sidebar-overlay {
            display: none; position: fixed; inset: 0; z-index: 49;
            background: rgba(0,0,0,0.55);
        }
        @media (max-width: 768px) {
            .adm-sidebar { transform: translateX(-100%); width: 240px !important; transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); }
            .adm-sidebar.mobile-open { transform: translateX(0); }
            .adm-main, .adm-main.collapsed { margin-left: 0 !important; }
            .mobile-topbar { display: flex; }
            .adm-sidebar-overlay.active { display: block; }
        }

        /* ── Admin table ── */
        .adm-table { width: 100%; font-size: 0.875rem; }
        .adm-table th { color: #64748b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding-bottom: 8px; font-weight: 600; }
        .adm-table td { padding: 10px 0; color: #cbd5e1; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .adm-table tr:hover td { background: rgba(255,255,255,0.02); }

        /* ── btn-primary ── */
        .btn-primary {
            background: linear-gradient(135deg,#f59e0b,#d97706) !important;
            box-shadow: 0 4px 15px rgba(245,158,11,0.3) !important;
            color: #fff !important;
        }
        .btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,0.4) !important; }
        .header-gradient { background: linear-gradient(135deg,#f59e0b,#d97706) !important; }

        /* ── Button reset (for clickable card headers) ── */
        button { font-family: inherit; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* ── Global Toasts ── */
        .adm-toast { pointer-events: all; transform: translateX(20px); opacity: 0; transition: transform .3s ease, opacity .3s ease; }
        .adm-toast.show { transform: translateX(0); opacity: 1; }
        /* ── Global Confirm Modal ── */
        #adm-confirm-modal { transition: opacity .15s ease; }
        #adm-confirm-modal.hidden { display: none !important; }
    </style>
</head>
<body class="page-bg min-h-screen text-slate-200">

    {{-- ── Starfield ── --}}
    <div id="adm-stars" class="star-wrap fixed inset-0 pointer-events-none z-0"></div>

    {{-- ── Mobile overlay ── --}}
    <div id="adm-overlay" class="adm-sidebar-overlay"></div>

    {{-- ── Sidebar ── --}}
    <aside id="adm-sidebar" class="adm-sidebar">

        {{-- Header: Logo + Toggle --}}
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                <span class="logo-icon">✨</span>
                <span class="logo-text">TretanInvite</span>
            </a>
            <button id="sidebar-toggle" class="sidebar-toggle-btn" title="Toggle sidebar">
                <span class="toggle-arrow">◀</span>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu Utama</div>

            <a href="{{ route('admin.dashboard') }}"
               class="adm-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="{{ route('admin.weddings.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.weddings.*') || request()->routeIs('admin.birthdays.*') || request()->routeIs('admin.greetings.*') || request()->routeIs('admin.guests.*') || request()->routeIs('admin.vip.*') ? 'active' : '' }}">
                <span class="nav-icon">💍</span>
                <span class="nav-label">Undangan</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <span class="nav-label">Pesanan</span>
            </a>
            <a href="{{ route('admin.statistik') }}"
               class="adm-nav-link {{ request()->routeIs('admin.statistik') ? 'active' : '' }}">
                <span class="nav-icon">📈</span>
                <span class="nav-label">Statistik</span>
            </a>
            <a href="{{ route('admin.track-record') }}"
               class="adm-nav-link {{ request()->routeIs('admin.track-record') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Track Record</span>
            </a>
            @if(session('admin_user_id'))
            <a href="{{ route('admin.profile') }}"
               class="adm-nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <span class="nav-icon">👤</span>
                <span class="nav-label">Profil</span>
            </a>
            @endif

            @if(session('admin_is_super'))
            <div class="sidebar-section-label">Super Admin</div>

            <a href="{{ route('admin.admins.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span>
                <span class="nav-label">Kelola Admin</span>
            </a>
            <a href="{{ route('admin.recycle.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.recycle.*') ? 'active' : '' }}">
                @php $trashCount = \App\Models\Wedding::onlyTrashed()->count() + \App\Models\Order::onlyTrashed()->count(); @endphp
                <span class="nav-icon">🗑️</span>
                <span class="nav-label">Sampah</span>
                @if($trashCount > 0)
                <span class="nav-badge">{{ $trashCount > 9 ? '9+' : $trashCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.audit-log') }}"
               class="adm-nav-link {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
                <span class="nav-icon">🔍</span>
                <span class="nav-label">Audit Log</span>
            </a>
            <a href="{{ route('admin.security') }}"
               class="adm-nav-link {{ request()->routeIs('admin.security') ? 'active' : '' }}">
                <span class="nav-icon">🛡️</span>
                <span class="nav-label">Security</span>
            </a>
            @endif
        </nav>

        {{-- Footer: User info + Logout --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <span class="nav-icon">{{ session('admin_is_super') ? '👑' : '👤' }}</span>
                <span class="sidebar-user-name">{{ session('admin_name', session('admin_email', 'Admin')) }}</span>
                @if(session('admin_is_super'))
                <span class="sidebar-role-badge" style="background:rgba(245,158,11,.15);color:#fcd34d;border:1px solid rgba(245,158,11,.3);">Super</span>
                @endif
            </div>
            <form action="{{ route('admin.logout') }}" method="post">
                @csrf
                <button type="submit" class="adm-nav-link w-full" style="color:#f87171;">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-label">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Mobile top bar ── --}}
    <div class="mobile-topbar">
        <button id="mobile-menu-btn" class="w-8 h-8 flex items-center justify-center rounded-lg border border-white/10 bg-white/05 text-slate-400 hover:text-white text-base transition">
            ☰
        </button>
        <a href="{{ route('admin.dashboard') }}" class="ml-3">
            <span class="text-sm font-black bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent">✨ TretanInvite</span>
        </a>
    </div>

    {{-- ── Welcome Toast ── --}}
    @if(session('welcome_message'))
    <div id="welcome-toast"
         class="fixed top-4 right-4 flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-800 border border-slate-600/60 shadow-2xl text-sm text-slate-100 transition-all duration-500" style="z-index:99999">
        <span class="text-lg">🎉</span>
        <span>{{ session('welcome_message') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 text-slate-400 hover:text-white text-base leading-none">&times;</button>
    </div>
    <script @nonce>setTimeout(()=>{const t=document.getElementById('welcome-toast');if(t){t.style.opacity='0';t.style.transform='translateY(-8px)';setTimeout(()=>t.remove(),500);}},4000);</script>
    @endif

    {{-- ══ Flash → Auto Toast ══ --}}
    @foreach(['success','error','warning','info'] as $__flashKey)
    @if(session($__flashKey))
    <script @nonce>document.addEventListener('DOMContentLoaded',()=>admToast(@json(session($__flashKey)),'{{ $__flashKey }}'));</script>
    @endif
    @endforeach

    {{-- ── Page Content ── --}}
    <div id="adm-main" class="adm-main">
        <div class="max-w-[1400px] mx-auto px-6 py-8">
            @yield('content')
        </div>
        <p class="text-center text-slate-700 text-xs py-6">TretanInvite Admin · {{ now()->format('H:i') }} WIB</p>
    </div>

    <script src="/js/tretaninvite.js"></script>

    @stack('scripts')

    <script @nonce>
        // ═══ GLOBAL ADMIN NOTIFICATION & AJAX SYSTEM ═══
        window.admToast = function(msg, type, duration) {
            type = type || 'success'; duration = duration !== undefined ? duration : 5000;
            const icons    = {success:'\u2705', error:'\u274C', warning:'\u26A0\uFE0F', info:'\u2139\uFE0F'};
            const borders  = {
                success: 'border-emerald-500/50',
                error:   'border-red-500/50',
                warning: 'border-amber-500/50',
                info:    'border-blue-500/50'
            };
            const bgs = {
                success: 'rgba(6,78,59,.85)',
                error:   'rgba(69,10,10,.85)',
                warning: 'rgba(69,26,3,.85)',
                info:    'rgba(7,25,65,.85)'
            };
            const container = document.getElementById('adm-toasts');
            const t = document.createElement('div');
            t.className = 'adm-toast flex items-start gap-3 px-4 py-3.5 rounded-2xl border shadow-2xl text-sm text-slate-100 ' + (borders[type]||borders.info);
            t.style.cssText = 'backdrop-filter:blur(14px);pointer-events:all;background:' + (bgs[type]||bgs.info);
            t.innerHTML = '<span class="text-base mt-0.5 shrink-0">' + (icons[type]||'\u2139\uFE0F') + '</span>' +
                          '<span class="flex-1 leading-relaxed">' + msg + '</span>' +
                          '<button onclick="this.closest(\'.adm-toast\').remove()" class="ml-1 text-slate-400 hover:text-white text-xl leading-none shrink-0 transition">&times;</button>';
            container.appendChild(t);
            requestAnimationFrame(() => { t.style.transform = 'translateX(0)'; t.style.opacity = '1'; });
            if (duration > 0) {
                setTimeout(() => {
                    t.style.transform = 'translateX(20px)'; t.style.opacity = '0';
                    setTimeout(() => t.remove(), 300);
                }, duration);
            }
            return t;
        };

        var _admConfirmCb = null;
        window.admConfirm = function(msg, onOk, opts) {
            opts = opts || {};
            var title   = opts.title   || 'Konfirmasi';
            var icon    = opts.icon    || '\u26A0\uFE0F';
            var danger  = opts.danger  || false;
            var okText  = opts.okText  || 'Ya, Lanjutkan';
            document.getElementById('adm-confirm-title').textContent = title;
            document.getElementById('adm-confirm-msg').innerHTML = msg;
            document.getElementById('adm-confirm-icon').textContent = icon;
            var okBtn = document.getElementById('adm-confirm-ok');
            okBtn.textContent = okText;
            okBtn.className = 'btn-adm text-sm font-semibold transition ' +
                (danger ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white hover:from-red-700'
                        : 'bg-gradient-to-r from-amber-500 to-orange-500 text-white hover:from-amber-600');
            _admConfirmCb = onOk;
            var modal = document.getElementById('adm-confirm-modal');
            modal.style.display = 'flex';
        };

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('adm-confirm-ok').addEventListener('click', function() {
                var modal = document.getElementById('adm-confirm-modal');
                modal.style.display = 'none';
                if (_admConfirmCb) { var cb = _admConfirmCb; _admConfirmCb = null; cb(); }
            });
            document.getElementById('adm-confirm-cancel').addEventListener('click', function() {
                document.getElementById('adm-confirm-modal').style.display = 'none';
                _admConfirmCb = null;
            });
            document.getElementById('adm-confirm-modal').addEventListener('click', function(e) {
                if (e.target === this) { this.style.display = 'none'; _admConfirmCb = null; }
            });
        });

        window.admFetch = async function(url, method, data) {
            method = method || 'POST'; data = data || {};
            var csrf = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrf ? csrf.content : '';
            var options = {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            };
            if (method !== 'GET' && method !== 'HEAD') options.body = JSON.stringify(data);
            var res = await fetch(url, options);
            var json = await res.json().catch(function() { return {}; });
            if (!res.ok) throw new Error(json.message || 'Terjadi kesalahan.');
            return json;
        };

        // Form delegation: data-adm-confirm on <form> → show modal before submit
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form.hasAttribute('data-adm-confirm')) return;
            e.preventDefault(); e.stopPropagation();
            var msg    = form.getAttribute('data-adm-confirm');
            var danger = form.hasAttribute('data-adm-danger');
            var isAjax = form.hasAttribute('data-adm-ajax');
            admConfirm(msg, function() {
                if (isAjax) {
                    var methodInput = form.querySelector('[name=_method]');
                    var method = (methodInput ? methodInput.value : form.method).toUpperCase();
                    var url = form.action;
                    var btn = form.querySelector('button[type=submit],button:not([type])');
                    if (btn) { btn.disabled = true; btn.style.opacity = '.55'; }
                    admFetch(url, method).then(function(r) {
                        admToast(r.message || 'Berhasil!', 'success');
                        if (r.wa_url) {
                            setTimeout(function() {
                                admToast('<b>' + (r.wa_label||'Notifikasi') + '</b> — ' + (r.customer_name||'') +
                                    ' &nbsp;<a href="' + r.wa_url + '" target="_blank" class="text-green-300 hover:text-green-200 underline font-semibold">Kirim WA →</a>',
                                    'success', 0);
                            }, 400);
                        }
                        var removeQuery = form.getAttribute('data-adm-remove-closest');
                        if (removeQuery) {
                            var card = form.closest(removeQuery);
                            if (card) {
                                card.style.transition = 'opacity .35s ease, transform .35s ease, max-height .45s ease, padding .45s ease, margin .45s ease';
                                card.style.opacity = '0'; card.style.transform = 'scale(.97)';
                                var h = card.offsetHeight;
                                card.style.maxHeight = h + 'px';
                                setTimeout(function() { card.style.maxHeight = '0'; card.style.padding = '0'; card.style.margin = '0'; card.style.overflow = 'hidden'; }, 200);
                                setTimeout(function() { card.remove(); }, 600);
                            }
                        }
                        if (form.hasAttribute('data-adm-reload')) setTimeout(function() { location.reload(); }, 1600);
                    }).catch(function(err) {
                        if (btn) { btn.disabled = false; btn.style.opacity = ''; }
                        admToast(err.message || 'Terjadi kesalahan, coba lagi.', 'error');
                    });
                } else {
                    var tmpAttr = form.getAttribute('data-adm-confirm');
                    form.removeAttribute('data-adm-confirm');
                    form.submit();
                    setTimeout(function() { form.setAttribute('data-adm-confirm', tmpAttr); }, 200);
                }
            }, { danger: danger, icon: danger ? '\uD83D\uDDD1\uFE0F' : '\u26A0\uFE0F', okText: danger ? 'Ya, Hapus' : 'Ya, Lanjutkan' });
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (window.TI) TI.spawnStars('adm-stars', 50);

            // ── Sidebar toggle ──
            const sidebar = document.getElementById('adm-sidebar');
            const main    = document.getElementById('adm-main');
            const btn     = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('adm-overlay');
            const mobileBtn = document.getElementById('mobile-menu-btn');

            // Restore persisted state
            if (localStorage.getItem('adm-sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
                main.classList.add('collapsed');
            }

            // Desktop toggle
            btn.addEventListener('click', () => {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                main.classList.toggle('collapsed', isCollapsed);
                localStorage.setItem('adm-sidebar-collapsed', isCollapsed);
            });

            // Mobile hamburger
            if (mobileBtn) {
                mobileBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('mobile-open');
                    overlay.classList.toggle('active');
                });
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                });
            }
        });
    </script>

    {{-- ══ GLOBAL TOAST CONTAINER & CONFIRM MODAL ══
         Placed last in <body> to guarantee highest stacking layer (above sidebar, overlays, etc.) --}}
    <div id="adm-toasts" style="position:fixed;top:1rem;right:1rem;z-index:99999;display:flex;flex-direction:column;gap:8px;max-width:420px;pointer-events:none"></div>

    <div id="adm-confirm-modal" style="display:none;position:fixed;inset:0;z-index:99998;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.7);backdrop-filter:blur(5px)">
        <div style="background:#0f172a;border:1px solid rgba(255,255,255,.12);border-radius:20px;width:100%;max-width:430px;padding:28px;box-shadow:0 30px 70px rgba(0,0,0,.9);animation:fadeUp .2s ease">
            <div class="flex items-start gap-4 mb-5">
                <div id="adm-confirm-icon" class="text-3xl shrink-0 mt-0.5"></div>
                <div class="flex-1">
                    <div id="adm-confirm-title" class="font-bold text-slate-100 text-base mb-1"></div>
                    <div id="adm-confirm-msg" class="text-sm text-slate-400 leading-relaxed"></div>
                </div>
            </div>
            <div class="flex gap-2 justify-end">
                <button id="adm-confirm-cancel"
                        class="px-5 py-2 rounded-xl text-sm font-semibold text-slate-400 border border-white/10 hover:bg-white/5 transition">Batal</button>
                <button id="adm-confirm-ok"
                        class="btn-adm text-sm font-semibold">Lanjutkan</button>
            </div>
        </div>
    </div>

</body>
</html>
