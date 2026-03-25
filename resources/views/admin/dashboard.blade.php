@extends('admin.layout')

@section('title', 'Dashboard')

@push('styles')
<style @nonce>
/* ── Section label ─────────────────────────────────────────── */
.section-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #475569;
    margin-bottom: 12px;
}
.section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.06);
}

/* ── Stat card ─────────────────────────────────────────────── */
.dash-stat {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 20px;
    transition: transform .2s, box-shadow .2s;
}
.dash-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
.dash-stat-danger { border-color: rgba(239,68,68,0.35) !important; background: rgba(239,68,68,0.05) !important; }

/* ── Panel card ────────────────────────────────────────────── */
.dash-panel {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
}

/* ── Bar chart ─────────────────────────────────────────────── */
.chart-col { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; }
.chart-bar-wrap { width: 100%; display: flex; align-items: flex-end; height: 100px; }
.chart-bar { width: 100%; border-radius: 4px 4px 0 0; transition: height .7s cubic-bezier(.4,2,.55,.9); }
.chart-bar-today { background: linear-gradient(to top, #7c3aed, #a78bfa) !important; }
.chart-bar-fill  { background: rgba(255,255,255,0.1); }
.chart-bar-fill:hover { background: rgba(255,255,255,0.18); }

/* ── Package pill ──────────────────────────────────────────── */
.pkg-vip     { background: rgba(234,179,8,.12);  color: #fbbf24; border: 1px solid rgba(234,179,8,.25);  }
.pkg-premium { background: rgba(245,158,11,.12); color: #f59e0b; border: 1px solid rgba(245,158,11,.25); }
.pkg-basic   { background: rgba(59,130,246,.12); color: #60a5fa; border: 1px solid rgba(59,130,246,.25); }
.pkg-trial   { background: rgba(148,163,184,.08); color: #94a3b8; border: 1px solid rgba(148,163,184,.15); }

/* ── Progress bar ──────────────────────────────────────────── */
.prog-track { height: 6px; background: rgba(255,255,255,0.06); border-radius: 99px; overflow: hidden; }
.prog-fill  { height: 100%; border-radius: 99px; transition: width .8s ease; }

/* ── Alert ─────────────────────────────────────────────────── */
.dash-alert { display: flex; align-items: center; gap: 14px; border-radius: 14px; padding: 14px 18px; margin-bottom: 12px; }
.dash-alert-danger { background: rgba(127,29,29,.4); border: 1px solid rgba(239,68,68,.3); }
.dash-alert-warn   { background: rgba(120,53,15,.3); border: 1px solid rgba(217,119,6,.3); }

/* ── Item row ──────────────────────────────────────────────── */
.item-row {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 12px; border-radius: 12px;
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.05);
    transition: background .15s, border-color .15s;
}
.item-row:hover { background: rgba(124,58,237,.08); border-color: rgba(124,58,237,.2); }

/* ── Upcoming badge ────────────────────────────────────────── */
.days-badge { font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 99px; white-space: nowrap; flex-shrink: 0; }
.days-today  { background: rgba(239,68,68,.2);   color: #f87171; border: 1px solid rgba(239,68,68,.3); }
.days-close  { background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
.days-normal { background: rgba(52,211,153,.1);  color: #34d399; border: 1px solid rgba(52,211,153,.2); }

/* ── Pulse dot ─────────────────────────────────────────────── */
{{ '@' }}keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: .6; transform: scale(1.5); } }
.pulse-dot { animation: pulse-dot 1.5s ease-in-out infinite; }

/* ── VIP row ───────────────────────────────────────────────── */
.vip-row {
    display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
    padding: 14px 16px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(234,179,8,.12);
    border-radius: 14px;
    transition: background .2s, border-color .2s;
}
.vip-row:hover { background: rgba(234,179,8,.06); border-color: rgba(234,179,8,.22); }

/* ── Quick action card ─────────────────────────────────────── */
.qa-card {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 18px 12px; text-align: center;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    transition: transform .2s, background .2s, border-color .2s;
}
.qa-card:hover { transform: translateY(-3px); }

/* ── Status pill ───────────────────────────────────────────── */
.status-pill { display: inline-flex; align-items: center; gap: 5px; font-size: 0.7rem; font-weight: 700; padding: 3px 10px; border-radius: 99px; }
.status-ok   { background: rgba(52,211,153,.12); color: #34d399; border: 1px solid rgba(52,211,153,.2); }
.status-warn { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.2); }
.status-err  { background: rgba(239,68,68,.12);  color: #f87171; border: 1px solid rgba(239,68,68,.2); }
</style>
@endpush

@section('content')

{{-- ══════ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6 fade-up stagger-1">
    <div>
        <p class="text-slate-500 text-xs">{{ now()->translatedFormat('l, d F Y') }}</p>
        <h1 class="text-2xl font-black text-slate-100 mt-0.5">Dashboard Admin</h1>
        <p class="text-slate-500 text-sm mt-0.5">Ringkasan aktivitas undangan digital Anda.</p>
    </div>
    <div class="flex flex-wrap gap-2 mt-1">
        <span class="status-pill {{ $qrisMissing ? 'status-err' : 'status-ok' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $qrisMissing ? 'bg-red-400' : 'bg-emerald-400' }} inline-block"></span>
            QRIS {{ $qrisMissing ? 'Belum diupload' : 'Tersedia' }}
        </span>
        <span class="status-pill {{ $pendingConfirm > 0 ? 'status-warn' : 'status-ok' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $pendingConfirm > 0 ? 'bg-yellow-400 pulse-dot' : 'bg-emerald-400' }} inline-block"></span>
            {{ $pendingConfirm }} konfirmasi pending
        </span>
        @if($rejectedCount > 0)
        <span class="status-pill status-err">
            <span class="w-1.5 h-1.5 rounded-full bg-red-400 pulse-dot inline-block"></span>
            {{ $rejectedCount }} ditolak
        </span>
        @endif
        @if($stuckCount > 0)
        <span class="status-pill status-warn">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
            {{ $stuckCount }} terbengkalai
        </span>
        @endif
        <span class="status-pill status-ok">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
            {{ $activeWeddings }} undangan aktif
        </span>
    </div>
</div>

{{-- ══════ ALERT BANNERS ════════════════════════════════════════════════ --}}
@if($qrisMissing)
<div class="dash-alert dash-alert-danger fade-up stagger-1">
    <div class="w-9 h-9 rounded-xl bg-red-900/50 flex items-center justify-center text-lg shrink-0">&#9888;&#65039;</div>
    <div class="flex-1">
        <p class="font-bold text-red-300 text-sm">QRIS belum diupload</p>
        <p class="text-red-400/70 text-xs mt-0.5">Pelanggan tidak bisa melihat QRIS untuk pembayaran.</p>
    </div>
    <a href="{{ route('admin.orders.index') }}#qris"
       class="shrink-0 px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-xl transition">Upload Sekarang</a>
</div>
@endif

@if($expiringSoon->isNotEmpty())
@php $criticalCount = $expiringSoon->filter(fn($w) => $w->trial_expires_at->isPast() || $w->trial_expires_at->diffInDays(now(), false) >= -2)->count(); @endphp
<div class="dash-alert {{ $criticalCount > 0 ? 'dash-alert-danger' : 'dash-alert-warn' }} fade-up stagger-2">
    <div class="w-9 h-9 rounded-xl {{ $criticalCount > 0 ? 'bg-red-900/50' : 'bg-amber-900/50' }} flex items-center justify-center text-lg shrink-0">&#9203;</div>
    <div class="flex-1">
        <p class="font-bold {{ $criticalCount > 0 ? 'text-red-300' : 'text-amber-300' }} text-sm mb-2">
            {{ $criticalCount > 0 ? 'Undangan kritis — ' : '' }}Masa aktif berakhir dalam 7 hari ({{ $expiringSoon->count() }} undangan)
        </p>
        <div class="flex flex-wrap gap-2">
            @foreach($expiringSoon as $tw)
            @php
                $twRoute = str_starts_with($tw->template ?? '', 'birthday')
                    ? route('admin.birthdays.edit', $tw)
                    : (str_starts_with($tw->template ?? '', 'greeting')
                        ? route('admin.greetings.edit', $tw)
                        : route('admin.weddings.edit', $tw));
                $isUrgent = $tw->isExpired() || $tw->trial_expires_at->diffInDays(now(), false) >= -2;
            @endphp
            <a href="{{ $twRoute }}"
               class="px-3 py-1 {{ $isUrgent ? 'bg-red-900/40 border-red-600/40 text-red-300 hover:bg-red-900/60' : 'bg-amber-900/30 border-amber-600/30 text-amber-300 hover:bg-amber-900/50' }} border text-xs font-bold rounded-lg transition">
                {{ $tw->bride_name }}
                @if($tw->isExpired())
                    <span class="text-red-400 ml-1">Berakhir</span>
                @else
                    <span class="opacity-70 font-normal ml-1">{{ $tw->trial_expires_at->diffForHumans() }}</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Alert: Pesanan Ditolak ───────────────────────────────────────────── --}}
@if($rejectedCount > 0)
<div class="dash-alert dash-alert-danger fade-up stagger-3">
    <div class="w-9 h-9 rounded-xl bg-red-900/50 flex items-center justify-center text-lg shrink-0">&#10060;</div>
    <div class="flex-1">
        <p class="font-bold text-red-300 text-sm">{{ $rejectedCount }} Pembayaran Ditolak &mdash; Perlu Tindak Lanjut</p>
        <p class="text-red-400/70 text-xs mt-0.5">Customer belum mengirim ulang bukti bayar yang valid. Hubungi mereka via WhatsApp.</p>
    </div>
    <a href="{{ route('admin.orders.index', ['payment' => 'ditolak']) }}"
       class="shrink-0 px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-xl transition">Tinjau &rarr;</a>
</div>
@endif

{{-- ── Alert: Pesanan Terbengkalai (belum bayar > 48 jam) ───────────────── --}}
@if($stuckCount > 0)
<div class="dash-alert dash-alert-warn fade-up stagger-3">
    <div class="w-9 h-9 rounded-xl bg-amber-900/50 flex items-center justify-center text-lg shrink-0">&#9749;</div>
    <div class="flex-1">
        <p class="font-bold text-amber-300 text-sm">{{ $stuckCount }} Pesanan Terbengkalai (&gt; 48 jam tanpa pembayaran)</p>
        <p class="text-amber-400/70 text-xs mt-0.5">Pesanan belum dibayar lebih dari 48 jam — pertimbangkan untuk follow-up ke customer.</p>
    </div>
    <a href="{{ route('admin.orders.index') }}"
       class="shrink-0 px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl transition">Follow Up &rarr;</a>
</div>
@endif

{{-- ══════ SECTION 1 — STATISTIK UTAMA ══════════════════════════════════ --}}
<div class="section-label fade-up stagger-1">Statistik Utama</div>
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-8">

    {{-- Pesanan Hari Ini --}}
    <div class="dash-stat fade-up stagger-1">
        <div class="flex items-start justify-between">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider leading-tight">Pesanan<br>Hari Ini</p>
            <div class="w-8 h-8 bg-orange-500/15 rounded-xl flex items-center justify-center text-base shrink-0">&#128230;</div>
        </div>
        <p class="text-4xl font-black mt-3 text-slate-100">{{ $ordersToday }}</p>
        <p class="text-slate-500 text-xs mt-1">{{ $ordersMonth }} bulan ini</p>
        <a href="{{ route('admin.orders.index', ['date' => 'today']) }}"
           class="mt-3 inline-block text-xs text-orange-400 hover:text-orange-300 font-semibold transition">Lihat &rarr;</a>
    </div>

    {{-- Perlu Konfirmasi --}}
    <div class="dash-stat fade-up stagger-2 {{ $pendingConfirm > 0 ? 'dash-stat-danger' : '' }}">
        <div class="flex items-start justify-between">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider leading-tight">Perlu<br>Konfirmasi</p>
            <div class="relative w-8 h-8 bg-red-500/15 rounded-xl flex items-center justify-center text-base shrink-0">
                &#128276;
                @if($pendingConfirm > 0)
                <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full pulse-dot border border-slate-900"></span>
                @endif
            </div>
        </div>
        <p class="text-4xl font-black mt-3 {{ $pendingConfirm > 0 ? 'text-red-400' : 'text-slate-100' }}">{{ $pendingConfirm }}</p>
        <p class="text-slate-500 text-xs mt-1">
            {{ $pendingNew }} baru &middot;
            @if($rejectedCount > 0)
            <span class="text-red-400 font-semibold">{{ $rejectedCount }} ditolak</span>
            @else
            0 ditolak
            @endif
        </p>
        <a href="{{ route('admin.orders.index', ['status' => 'baru']) }}"
           class="mt-3 inline-block text-xs text-red-400 hover:text-red-300 font-semibold transition">Konfirmasi &rarr;</a>
    </div>

    {{-- Total Undangan --}}
    <div class="dash-stat fade-up stagger-3">
        <div class="flex items-start justify-between">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider leading-tight">Total<br>Undangan</p>
            <div class="w-8 h-8 bg-violet-500/15 rounded-xl flex items-center justify-center text-base shrink-0">&#128141;</div>
        </div>
        <p class="text-4xl font-black mt-3 text-slate-100">{{ $totalWeddings }}</p>
        <p class="text-slate-500 text-xs mt-1">{{ $activeWeddings }} aktif saat ini</p>
        <a href="{{ route('admin.weddings.index') }}"
           class="mt-3 inline-block text-xs text-violet-400 hover:text-violet-300 font-semibold transition">Kelola &rarr;</a>
    </div>

    {{-- Pendapatan Bulan Ini --}}
    <div class="dash-stat fade-up stagger-4">
        <div class="flex items-start justify-between">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider leading-tight">Pendapatan<br>Bulan Ini</p>
            <div class="w-8 h-8 bg-emerald-500/15 rounded-xl flex items-center justify-center text-base shrink-0">&#128176;</div>
        </div>
        @if($isSuperAdmin)
        <p class="text-xl font-black mt-3 text-emerald-400 leading-snug">
            Rp {{ number_format($revenueMonth, 0, ',', '.') }}
        </p>
        <div class="flex items-center gap-1.5 mt-1">
            @if($revenueGrowth !== null)
            <span class="text-xs font-bold {{ $revenueGrowth >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%
            </span>
            <span class="text-xs text-slate-600">vs bln lalu</span>
            @else
            <span class="text-xs text-slate-600">&mdash;</span>
            @endif
        </div>
        @else
        <p class="text-sm font-semibold mt-3 text-slate-500 italic">Hanya super-admin</p>
        <div class="flex items-center gap-1.5 mt-1">
            <span class="text-xs text-slate-600">&mdash;</span>
        </div>
        @endif
        <a href="{{ route('admin.orders.index') }}"
           class="mt-3 inline-block text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition">Detail &rarr;</a>
    </div>

    {{-- Check-In Hari Ini --}}
    <div class="dash-stat fade-up stagger-5">
        <div class="flex items-start justify-between">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider leading-tight">Check-In<br>Hari Ini</p>
            <div class="w-8 h-8 bg-teal-500/15 rounded-xl flex items-center justify-center text-base shrink-0">&#9989;</div>
        </div>
        <p class="text-4xl font-black mt-3 text-teal-400">{{ $checkedInToday }}</p>
        <p class="text-slate-500 text-xs mt-1">tamu hadir hari ini</p>
        <p class="mt-3 text-xs text-teal-500/70">VIP Check-In</p>
    </div>

</div>

{{-- ══════ SECTION 2 — ANALITIK ══════════════════════════════════════════ --}}
<div class="section-label fade-up stagger-2">Analitik</div>
<div class="grid lg:grid-cols-3 gap-4 mb-8">

    {{-- Chart pesanan 7 hari --}}
    <div class="lg:col-span-2 dash-panel p-6 fade-up stagger-2">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-300 text-sm">Pesanan 7 Hari Terakhir</h2>
            <span class="text-xs text-slate-600">Hari ini: <span class="text-violet-400 font-bold">{{ $chartDays->last()['count'] ?? 0 }}</span> pesanan</span>
        </div>
        <div class="flex items-end gap-2 h-28">
            @foreach($chartDays as $day)
            @php
                $pct = $chartMax > 0 ? round(($day['count'] / $chartMax) * 100) : 0;
                $isToday = $day['date'] === now()->toDateString();
            @endphp
            <div class="chart-col">
                <span class="text-xs font-bold {{ $isToday ? 'text-violet-400' : 'text-slate-600' }}">
                    {{ $day['count'] > 0 ? $day['count'] : '' }}
                </span>
                <div class="chart-bar-wrap">
                    <div class="chart-bar {{ $isToday ? 'chart-bar-today' : 'chart-bar-fill' }}"
                         style="height: {{ max(4, $pct) }}%"></div>
                </div>
                <span class="text-xs {{ $isToday ? 'text-violet-400 font-bold' : 'text-slate-500' }}">{{ $day['label'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Distribusi paket --}}
        <div class="mt-6 pt-5 border-t border-white/5">
            <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-3">Distribusi Paket Aktif</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach([
                    ['key' => 'vip',     'label' => 'VIP Royal', 'cls' => 'pkg-vip'],
                    ['key' => 'premium', 'label' => 'Premium',   'cls' => 'pkg-premium'],
                    ['key' => 'basic',   'label' => 'Basic',     'cls' => 'pkg-basic'],
                    ['key' => 'trial',   'label' => 'Trial',     'cls' => 'pkg-trial'],
                ] as $pkg)
                <div class="flex items-center justify-between px-3 py-2 rounded-xl {{ $pkg['cls'] }}">
                    <span class="text-xs font-semibold opacity-80">{{ $pkg['label'] }}</span>
                    <span class="text-sm font-black">{{ $packageStats[$pkg['key']] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Distribusi jenis undangan --}}
        <div class="mt-4 pt-4 border-t border-white/5">
            <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-3">Jenis Undangan</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach([
                    ['key' => 'wedding',    'label' => 'Pernikahan',  'icon' => '&#128141;', 'bg' => 'bg-violet-900/25 border border-violet-700/20 text-violet-300'],
                    ['key' => 'birthday',   'label' => 'Ulang Tahun', 'icon' => '&#127874;', 'bg' => 'bg-pink-900/25 border border-pink-700/20 text-pink-300'],
                    ['key' => 'greeting',   'label' => 'Greeting',    'icon' => '&#128140;', 'bg' => 'bg-emerald-900/25 border border-emerald-700/20 text-emerald-300'],
                ] as $jenis)
                <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl {{ $jenis['bg'] }}">
                    <span class="text-base shrink-0">{!! $jenis['icon'] !!}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold opacity-80 truncate">{{ $jenis['label'] }}</p>
                        <p class="text-sm font-black leading-tight">
                            <span title="Aktif">{{ $jenisActive[$jenis['key']] }}</span>
                            <span class="text-xs font-normal opacity-40">/ {{ $jenisCount[$jenis['key']] }}</span>
                        </p>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($jenisExpired[$jenis['key']] > 0)
                            <span class="text-xs text-red-400 font-semibold" title="Expired">⏰ {{ $jenisExpired[$jenis['key']] }}</span>
                            @endif
                            @if($jenisTrashed[$jenis['key']] > 0)
                            <span class="text-xs text-slate-500 font-semibold" title="Di trash">🗑 {{ $jenisTrashed[$jenis['key']] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Statistik Tamu --}}
    <div class="dash-panel p-6 fade-up stagger-3">
        <h2 class="font-bold text-slate-300 text-sm mb-1">Statistik Tamu</h2>
        @if($isSuperAdmin && $revenueChartDays)
        {{-- Tab switcher (CSS-only) --}}
        <div class="flex gap-2 mb-4 mt-3">
            <span id="tab-guest-lbl" class="text-xs font-bold text-teal-400 cursor-pointer" onclick="showTab('guest')">Tamu</span>
            <span class="text-xs text-slate-700">&middot;</span>
            <span id="tab-rev-lbl" class="text-xs font-semibold text-slate-500 hover:text-emerald-400 cursor-pointer transition" onclick="showTab('rev')">Pendapatan 7 Hari</span>
        </div>
        @else
        <div class="mb-4 mt-1"></div>
        @endif

        {{-- Tamu panel --}}
        <div id="tab-guest" class="space-y-4">
            @php
                $guestRows = [
                    ['label' => 'Total tamu',       'val' => $totalGuests,     'pct' => 100,   'color' => 'bg-blue-500'],
                    ['label' => 'Sudah RSVP',        'val' => $guestsRsvp,     'pct' => $totalGuests ? round(($guestsRsvp/$totalGuests)*100)     : 0, 'color' => 'bg-violet-500'],
                    ['label' => 'Konfirmasi hadir',  'val' => $guestsAttending,'pct' => $totalGuests ? round(($guestsAttending/$totalGuests)*100) : 0, 'color' => 'bg-emerald-500'],
                ];
            @endphp
            @foreach($guestRows as $row)
            <div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-slate-400">{{ $row['label'] }}</span>
                    <span class="font-bold text-slate-200">{{ $row['val'] }}</span>
                </div>
                <div class="prog-track">
                    <div class="prog-fill {{ $row['color'] }}" style="width:{{ $row['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
            @if($totalGuests > 0)
            <div class="pt-3 border-t border-white/5 flex justify-between text-xs">
                <span class="text-slate-500">Tingkat respon</span>
                <span class="text-slate-200 font-bold">{{ round(($guestsRsvp/$totalGuests)*100) }}%</span>
            </div>
            @endif
            @if($isSuperAdmin)
            <div class="pt-3 border-t border-white/5 space-y-1.5">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Pendapatan bulan ini</span>
                    <span class="text-emerald-400 font-bold">Rp {{ number_format($revenueMonth, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Bulan lalu</span>
                    <span class="text-slate-400 font-bold">Rp {{ number_format($revenueLastMonth, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>{{-- /tab-guest --}}

        {{-- Revenue 7-day chart (super-admin only) --}}
        @if($isSuperAdmin && $revenueChartDays)
        <div id="tab-rev" class="hidden space-y-3">
            <div class="flex items-end gap-1.5 h-24 mt-2">
                @php $revTotal = $revenueChartDays->sum('amount'); @endphp
                @foreach($revenueChartDays as $rd)
                @php
                    $rPct = $revenueChartMax > 0 ? round(($rd['amount'] / $revenueChartMax) * 100) : 0;
                    $rToday = $rd['date'] === now()->toDateString();
                @endphp
                <div class="chart-col">
                    @if($rd['amount'] > 0)
                    <span class="text-[9px] font-bold {{ $rToday ? 'text-emerald-300' : 'text-slate-600' }} leading-none" style="white-space:nowrap">
                        {{ number_format($rd['amount']/1000, 0) }}k
                    </span>
                    @else
                    <span></span>
                    @endif
                    <div class="chart-bar-wrap">
                        <div class="{{ $rToday ? 'chart-bar-today' : '' }} chart-bar"
                             style="height:{{ max(4, $rPct) }}%; background: {{ $rToday ? '' : 'linear-gradient(to top,#10b981,#34d399)' }}"></div>
                    </div>
                    <span class="text-xs {{ $rToday ? 'text-emerald-400 font-bold' : 'text-slate-500' }}">{{ $rd['label'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="pt-2 border-t border-white/5 flex justify-between text-xs">
                <span class="text-slate-500">Total 7 hari</span>
                <span class="text-emerald-400 font-bold">Rp {{ number_format($revTotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Hari ini</span>
                <span class="text-emerald-300 font-bold">Rp {{ number_format($revenueChartDays->last()['amount'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script @nonce>
function showTab(t) {
    const g = document.getElementById('tab-guest');
    const r = document.getElementById('tab-rev');
    const gl = document.getElementById('tab-guest-lbl');
    const rl = document.getElementById('tab-rev-lbl');
    if (!g || !r) return;
    if (t === 'rev') {
        g.classList.add('hidden'); r.classList.remove('hidden');
        if (gl) { gl.classList.remove('text-teal-400'); gl.classList.add('text-slate-500'); }
        if (rl) { rl.classList.remove('text-slate-500'); rl.classList.add('text-emerald-400'); }
    } else {
        r.classList.add('hidden'); g.classList.remove('hidden');
        if (rl) { rl.classList.remove('text-emerald-400'); rl.classList.add('text-slate-500'); }
        if (gl) { gl.classList.remove('text-slate-500'); gl.classList.add('text-teal-400'); }
    }
}
</script>
@endpush

{{-- ══════ SECTION 3 — PERLU PERHATIAN ══════════════════════════════════ --}}
<div class="section-label fade-up stagger-3">Perlu Perhatian</div>
<div class="grid lg:grid-cols-2 gap-4 mb-8">

    {{-- Butuh Tindakan --}}
    <div class="dash-panel p-6 fade-up stagger-3">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-200 text-sm">Butuh Tindakan</h2>
            @if($pendingConfirm + $pendingNew + $rejectedCount > 0)
            <span class="px-2.5 py-0.5 bg-red-900/30 border border-red-700/30 text-red-400 text-xs font-bold rounded-full">
                {{ $pendingConfirm + $pendingNew + $rejectedCount }}
            </span>
            @else
            <span class="px-2.5 py-0.5 bg-emerald-900/20 border border-emerald-700/20 text-emerald-500 text-xs font-semibold rounded-full">Semua OK</span>
            @endif
        </div>
        @if($actionOrders->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <span class="text-3xl mb-3">&#127881;</span>
            <p class="text-slate-400 text-sm font-semibold">Semua sudah diproses!</p>
            <p class="text-slate-600 text-xs mt-1">Tidak ada pesanan yang membutuhkan tindakan.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($actionOrders as $ao)
            @php
                $aoHref = $ao->payment_status === 'ditolak'
                    ? route('admin.orders.index', ['payment' => 'ditolak'])
                    : route('admin.orders.index');
                $aoBg   = match($ao->payment_status) {
                    'menunggu_konfirmasi' => 'bg-amber-900/40',
                    'ditolak'             => 'bg-red-900/40',
                    default               => 'bg-blue-900/40',
                };
                $aoIcon = match($ao->payment_status) {
                    'menunggu_konfirmasi' => '&#128179;',
                    'ditolak'             => '&#10060;',
                    default               => '&#127381;',
                };
                $aoDesc = match($ao->payment_status) {
                    'menunggu_konfirmasi' => 'Bukti bayar menunggu konfirmasi',
                    'ditolak'             => 'Pembayaran ditolak — perlu tindak lanjut',
                    default               => 'Pesanan baru, belum terhubung undangan',
                };
                $aoBadgeCls = match($ao->payment_status) {
                    'menunggu_konfirmasi' => 'bg-amber-900/40 text-amber-300 border border-amber-700/30',
                    'ditolak'             => 'bg-red-900/40 text-red-300 border border-red-700/30',
                    default               => 'bg-blue-900/40 text-blue-300 border border-blue-700/30',
                };
                $aoBadgeLabel = match($ao->payment_status) {
                    'menunggu_konfirmasi' => 'Konfirmasi',
                    'ditolak'             => 'Hubungi',
                    default               => 'Proses',
                };
            @endphp
            <a href="{{ $aoHref }}" class="item-row block no-underline">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 {{ $aoBg }}">
                    {!! $aoIcon !!}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-slate-200 truncate">{{ $ao->customer_name }}</p>
                    <p class="text-xs text-slate-500 truncate">
                        {{ $aoDesc }} &middot; {{ $ao->created_at->diffForHumans() }}
                    </p>
                </div>
                <span class="shrink-0 px-2 py-1 rounded-lg text-xs font-bold {{ $aoBadgeCls }}">
                    {{ $aoBadgeLabel }}
                </span>
            </a>
            @endforeach
        </div>
        <a href="{{ route('admin.orders.index') }}"
           class="mt-4 inline-block text-xs text-violet-400 hover:text-violet-300 font-semibold transition">Lihat semua pesanan &rarr;</a>

        {{-- ── Pesanan Terbengkalai (> 48 jam belum bayar) ───────────── --}}
        @if($stuckOrders->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-white/5">
            <p class="text-xs font-bold text-amber-500/80 uppercase tracking-wider mb-2">&#9749; Terbengkalai &gt; 48 Jam</p>
            <div class="space-y-2">
                @foreach($stuckOrders as $so)
                <a href="{{ route('admin.orders.index') }}" class="item-row block no-underline">
                    <div class="w-8 h-8 rounded-xl bg-amber-900/30 flex items-center justify-center text-sm shrink-0">&#10067;</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-slate-200 truncate">{{ $so->customer_name }}</p>
                        <p class="text-xs text-slate-500 truncate">
                            Belum bayar &middot; {{ $so->created_at->diffForHumans() }}
                            @if($so->wedding) &middot; {{ $so->wedding->bride_name }} @endif
                        </p>
                    </div>
                    <span class="shrink-0 px-2 py-1 rounded-lg text-xs font-bold bg-amber-900/30 text-amber-400 border border-amber-700/30">
                        Follow Up
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>

    {{-- Undangan Mendatang --}}
    <div class="dash-panel p-6 fade-up stagger-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-200 text-sm">Undangan Mendatang</h2>
            <span class="text-xs text-slate-600">30 hari ke depan</span>
        </div>
        @if($upcomingWeddings->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <span class="text-3xl mb-3">&#128197;</span>
            <p class="text-slate-400 text-sm font-semibold">Tidak ada acara mendatang</p>
            <p class="text-slate-600 text-xs mt-1">Tidak ada undangan dengan tanggal acara dalam 30 hari ke depan.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($upcomingWeddings as $uw)
            @php
                $daysLeft = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($uw->event_date), false);
                $badgeCls = $daysLeft === 0 ? 'days-today' : ($daysLeft <= 3 ? 'days-close' : 'days-normal');
                $badgeTxt = $daysLeft === 0 ? 'Hari Ini' : ($daysLeft === 1 ? 'Besok' : $daysLeft . ' hari');
                $editRoute = str_starts_with($uw->template ?? '', 'birthday')
                    ? route('admin.birthdays.edit', $uw)
                    : route('admin.weddings.edit', $uw);
                $uwIcon = str_starts_with($uw->template ?? '', 'birthday') ? '&#127874;' : '&#128141;';
            @endphp
            <div class="item-row">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0
                    {{ $uw->isVip() ? 'bg-yellow-900/40' : ($uw->isPremium() ? 'bg-amber-900/40' : 'bg-blue-900/40') }}">
                    {!! $uwIcon !!}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-slate-200 truncate">
                        {{ $uw->bride_name }}{{ $uw->groom_name ? ' & '.$uw->groom_name : '' }}
                    </p>
                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($uw->event_date)->translatedFormat('d M Y') }}</p>
                </div>
                <span class="days-badge {{ $badgeCls }}">{{ $badgeTxt }}</span>
                <a href="{{ $editRoute }}" class="text-slate-600 hover:text-slate-300 transition text-sm">&#9999;&#65039;</a>
            </div>
            @endforeach
        </div>
        <a href="{{ route('admin.weddings.index') }}"
           class="mt-4 inline-block text-xs text-blue-400 hover:text-blue-300 font-semibold transition">Semua undangan &rarr;</a>
        @endif
    </div>

</div>

{{-- ══════ SECTION 4 — AKTIVITAS TERBARU ════════════════════════════════ --}}
<div class="section-label fade-up stagger-4">Aktivitas Terbaru</div>
<div class="grid lg:grid-cols-2 gap-4 mb-8">

    {{-- Undangan Terbaru --}}
    <div class="dash-panel p-6 fade-up stagger-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-200 text-sm">Undangan Terbaru</h2>
            <a href="{{ route('admin.weddings.index') }}" class="text-xs text-slate-500 hover:text-slate-300 transition">Semua &rarr;</a>
        </div>
        @if($recentWeddings->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <span class="text-3xl mb-3">&#128429;</span>
            <p class="text-slate-400 text-sm">Belum ada undangan.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($recentWeddings as $rw)
            @php
                $rwRoute = str_starts_with($rw->template ?? '', 'birthday')
                    ? route('admin.birthdays.edit', $rw)
                    : (str_starts_with($rw->template ?? '', 'greeting')
                        ? route('admin.greetings.edit', $rw)
                        : route('admin.weddings.edit', $rw));
                $rwIcon = str_starts_with($rw->template ?? '', 'birthday') ? '&#127874;'
                    : (str_starts_with($rw->template ?? '', 'greeting') ? '&#128140;' : '&#128141;');
            @endphp
            <div class="item-row">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0
                    {{ $rw->isVip() ? 'bg-yellow-900/40' : ($rw->isPremium() ? 'bg-amber-900/40' : ($rw->isTrial() ? 'bg-white/5' : 'bg-blue-900/40')) }}">
                    {!! $rwIcon !!}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-slate-200 truncate">
                        {{ $rw->bride_name }}{{ $rw->groom_name ? ' & '.$rw->groom_name : '' }}
                    </p>
                    <p class="text-xs text-slate-500 truncate">{{ $rw->template }} &middot; {{ $rw->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-lg font-bold
                    {{ $rw->isVip() ? 'bg-yellow-900/40 text-yellow-300 border border-yellow-700/30' : ($rw->isPremium() ? 'bg-amber-900/40 text-amber-300 border border-amber-700/30' : ($rw->isTrial() ? 'bg-white/5 text-slate-500' : 'bg-blue-900/40 text-blue-300 border border-blue-700/30')) }}">
                    {{ $rw->isVip() ? 'VIP' : ($rw->isPremium() ? 'Premium' : ($rw->isTrial() ? 'Trial' : 'Basic')) }}
                </span>
                <a href="{{ $rwRoute }}" class="text-slate-600 hover:text-slate-300 transition text-sm shrink-0">&#9999;&#65039;</a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- RSVP Terbaru --}}
    <div class="dash-panel p-6 fade-up stagger-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-200 text-sm">RSVP Terbaru</h2>
            <span class="px-2 py-0.5 bg-green-900/20 border border-green-700/20 text-green-500 text-xs font-bold rounded-full">{{ $guestsRsvp }} total</span>
        </div>
        @if($recentRsvps->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <span class="text-3xl mb-3">&#128236;</span>
            <p class="text-slate-400 text-sm">Belum ada RSVP.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($recentRsvps as $rr)
            <div class="item-row">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0
                    {{ $rr->is_attending === true ? 'bg-emerald-900/40' : ($rr->is_attending === false ? 'bg-red-900/30' : 'bg-white/5') }}">
                    {!! $rr->is_attending === true ? '&#10004;&#65039;' : ($rr->is_attending === false ? '&#10006;&#65039;' : '&#10067;') !!}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-slate-200 truncate">{{ $rr->guest_name }}</p>
                    <p class="text-xs text-slate-500 truncate">
                        {{ $rr->wedding ? ($rr->wedding->bride_name . ($rr->wedding->groom_name ? ' & '.$rr->wedding->groom_name : '')) : '—' }}
                        @if($rr->group_name) &middot; {{ $rr->group_name }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($rr->pax)
                    <span class="text-xs text-slate-500">{{ $rr->pax }} pax</span>
                    @endif
                    <span class="text-xs text-slate-600">{{ $rr->replied_at->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- ══════ SECTION 5 — VIP ROYAL ══════════════════════════════════════════ --}}
@if($vipWeddings->isNotEmpty())
<div class="section-label fade-up stagger-5">VIP Royal</div>
<div class="space-y-2 mb-8 fade-up stagger-5">
    @foreach($vipWeddings as $vw)
    <div class="vip-row">
        <div class="flex items-center gap-2.5 flex-1 min-w-0">
            <div class="w-8 h-8 rounded-xl bg-yellow-900/50 flex items-center justify-center text-sm shrink-0">&#9819;</div>
            <div class="min-w-0">
                <p class="font-bold text-slate-200 text-sm truncate">
                    {{ $vw->bride_name }}{{ $vw->groom_name ? ' & '.$vw->groom_name : '' }}
                </p>
                @if($vw->event_date)
                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($vw->event_date)->translatedFormat('d M Y') }}</p>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-1.5 shrink-0">
            <a href="{{ route('admin.vip.dashboard', ['wedding_id' => $vw->id]) }}"
               class="px-3 py-1.5 rounded-lg bg-yellow-900/30 border border-yellow-700/30 text-yellow-300 text-xs font-bold hover:bg-yellow-900/50 transition">Dashboard</a>
            <a href="{{ route('admin.vip.qr-codes', ['wedding_id' => $vw->id]) }}"
               class="px-3 py-1.5 rounded-lg bg-indigo-900/30 border border-indigo-700/30 text-indigo-300 text-xs font-bold hover:bg-indigo-900/50 transition">QR Code</a>
            <a href="{{ route('admin.vip.scan', ['wedding_id' => $vw->id]) }}"
               class="px-3 py-1.5 rounded-lg bg-teal-900/30 border border-teal-700/30 text-teal-300 text-xs font-bold hover:bg-teal-900/50 transition">Scan</a>
            <a href="{{ route('admin.vip.guestbook', ['wedding_id' => $vw->id]) }}"
               class="px-3 py-1.5 rounded-lg bg-violet-900/30 border border-violet-700/30 text-violet-300 text-xs font-bold hover:bg-violet-900/50 transition">Guestbook</a>
            <a href="{{ route('admin.vip.rsvp-live', $vw->id) }}"
               class="px-3 py-1.5 rounded-lg bg-emerald-900/30 border border-emerald-700/30 text-emerald-300 text-xs font-bold hover:bg-emerald-900/50 transition">RSVP Live</a>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ══════ SECTION 5b — PREMIUM ════════════════════════════════════════════ --}}
@if($premiumWeddings->isNotEmpty())
<div class="section-label fade-up stagger-5">Premium</div>
<div class="space-y-2 mb-8 fade-up stagger-5">
    @foreach($premiumWeddings as $pw)
    @php
        $premiumToken = \App\Models\Order::where('wedding_id', $pw->id)->latest()->value('public_token');
    @endphp
    <div class="vip-row" style="border-color: rgba(99,179,237,.15); background: rgba(30,64,90,.25);">
        <div class="flex items-center gap-2.5 flex-1 min-w-0">
            <div class="w-8 h-8 rounded-xl bg-sky-900/50 flex items-center justify-center text-sm shrink-0">&#11088;</div>
            <div class="min-w-0">
                <p class="font-bold text-slate-200 text-sm truncate">
                    {{ $pw->bride_name }}{{ $pw->groom_name ? ' & '.$pw->groom_name : '' }}
                </p>
                @if($pw->event_date)
                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($pw->event_date)->translatedFormat('d M Y') }}</p>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-1.5 shrink-0">
            @if($premiumToken)
            <a href="{{ url('/my/'.$premiumToken) }}"
               target="_blank"
               class="px-3 py-1.5 rounded-lg bg-sky-600 border border-sky-500/50 text-white text-xs font-bold hover:bg-sky-500 transition">Dashboard</a>
            <a href="{{ url('/my/'.$premiumToken.'/tamu') }}"
               target="_blank"
               class="px-3 py-1.5 rounded-lg bg-emerald-900/30 border border-emerald-700/30 text-emerald-300 text-xs font-bold hover:bg-emerald-900/50 transition">Tamu</a>
            @else
            <span title="Belum ada order terhubung"
                  class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700/30 text-slate-500 text-xs font-bold cursor-not-allowed opacity-50">Dashboard</span>
            @endif
            <a href="{{ route('admin.guests.index', ['wedding_id' => $pw->id]) }}"
               class="px-3 py-1.5 rounded-lg bg-violet-900/30 border border-violet-700/30 text-violet-300 text-xs font-bold hover:bg-violet-900/50 transition">Daftar Tamu</a>
            <a href="{{ url('/'.$pw->slug) }}"
               target="_blank"
               class="px-3 py-1.5 rounded-lg bg-slate-700/40 border border-slate-600/30 text-slate-300 text-xs font-bold hover:bg-slate-700/60 transition">Lihat</a>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ══════ SECTION 6 — AKSI CEPAT ════════════════════════════════════════ --}}
<div class="section-label fade-up stagger-6">Aksi Cepat</div>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4 fade-up stagger-6">

    <a href="{{ route('admin.weddings.create') }}"
       class="qa-card hover:border-violet-500/30 hover:bg-violet-900/10">
        <span class="text-2xl">&#9999;&#65039;</span>
        <span class="text-xs font-bold text-slate-300">Buat Undangan</span>
    </a>

    <a href="{{ route('admin.orders.index', ['date' => 'today']) }}"
       class="qa-card hover:border-orange-500/30 hover:bg-orange-900/10">
        <span class="text-2xl">&#128203;</span>
        <span class="text-xs font-bold text-slate-300">Pesanan Hari Ini</span>
    </a>

    <a href="{{ route('admin.guests.import') }}"
       class="qa-card hover:border-emerald-500/30 hover:bg-emerald-900/10">
        <span class="text-2xl">&#128194;</span>
        <span class="text-xs font-bold text-slate-300">Import Tamu</span>
    </a>

    <a href="{{ route('admin.orders.index') }}#qris"
       class="qa-card hover:border-blue-500/30 hover:bg-blue-900/10">
        <span class="text-2xl">&#128241;</span>
        <span class="text-xs font-bold text-slate-300">Upload QRIS</span>
    </a>

    <a href="{{ route('admin.weddings.index') }}"
       class="qa-card hover:border-cyan-500/30 hover:bg-cyan-900/10">
        <span class="text-2xl">&#128141;</span>
        <span class="text-xs font-bold text-slate-300">Semua Undangan</span>
    </a>

    <a href="{{ route('admin.orders.index', ['status' => 'baru']) }}"
       class="qa-card hover:border-red-500/30 hover:bg-red-900/10 {{ $pendingConfirm > 0 ? 'border-red-700/30' : '' }}">
        <span class="text-2xl">&#128276;</span>
        <span class="text-xs font-bold {{ $pendingConfirm > 0 ? 'text-red-400' : 'text-slate-300' }}">
            Konfirmasi{{ $pendingConfirm > 0 ? ' ('.$pendingConfirm.')' : '' }}
        </span>
    </a>

    @if($rejectedCount > 0)
    <a href="{{ route('admin.orders.index', ['payment' => 'ditolak']) }}"
       class="qa-card hover:border-red-500/30 hover:bg-red-900/10 border-red-800/40">
        <span class="text-2xl">&#10060;</span>
        <span class="text-xs font-bold text-red-400">Ditolak ({{ $rejectedCount }})</span>
    </a>
    @endif

</div>

@endsection
