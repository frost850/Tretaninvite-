@extends('admin.layout')

@section('title', 'Statistik')

@push('styles')
<style @nonce>
.section-label {
    display: flex; align-items: center; gap: 10px;
    font-size: .7rem; font-weight: 800; letter-spacing: .08em;
    text-transform: uppercase; color: #475569; margin-bottom: 14px;
}
.section-label::after {
    content: ''; flex: 1; height: 1px;
    background: rgba(255,255,255,.06);
}

/* ── Stat card ── */
.s-card {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px; padding: 22px;
    transition: transform .2s, box-shadow .2s;
}
.s-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,.4); }

/* ── Panel ── */
.s-panel {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px; padding: 24px;
}

/* ── Progress row ── */
.prog-row { margin-bottom: 18px; }
.prog-row:last-child { margin-bottom: 0; }
.prog-track { height: 8px; background: rgba(255,255,255,.06); border-radius: 99px; overflow: hidden; margin-top: 6px; }
.prog-fill  { height: 100%; border-radius: 99px; transition: width 1s cubic-bezier(.4,2,.55,.9); }

/* ── Status breakdown bar ── */
.breakdown-bar { display: flex; height: 14px; border-radius: 99px; overflow: hidden; gap: 2px; }
.breakdown-seg { transition: flex .8s ease; min-width: 4px; border-radius: 2px; }

/* ── Bar chart ── */
.bc-col { display: flex; flex-direction: column; align-items: center; gap: 4px; flex: 1; min-width: 0; }
.bc-wrap { width: 100%; display: flex; align-items: flex-end; height: 90px; }
.bc-bar { width: 100%; border-radius: 3px 3px 0 0; transition: height .8s cubic-bezier(.4,2,.55,.9); }
</style>
@endpush

@section('content')

{{-- ══════ HEADER ════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6 fade-up stagger-1">
    <div>
        <p class="text-slate-500 text-xs">Gambaran lengkap bisnis</p>
        <h1 class="text-2xl font-black text-slate-100 mt-0.5">Statistik</h1>
        <p class="text-slate-500 text-sm mt-0.5">Distribusi undangan, pembayaran, dan tamu secara keseluruhan.</p>
    </div>
    <p class="text-slate-600 text-xs mt-1">🕐 <span id="realtime-clock"></span></p>
</div>

{{-- ══════ SUMMARY CARDS ══════════════════════════════════ --}}
@php
    $totalAktif = array_sum($paketActive);
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8 fade-up stagger-1">
    <div class="s-card">
        <div class="w-9 h-9 rounded-xl bg-violet-500/15 flex items-center justify-center text-xl mb-3">📋</div>
        <p class="text-3xl font-black text-slate-100">{{ $totalUndangan }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Undangan</p>
        <p class="text-xs text-violet-400 mt-1 font-semibold">{{ $totalAktif }} aktif</p>
    </div>
    <div class="s-card">
        <div class="w-9 h-9 rounded-xl bg-emerald-500/15 flex items-center justify-center text-xl mb-3">💰</div>
        <p class="text-xl font-black text-emerald-400 leading-snug">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Pendapatan</p>
        <p class="text-xs text-slate-600 mt-1 font-semibold">dari {{ $payStatus['lunas'] }} pesanan lunas</p>
    </div>
    <div class="s-card">
        <div class="w-9 h-9 rounded-xl bg-orange-500/15 flex items-center justify-center text-xl mb-3">📦</div>
        <p class="text-3xl font-black text-slate-100">{{ $totalOrders }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Pesanan</p>
        <p class="text-xs text-{{ $payStatus['menunggu_konfirmasi'] > 0 ? 'amber' : 'slate' }}-400 mt-1 font-semibold">
            {{ $payStatus['menunggu_konfirmasi'] }} pending konfirmasi
        </p>
    </div>
    <div class="s-card">
        <div class="w-9 h-9 rounded-xl bg-blue-500/15 flex items-center justify-center text-xl mb-3">👥</div>
        <p class="text-3xl font-black text-slate-100">{{ $totalGuests }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Tamu</p>
        <p class="text-xs text-blue-400 mt-1 font-semibold">
            @if($totalGuests > 0){{ round(($guestsRsvp/$totalGuests)*100) }}% sudah RSVP@else—@endif
        </p>
    </div>
</div>

{{-- ══════ JENIS + PAKET ══════════════════════════════════ --}}
<div class="grid lg:grid-cols-2 gap-4 mb-8">

    {{-- Distribusi Jenis Undangan --}}
    <div class="s-panel fade-up stagger-2">
        <h2 class="font-bold text-slate-200 text-sm mb-5">💍 Distribusi Jenis Undangan</h2>
        @php
            $jenisList = [
                ['key' => 'wedding',    'label' => 'Wedding',      'emoji' => '💍', 'color' => 'bg-violet-500',  'text' => 'text-violet-400'],
                ['key' => 'birthday',   'label' => 'Birthday',     'emoji' => '🎂', 'color' => 'bg-pink-500',    'text' => 'text-pink-400'],
                ['key' => 'greeting',   'label' => 'Greeting Card','emoji' => '💌', 'color' => 'bg-teal-500',    'text' => 'text-teal-400'],
            ];
        @endphp
        @foreach($jenisList as $j)
        @php
            $pct = $totalUndangan > 0 ? round(($jenisCount[$j['key']] / $totalUndangan) * 100) : 0;
        @endphp
        <div class="prog-row">
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-300 font-semibold flex items-center gap-2">
                    <span>{{ $j['emoji'] }}</span> {{ $j['label'] }}
                </span>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500">{{ $jenisActive[$j['key']] }} aktif</span>
                    <span class="font-black text-slate-100">{{ $jenisCount[$j['key']] }}</span>
                    <span class="text-xs font-bold {{ $j['text'] }} w-10 text-right">{{ $pct }}%</span>
                </div>
            </div>
            <div class="prog-track">
                <div class="prog-fill {{ $j['color'] }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @endforeach

        <div class="mt-5 pt-4 border-t border-white/5">
            <p class="text-xs text-slate-500 mb-3 font-semibold uppercase tracking-wider">Pendapatan per Jenis</p>
            @foreach($jenisList as $j)
            <div class="flex justify-between text-xs py-1.5 border-b border-white/4 last:border-0">
                <span class="text-slate-400 flex items-center gap-1.5">{{ $j['emoji'] }} {{ $j['label'] }}</span>
                <span class="font-bold {{ $j['text'] }}">Rp {{ number_format($revenueByJenis[$j['key']], 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Distribusi Paket --}}
    <div class="s-panel fade-up stagger-3">
        <h2 class="font-bold text-slate-200 text-sm mb-5">🎁 Distribusi Paket</h2>
        @php
            $paketList = [
                ['key' => 'vip',     'label' => 'VIP Royal', 'color' => 'bg-amber-400',  'text' => 'text-amber-400'],
                ['key' => 'premium', 'label' => 'Premium',   'color' => 'bg-orange-400', 'text' => 'text-orange-400'],
                ['key' => 'basic',   'label' => 'Basic',     'color' => 'bg-blue-400',   'text' => 'text-blue-400'],
                ['key' => 'trial',   'label' => 'Trial',     'color' => 'bg-slate-400',  'text' => 'text-slate-400'],
            ];
            $totalPaket = array_sum($paketCount);
        @endphp
        @foreach($paketList as $p)
        @php
            $pct = $totalPaket > 0 ? round(($paketCount[$p['key']] / $totalPaket) * 100) : 0;
        @endphp
        <div class="prog-row">
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-300 font-semibold">{{ $p['label'] }}</span>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500">{{ $paketActive[$p['key']] }} aktif</span>
                    <span class="font-black text-slate-100">{{ $paketCount[$p['key']] }}</span>
                    <span class="text-xs font-bold {{ $p['text'] }} w-10 text-right">{{ $pct }}%</span>
                </div>
            </div>
            <div class="prog-track">
                <div class="prog-fill {{ $p['color'] }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @endforeach

        <div class="mt-5 pt-4 border-t border-white/5">
            <p class="text-xs text-slate-500 mb-3 font-semibold uppercase tracking-wider">Pendapatan per Paket</p>
            @foreach($paketList as $p)
            <div class="flex justify-between text-xs py-1.5 border-b border-white/4 last:border-0">
                <span class="text-slate-400">{{ $p['label'] }}</span>
                <span class="font-bold {{ $p['text'] }}">Rp {{ number_format($revenueByPaket[$p['key']], 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ══════ STATUS PEMBAYARAN ══════════════════════════════ --}}
<div class="section-label fade-up stagger-2">Status Pembayaran (All-Time)</div>
<div class="s-panel mb-8 fade-up stagger-2">
    @php
        $payLabels = [
            ['key' => 'lunas',               'label' => 'Lunas',             'color' => 'bg-emerald-500', 'text' => 'text-emerald-400', 'bg' => 'bg-emerald-500/10 border-emerald-500/25'],
            ['key' => 'menunggu_konfirmasi',  'label' => 'Menunggu Konfirmasi','color' => 'bg-amber-400',  'text' => 'text-amber-400',   'bg' => 'bg-amber-500/10 border-amber-500/25'],
            ['key' => 'belum_bayar',          'label' => 'Belum Bayar',       'color' => 'bg-slate-500',  'text' => 'text-slate-400',   'bg' => 'bg-slate-500/10 border-slate-500/25'],
            ['key' => 'ditolak',              'label' => 'Ditolak',           'color' => 'bg-red-500',    'text' => 'text-red-400',     'bg' => 'bg-red-500/10 border-red-500/25'],
        ];
    @endphp

    {{-- Breakdown bar --}}
    <div class="breakdown-bar mb-5">
        @foreach($payLabels as $s)
        @php $flex = $totalOrders > 0 ? $payStatus[$s['key']] : 0; @endphp
        <div class="breakdown-seg {{ $s['color'] }}" style="flex: {{ max($flex, 0.1) }}"></div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach($payLabels as $s)
        @php $pct = $totalOrders > 0 ? round(($payStatus[$s['key']] / $totalOrders) * 100) : 0; @endphp
        <div class="px-4 py-3 rounded-xl border {{ $s['bg'] }}">
            <p class="text-2xl font-black {{ $s['text'] }}">{{ $payStatus[$s['key']] }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $s['label'] }}</p>
            <p class="text-xs font-bold {{ $s['text'] }} mt-1">{{ $pct }}%</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ══════ TAMU + REVENUE BY JENIS ═══════════════════════ --}}
<div class="grid lg:grid-cols-2 gap-4 mb-8">

    {{-- Statistik Tamu --}}
    <div class="s-panel fade-up stagger-3">
        <h2 class="font-bold text-slate-200 text-sm mb-5">👥 Statistik Tamu</h2>
        @php
            $tamuRows = [
                ['label' => 'Total Tamu',          'val' => $totalGuests,     'pct' => 100,   'color' => 'bg-blue-500'],
                ['label' => 'Sudah RSVP',           'val' => $guestsRsvp,     'pct' => $totalGuests ? round(($guestsRsvp/$totalGuests)*100) : 0,      'color' => 'bg-violet-500'],
                ['label' => 'Konfirmasi Hadir',     'val' => $guestsAttending,'pct' => $totalGuests ? round(($guestsAttending/$totalGuests)*100) : 0,  'color' => 'bg-emerald-500'],
                ['label' => 'Tidak Hadir',          'val' => $guestsDeclined, 'pct' => $totalGuests ? round(($guestsDeclined/$totalGuests)*100) : 0,   'color' => 'bg-red-500'],
                ['label' => 'Belum Membalas',       'val' => $guestsPending,  'pct' => $totalGuests ? round(($guestsPending/$totalGuests)*100) : 0,    'color' => 'bg-slate-500'],
                ['label' => 'Check-In',             'val' => $guestsCheckedIn,'pct' => $totalGuests ? round(($guestsCheckedIn/$totalGuests)*100) : 0,  'color' => 'bg-teal-500'],
            ];
        @endphp
        @foreach($tamuRows as $r)
        <div class="prog-row">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">{{ $r['label'] }}</span>
                <div class="flex items-center gap-2">
                    <span class="font-black text-slate-200">{{ $r['val'] }}</span>
                    <span class="text-slate-500 w-8 text-right">{{ $r['pct'] }}%</span>
                </div>
            </div>
            <div class="prog-track" style="height: 6px;">
                <div class="prog-fill {{ $r['color'] }}" style="width: {{ $r['pct'] }}%"></div>
            </div>
        </div>
        @endforeach

        @if($totalGuests > 0)
        <div class="mt-4 pt-4 border-t border-white/5 grid grid-cols-2 gap-3">
            <div class="text-center px-3 py-2 rounded-xl bg-white/3 border border-white/6">
                <p class="text-xl font-black text-violet-400">{{ round(($guestsRsvp/$totalGuests)*100) }}%</p>
                <p class="text-xs text-slate-500 mt-0.5">Tingkat Respons</p>
            </div>
            <div class="text-center px-3 py-2 rounded-xl bg-white/3 border border-white/6">
                <p class="text-xl font-black text-emerald-400">
                    {{ $guestsRsvp > 0 ? round(($guestsAttending/$guestsRsvp)*100) : 0 }}%
                </p>
                <p class="text-xs text-slate-500 mt-0.5">Yang Hadir (dari RSVP)</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Revenue by Jenis (donut style) --}}
    <div class="s-panel fade-up stagger-4">
        <h2 class="font-bold text-slate-200 text-sm mb-5">💎 Kontribusi Pendapatan per Jenis</h2>
        @php
            $jenisList2 = [
                ['key' => 'wedding',    'label' => 'Wedding',       'emoji' => '💍', 'color' => 'bg-violet-500', 'text' => 'text-violet-400'],
                ['key' => 'birthday',   'label' => 'Birthday',      'emoji' => '🎂', 'color' => 'bg-pink-500',   'text' => 'text-pink-400'],
                ['key' => 'greeting',   'label' => 'Greeting Card', 'emoji' => '💌', 'color' => 'bg-teal-500',   'text' => 'text-teal-400'],
            ];
        @endphp

        {{-- Stacked bar --}}
        <div class="breakdown-bar mb-5" style="height: 10px;">
            @foreach($jenisList2 as $j)
            @php $flex = $totalRevenue > 0 ? $revenueByJenis[$j['key']] : 0; @endphp
            <div class="breakdown-seg {{ $j['color'] }}" style="flex: {{ max($flex, 0.001) }}"></div>
            @endforeach
        </div>

        <div class="space-y-3">
            @foreach($jenisList2 as $j)
            @php $pct = $totalRevenue > 0 ? round(($revenueByJenis[$j['key']]/$totalRevenue)*100) : 0; @endphp
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full {{ $j['color'] }} shrink-0"></div>
                <span class="text-sm text-slate-300 flex items-center gap-1.5 flex-1">{{ $j['emoji'] }} {{ $j['label'] }}</span>
                <span class="text-xs text-slate-500">{{ $pct }}%</span>
                <span class="text-sm font-bold {{ $j['text'] }}">Rp {{ number_format($revenueByJenis[$j['key']], 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="mt-5 pt-4 border-t border-white/5">
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 font-semibold">Total All-Time</span>
                <span class="font-black text-emerald-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════ GRAFIK PENDAPATAN 12 BULAN ════════════════════ --}}
<div class="section-label fade-up stagger-3">Tren 12 Bulan Terakhir</div>
<div class="s-panel mb-6 fade-up stagger-3">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-bold text-slate-300 text-sm">Pendapatan & Jumlah Pesanan</h2>
        <div class="flex items-center gap-4 text-xs text-slate-500">
            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-emerald-500"></span>Pendapatan</span>
            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-violet-500 opacity-50"></span>Pesanan</span>
        </div>
    </div>
    <div class="flex items-end gap-1.5">
        @foreach($monthly as $m)
        @php
            $revPct   = $monthlyMaxRevenue > 0 ? round(($m['revenue'] / $monthlyMaxRevenue) * 100) : 0;
            $countPct = $monthlyMaxCount   > 0 ? round(($m['count']   / $monthlyMaxCount)   * 100) : 0;
        @endphp
        <div class="bc-col">
            @if($m['revenue'] > 0)
            <span class="text-xs text-emerald-400 font-bold" style="font-size:9px">
                {{ number_format($m['revenue']/1000, 0) }}K
            </span>
            @else
            <span class="text-xs text-slate-700" style="font-size:9px">-</span>
            @endif
            <div class="bc-wrap relative">
                {{-- Revenue bar --}}
                <div class="bc-bar {{ $m['isThisMonth'] ? 'bg-emerald-500' : 'bg-emerald-500/40' }}"
                     style="height: {{ max(4, $revPct) }}%; position: absolute; bottom: 0; left: 0; width: 55%; border-radius: 3px 3px 0 0;"></div>
                {{-- Count bar --}}
                <div class="bc-bar {{ $m['isThisMonth'] ? 'bg-violet-500' : 'bg-violet-500/30' }}"
                     style="height: {{ max(4, $countPct) }}%; position: absolute; bottom: 0; right: 0; width: 35%; border-radius: 3px 3px 0 0;"></div>
            </div>
            <span class="text-xs {{ $m['isThisMonth'] ? 'text-emerald-400 font-bold' : 'text-slate-600' }}"
                  style="font-size: 9px; white-space: nowrap;">{{ $m['label'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- Monthly table --}}
    <div class="mt-6 pt-5 border-t border-white/5 overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="text-slate-600 uppercase tracking-wider">
                    <th class="text-left pb-2 font-semibold">Bulan</th>
                    <th class="text-right pb-2 font-semibold">Pesanan</th>
                    <th class="text-right pb-2 font-semibold">Pendapatan</th>
                    <th class="text-right pb-2 font-semibold">Lunas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly as $m)
                <tr class="border-t border-white/4 {{ $m['isThisMonth'] ? 'text-slate-200' : 'text-slate-500' }}">
                    <td class="py-1.5 font-semibold {{ $m['isThisMonth'] ? 'text-emerald-400' : '' }}">
                        {{ $m['label'] }} <span class="text-slate-700">{{ $m['year'] }}</span>
                        @if($m['isThisMonth'])<span class="ml-1 text-emerald-500 text-xs">← ini</span>@endif
                    </td>
                    <td class="text-right py-1.5">{{ $m['count'] }}</td>
                    <td class="text-right py-1.5 {{ $m['isThisMonth'] ? 'text-emerald-400 font-bold' : '' }}">
                        Rp {{ number_format($m['revenue'], 0, ',', '.') }}
                    </td>
                    <td class="text-right py-1.5 text-emerald-500/70">
                        @if($m['revenue'] > 0)✓@else—@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script @nonce>
(function () {
    const el = document.getElementById('realtime-clock');
    if (!el) return;

    const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];
    const DAYS_ID   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const now = new Date();
        const day   = DAYS_ID[now.getDay()];
        const date  = now.getDate();
        const month = MONTHS_ID[now.getMonth()];
        const year  = now.getFullYear();
        const time  = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        el.textContent = `${day}, ${date} ${month} ${year} · ${time} WIB`;
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endpush

@endsection
