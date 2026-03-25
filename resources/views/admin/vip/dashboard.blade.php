@extends('admin.layout')

@section('title', 'VIP Dashboard — ' . $wedding->bride_name)

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.weddings.index') }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">← Kembali ke Daftar Undangan</a>
        <h1 class="text-2xl font-semibold text-slate-100 flex items-center gap-2">
            <span class="text-yellow-400">👑</span> VIP Dashboard
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}
            <span class="ml-2 px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-400 text-xs font-bold">VIP</span>
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ url('/' . $wedding->slug) }}" target="_blank"
           class="px-4 py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">
            🔗 Lihat Undangan
        </a>
        @if(!empty($customerToken))
        <div x-data="{ copied: false }" class="relative">
            <button @click="navigator.clipboard.writeText('{{ route('my.vip.dashboard', $customerToken) }}').then(() => { copied = true; setTimeout(() => copied = false, 2500) })"
                    class="px-4 py-2 rounded-lg border border-yellow-500/40 text-yellow-400 text-sm hover:bg-yellow-500/10 transition flex items-center gap-2">
                👑 Link Portal Pelanggan
                <span x-show="!copied">📋 Salin</span>
                <span x-show="copied" x-cloak class="text-green-400 font-semibold">✅ Tersalin!</span>
            </button>
        </div>
        @endif
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Tamu',     'value' => $stats['total_guests'],  'color' => 'from-slate-700 to-slate-600',   'icon' => '👥'],
        ['label' => 'Hadir (RSVP)',   'value' => $stats['rsvp_hadir'],    'color' => 'from-green-800 to-green-700',   'icon' => '✅'],
        ['label' => 'Tidak Hadir',    'value' => $stats['rsvp_tidak'],    'color' => 'from-red-900 to-red-800',       'icon' => '❌'],
        ['label' => 'Check-In',       'value' => $stats['checked_in'],    'color' => 'from-teal-800 to-teal-700',     'icon' => '📷'],
        ['label' => 'Belum Buka',     'value' => $stats['belum_buka'],    'color' => 'from-yellow-900 to-yellow-800', 'icon' => '📩'],
        ['label' => 'Total Pax',      'value' => $stats['total_pax'],     'color' => 'from-blue-900 to-blue-800',     'icon' => '🪑'],
        ['label' => 'Ucapan Masuk',   'value' => $stats['guestbook'] ?? '—', 'color' => 'from-purple-900 to-purple-800', 'icon' => '💬'],
    ] as $s)
    <div class="bg-gradient-to-br {{ $s['color'] }} rounded-xl p-4 border border-white/5">
        <div class="text-2xl mb-1">{{ $s['icon'] }}</div>
        <div class="text-2xl font-bold text-white">{{ $s['value'] }}</div>
        <div class="text-xs text-slate-400 mt-1">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- VIP Feature Links --}}
<h2 class="text-slate-300 font-semibold text-sm uppercase tracking-wider mb-4">Fitur VIP</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

    {{-- Live RSVP --}}
    <a href="{{ route('admin.vip.rsvp-live', $wedding) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-red-500/40 hover:bg-red-900/10 p-5 transition">
        <div class="text-3xl">🔴</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-red-400 transition">Live RSVP</div>
            <div class="text-sm text-slate-400 mt-1">Pantau konfirmasi tamu secara real-time tanpa refresh halaman.</div>
        </div>
    </a>

    {{-- Scan Check-In --}}
    <a href="{{ route('admin.vip.scan', ['wedding_id' => $wedding->id]) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-teal-500/40 hover:bg-teal-900/10 p-5 transition">
        <div class="text-3xl">📷</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-teal-400 transition">Scan Check-In</div>
            <div class="text-sm text-slate-400 mt-1">Scan QR tamu di venue — otomatis tandai hadir & check-in tanpa ketik manual.</div>
        </div>
    </a>

    {{-- QR Code --}}
    <a href="{{ route('admin.vip.qr-codes', ['wedding_id' => $wedding->id]) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-blue-500/40 hover:bg-blue-900/10 p-5 transition">
        <div class="text-3xl">🔲</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-blue-400 transition">QR Code Tamu</div>
            <div class="text-sm text-slate-400 mt-1">Generate QR unik per tamu untuk check-in di venue. Bisa diprint atau dikirim via WA.</div>
        </div>
    </a>

    {{-- Export Laporan --}}
    <a href="{{ route('admin.vip.export', ['wedding_id' => $wedding->id]) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-green-500/40 hover:bg-green-900/10 p-5 transition">
        <div class="text-3xl">📊</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-green-400 transition">Export Laporan Tamu</div>
            <div class="text-sm text-slate-400 mt-1">Download Excel lengkap: RSVP, pax, kapan buka, berapa kali buka undangan.</div>
        </div>
    </a>

    {{-- Guestbook --}}
    @if($wedding->guestbook_enabled)
    <a href="{{ route('admin.vip.guestbook', ['wedding_id' => $wedding->id]) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-purple-500/40 hover:bg-purple-900/10 p-5 transition">
        <div class="text-3xl">💬</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-purple-400 transition">Guestbook Digital</div>
            <div class="text-sm text-slate-400 mt-1">Lihat & moderasi ucapan dari tamu di halaman undangan. {{ $stats['guestbook'] }} ucapan masuk.</div>
        </div>
    </a>
    @endif

    {{-- Settings --}}
    <a href="{{ route('admin.vip.settings', ['wedding_id' => $wedding->id]) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-yellow-500/40 hover:bg-yellow-900/10 p-5 transition">
        <div class="text-3xl">⚙️</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-yellow-400 transition">Pengaturan VIP</div>
            <div class="text-sm text-slate-400 mt-1">Video embed, password proteksi, guestbook, notif email, extra acara.</div>
        </div>
    </a>

    {{-- Cetak Fisik --}}
    <a href="{{ route('admin.weddings.print', $wedding) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-rose-500/40 hover:bg-rose-900/10 p-5 transition">
        <div class="text-3xl">🖨️</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-rose-400 transition">Cetak Undangan Fisik</div>
            <div class="text-sm text-slate-400 mt-1">Print undangan A5 dengan QR personal per tamu. Siap kirim ke percetakan.</div>
            <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded bg-rose-500/20 text-rose-400">🌸 Print ready · A5</span>
        </div>
    </a>

    {{-- Edit Undangan --}}
    <a href="{{ route('admin.weddings.edit', $wedding) }}"
       class="group flex items-start gap-4 rounded-xl bg-slate-800 border border-white/5 hover:border-slate-400/40 hover:bg-slate-700/30 p-5 transition">
        <div class="text-3xl">✏️</div>
        <div>
            <div class="font-semibold text-slate-100 group-hover:text-slate-200 transition">Edit Undangan</div>
            <div class="text-sm text-slate-400 mt-1">Ubah konten, tamu, galeri, dan template undangan.</div>
        </div>
    </a>

</div>

@endsection
