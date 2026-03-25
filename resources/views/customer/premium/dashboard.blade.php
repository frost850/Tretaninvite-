@extends('customer.layout')

@section('title', 'Portal Premium — ' . $wedding->bride_name . ($wedding->groom_name ? ' & ' . $wedding->groom_name : ''))

@section('portal_badge')
    <span class="text-xs text-sky-400 font-semibold bg-sky-500/10 px-2 py-0.5 rounded-md border border-sky-500/20">Premium</span>
@endsection

@section('nav')
    <a href="{{ route('my.vip.dashboard', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('my.vip.guests', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.guests') ? 'active' : '' }}">Tamu</a>
@endsection

@section('content')

{{-- Header --}}
<div class="mb-8 fade-up">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xs font-bold mb-3 uppercase tracking-wider">
                ⭐ Premium Portal
            </div>
            <h1 class="text-3xl font-black text-slate-100">
                {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}
            </h1>
            <p class="text-slate-400 text-sm mt-1">Pantau & kelola undangan Premium Anda</p>
        </div>
        <a href="{{ url('/' . $wedding->slug) }}" target="_blank"
           class="px-4 py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">
            Lihat Undangan
        </a>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-900/40 border border-green-700/40 text-green-300 text-sm fade-up">
    {{ session('success') }}
</div>
@endif

{{-- Archive banner --}}
@if(!empty($isArchived))
<div class="mb-6 flex flex-wrap items-center justify-between gap-3 px-4 py-4 rounded-xl bg-amber-900/20 border border-amber-700/40 fade-up">
    <div class="flex items-center gap-3">
        <span class="text-2xl">🗄️</span>
        <div>
            <div class="font-bold text-amber-300 text-sm">Mode Arsip — Masa Aktif Habis</div>
            <div class="text-xs text-amber-400/70 mt-0.5">Statistik tamu tetap tersedia. RSVP &amp; interaksi baru sudah ditutup.</div>
        </div>
    </div>
    <a href="{{ route('orders.create', ['template' => $wedding->template, 'pkg' => $wedding->package, 'renew' => $token]) }}"
       class="shrink-0 px-4 py-2 rounded-lg text-xs font-bold text-white"
       style="background:linear-gradient(135deg,#f59e0b,#d97706)">
        🔄 Perpanjang Sekarang
    </a>
</div>
@endif

{{-- Statistik Tamu --}}
<h2 class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">Statistik Tamu</h2>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8">
    <div class="bg-gradient-to-br from-slate-700 to-slate-600 rounded-xl p-4 border border-white/5 fade-up stagger-1">
        <div class="text-2xl font-bold text-white">{{ $stats['total_guests'] }}</div>
        <div class="text-xs text-slate-400 mt-1">Total Tamu</div>
    </div>
    <div class="bg-gradient-to-br from-green-900 to-green-800 rounded-xl p-4 border border-white/5 fade-up stagger-2">
        <div class="text-2xl font-bold text-white">{{ $stats['rsvp_hadir'] }}</div>
        <div class="text-xs text-slate-400 mt-1">Hadir</div>
    </div>
    <div class="bg-gradient-to-br from-red-900 to-red-800 rounded-xl p-4 border border-white/5 fade-up stagger-3">
        <div class="text-2xl font-bold text-white">{{ $stats['rsvp_tidak'] }}</div>
        <div class="text-xs text-slate-400 mt-1">Tidak Hadir</div>
    </div>
    <div class="bg-gradient-to-br from-orange-900 to-orange-800 rounded-xl p-4 border border-white/5 fade-up stagger-4">
        <div class="text-2xl font-bold text-white">{{ $stats['belum_rsvp'] }}</div>
        <div class="text-xs text-slate-400 mt-1">Belum RSVP</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-900 to-yellow-800 rounded-xl p-4 border border-white/5 fade-up stagger-5">
        <div class="text-2xl font-bold text-white">{{ $stats['belum_buka'] }}</div>
        <div class="text-xs text-slate-400 mt-1">Belum Buka</div>
    </div>
    <div class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-xl p-4 border border-white/5 fade-up stagger-6">
        <div class="text-2xl font-bold text-white">{{ $stats['total_pax'] }}</div>
        <div class="text-xs text-slate-400 mt-1">Total Kursi</div>
    </div>
</div>

{{-- Aksi --}}
<h2 class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">Aksi</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

    {{-- Kelola Tamu --}}
    <a href="{{ route('my.vip.guests', $token) }}"
       class="group flex flex-col gap-2 rounded-xl bg-slate-800 border border-white/5 hover:border-sky-500/40 hover:bg-sky-900/10 p-5 transition fade-up stagger-1">
        <div class="flex items-center gap-2">
            <span class="text-lg">👥</span>
            <div class="font-semibold text-slate-100 group-hover:text-sky-400 transition">Kelola Tamu</div>
        </div>
        <div class="text-sm text-slate-400">
            Tambah, lihat, dan hapus tamu undangan secara mandiri.
            <span class="text-sky-400 font-semibold">{{ $stats['total_guests'] }} tamu</span> terdaftar.
        </div>
    </a>

    {{-- Tracking --}}
    @if($wedding->tracking_token)
    <a href="{{ route('tracking.show', $wedding->tracking_token) }}"
       class="group flex flex-col gap-2 rounded-xl bg-slate-800 border border-white/5 hover:border-green-500/40 hover:bg-green-900/10 p-5 transition fade-up stagger-2">
        <div class="flex items-center gap-2">
            <span class="text-lg">📡</span>
            <div class="font-semibold text-slate-100 group-hover:text-green-400 transition">Tracking Tamu</div>
        </div>
        <div class="text-sm text-slate-400">Lihat siapa yang sudah buka undangan dan status RSVP.</div>
    </a>
    @endif

    {{-- Lihat Undangan --}}
    <a href="{{ url('/' . $wedding->slug) }}" target="_blank"
       class="group flex flex-col gap-2 rounded-xl bg-slate-800 border border-white/5 hover:border-blue-500/40 hover:bg-blue-900/10 p-5 transition fade-up stagger-4">
        <div class="flex items-center gap-2">
            <span class="text-lg">💌</span>
            <div class="font-semibold text-slate-100 group-hover:text-blue-400 transition">Lihat Undangan</div>
        </div>
        <div class="text-sm text-slate-400">Buka halaman undangan seperti yang dilihat tamu.</div>
    </a>

    {{-- Teaser VIP --}}
    <div class="sm:col-span-2 rounded-xl border border-dashed border-yellow-500/30 bg-yellow-500/5 p-5 fade-up">
        <div class="flex items-start gap-3">
            <span class="text-2xl mt-0.5">✨</span>
            <div>
                <div class="font-semibold text-yellow-300 mb-1">Fitur Eksklusif VIP</div>
                <div class="text-sm text-slate-400 mb-3">
                    Upgrade ke paket <span class="text-yellow-400 font-semibold">VIP Royal</span> untuk mendapatkan:
                    <span class="text-slate-300">QR Code personal per tamu</span>,
                    <span class="text-slate-300">scan check-in hari H</span>,
                    <span class="text-slate-300">guestbook digital</span>,
                    <span class="text-slate-300">amplop digital</span>, dan
                    <span class="text-slate-300">tiket undangan digital</span>.
                </div>
                <a href="{{ route('packages.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-yellow-400 hover:text-yellow-300 transition">
                    Lihat paket VIP →
                </a>
            </div>
        </div>
    </div>

    {{-- Simpan Link Portal --}}
    <div x-data="{ copied: false }"
         class="sm:col-span-2 flex flex-col gap-3 rounded-xl border border-sky-500/20 bg-sky-500/5 p-5 fade-up">
        <div>
            <div class="font-semibold text-slate-100 mb-1">Simpan Link Portal</div>
            <div class="text-sm text-slate-400">Bookmark halaman ini untuk akses portal kapan saja tanpa perlu login.</div>
        </div>
        <div class="flex items-center gap-2">
            <input type="text" readonly value="{{ url()->current() }}"
                   class="flex-1 text-xs font-mono rounded-lg px-3 py-2 min-w-0">
            <button @click="navigator.clipboard.writeText('{{ url()->current() }}').then(() => { copied = true; setTimeout(() => copied = false, 2500) })"
                    class="shrink-0 px-3 py-2 rounded-lg text-xs font-semibold text-white bg-gradient-to-r from-sky-500 to-blue-500 hover:opacity-90 transition whitespace-nowrap">
                <span x-show="!copied">Salin</span>
                <span x-show="copied" x-cloak>Tersalin!</span>
            </button>
        </div>
    </div>

</div>

<div class="rounded-xl border border-white/5 bg-slate-800/50 p-4 text-center text-slate-500 text-sm fade-up">
    Fitur edit undangan, export laporan & pengaturan dikelola oleh admin kami.
</div>

@endsection
