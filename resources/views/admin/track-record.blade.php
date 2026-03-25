@extends('admin.layout')

@section('title', 'Track Record')

@push('styles')
<style @nonce>
.tr-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 20px;
}
.tr-section-label {
    display: flex; align-items: center; gap: 10px;
    font-size: 0.7rem; font-weight: 800; letter-spacing: 0.08em;
    text-transform: uppercase; color: #475569; margin-bottom: 12px;
}
.tr-section-label::after { content:''; flex:1; height:1px; background:rgba(255,255,255,0.06); }

.tr-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
.tr-table th {
    text-align: left; padding: 8px 12px;
    font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em;
    text-transform: uppercase; color: #475569;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.tr-table td { padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
.tr-table tr:last-child td { border-bottom: none; }
.tr-table tr.this-month td { background: rgba(124,58,237,0.07); }
.tr-table tr:hover td { background: rgba(255,255,255,0.03); }
.tr-table tr.this-month:hover td { background: rgba(124,58,237,0.12); }

.pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 99px;
}
.pill-lunas   { background:rgba(52,211,153,.12); color:#34d399; border:1px solid rgba(52,211,153,.2); }
.pill-pending { background:rgba(245,158,11,.12); color:#fbbf24; border:1px solid rgba(245,158,11,.2); }
.pill-belum   { background:rgba(148,163,184,.08); color:#94a3b8; border:1px solid rgba(148,163,184,.15); }
.pill-ditolak { background:rgba(239,68,68,.12);  color:#f87171; border:1px solid rgba(239,68,68,.2); }
.pill-vip     { background:rgba(234,179,8,.12);  color:#fbbf24; border:1px solid rgba(234,179,8,.25); }
.pill-premium { background:rgba(245,158,11,.12); color:#f59e0b; border:1px solid rgba(245,158,11,.25); }
.pill-basic   { background:rgba(59,130,246,.12); color:#60a5fa; border:1px solid rgba(59,130,246,.25); }
.pill-trial   { background:rgba(148,163,184,.08); color:#94a3b8; border:1px solid rgba(148,163,184,.15); }

.bar-bg { height: 6px; background:rgba(255,255,255,0.06); border-radius:99px; min-width:60px; }
.bar-fill { height: 100%; border-radius:99px; background: linear-gradient(to right, #7c3aed, #a78bfa); }

.summary-num { font-size: 1.5rem; font-weight: 900; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6 fade-up stagger-1">
    <div>
        <p class="text-slate-500 text-xs">{{ now()->translatedFormat('l, d F Y') }}</p>
        <h1 class="text-2xl font-black text-slate-100 mt-0.5">Track Record</h1>
        <p class="text-slate-500 text-sm mt-0.5">Riwayat lengkap pendapatan & pesanan.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}"
       class="px-4 py-2 rounded-xl border border-white/10 text-slate-400 hover:text-slate-200 hover:bg-white/5 text-sm font-semibold transition">
        ← Dashboard
    </a>
</div>

{{-- ══════ SUMMARY CARDS ══════ --}}
<div class="tr-section-label fade-up stagger-1">Ringkasan All-Time</div>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-8 fade-up stagger-1">

    <div class="tr-card text-center">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-2">Total Pesanan</p>
        <p class="summary-num text-slate-100">{{ $totalOrders }}</p>
    </div>

    <div class="tr-card text-center border-emerald-700/25" style="background:rgba(52,211,153,0.04);">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-2">Lunas</p>
        <p class="summary-num text-emerald-400">{{ $totalLunas }}</p>
    </div>

    <div class="tr-card text-center border-yellow-700/25" style="background:rgba(245,158,11,0.04);">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-2">Menunggu Konfirmasi</p>
        <p class="summary-num text-yellow-400">{{ $totalPending }}</p>
    </div>

    <div class="tr-card text-center border-slate-600/25">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-2">Belum Bayar</p>
        <p class="summary-num text-slate-400">{{ $totalBelum }}</p>
    </div>

    <div class="tr-card text-center border-red-700/25" style="background:rgba(239,68,68,0.04);">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-2">Ditolak</p>
        <p class="summary-num text-red-400">{{ $totalDitolak }}</p>
    </div>

    <div class="tr-card text-center border-violet-700/25 col-span-2 sm:col-span-1" style="background:rgba(124,58,237,0.06);">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-2">Total Pendapatan</p>
        <p class="text-violet-300 font-black text-lg">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>

</div>

{{-- ══════ BREAKDOWN PER PAKET ══════ --}}
<div class="tr-section-label fade-up stagger-2">Breakdown per Paket (Lunas)</div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8 fade-up stagger-2">
    @php
        $pkgDefs = [
            'vip'     => ['label' => 'VIP Royal',  'cls' => 'pill-vip',     'color' => 'bg-yellow-500'],
            'premium' => ['label' => 'Premium',    'cls' => 'pill-premium', 'color' => 'bg-amber-500'],
            'basic'   => ['label' => 'Basic',      'cls' => 'pill-basic',   'color' => 'bg-blue-500'],
            'trial'   => ['label' => 'Trial',      'cls' => 'pill-trial',   'color' => 'bg-slate-500'],
        ];
    @endphp
    @foreach($pkgDefs as $key => $def)
    <div class="tr-card">
        <div class="flex items-center justify-between mb-3">
            <span class="pill {{ $def['cls'] }}">{{ $def['label'] }}</span>
        </div>
        <p class="text-2xl font-black text-slate-100 mb-1">{{ $byPackage[$key]['count'] }}</p>
        <p class="text-xs text-slate-500">pesanan lunas</p>
        <p class="text-sm font-bold text-emerald-400 mt-2">Rp {{ number_format($byPackage[$key]['revenue'], 0, ',', '.') }}</p>
    </div>
    @endforeach
</div>

{{-- ══════ TABEL 12 BULAN ══════ --}}
<div class="tr-section-label fade-up stagger-3">Riwayat Bulanan (12 bulan terakhir)</div>
<div class="tr-card mb-8 overflow-x-auto fade-up stagger-3">
    @php $maxRevMonth = max(1, $monthly->max('revenue')); @endphp
    <table class="tr-table">
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Total Pesanan</th>
                <th>Lunas</th>
                <th>Konfirmasi</th>
                <th>Belum Bayar</th>
                <th>Ditolak</th>
                <th>Pendapatan</th>
                <th style="width:120px">Bar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthly as $m)
            <tr class="{{ $m['isThisMonth'] ? 'this-month' : '' }}">
                <td>
                    <span class="font-bold text-slate-200">{{ $m['label'] }}</span>
                    @if($m['isThisMonth'])
                        <span class="ml-2 text-violet-400 text-xs font-bold">← bulan ini</span>
                    @endif
                </td>
                <td class="text-slate-400 font-semibold">{{ $m['total'] }}</td>
                <td>
                    @if($m['lunas'] > 0)
                    <span class="pill pill-lunas">{{ $m['lunas'] }}</span>
                    @else
                    <span class="text-slate-600">—</span>
                    @endif
                </td>
                <td>
                    @if($m['pending'] > 0)
                    <span class="pill pill-pending">{{ $m['pending'] }}</span>
                    @else
                    <span class="text-slate-600">—</span>
                    @endif
                </td>
                <td>
                    @if($m['belum'] > 0)
                    <span class="pill pill-belum">{{ $m['belum'] }}</span>
                    @else
                    <span class="text-slate-600">—</span>
                    @endif
                </td>
                <td>
                    @if($m['ditolak'] > 0)
                    <span class="pill pill-ditolak">{{ $m['ditolak'] }}</span>
                    @else
                    <span class="text-slate-600">—</span>
                    @endif
                </td>
                <td>
                    @if($m['revenue'] > 0)
                    <span class="font-black {{ $m['isThisMonth'] ? 'text-violet-300' : 'text-emerald-400' }}">
                        Rp {{ number_format($m['revenue'], 0, ',', '.') }}
                    </span>
                    @else
                    <span class="text-slate-600">Rp 0</span>
                    @endif
                </td>
                <td>
                    <div class="bar-bg">
                        <div class="bar-fill" style="width:{{ $maxRevMonth > 0 ? round(($m['revenue']/$maxRevMonth)*100) : 0 }}%"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="font-black text-slate-300 text-sm pt-3">Total</td>
                <td class="font-black text-slate-300">{{ $monthly->sum('total') }}</td>
                <td class="font-black text-emerald-400">{{ $monthly->sum('lunas') }}</td>
                <td class="font-black text-yellow-400">{{ $monthly->sum('pending') }}</td>
                <td class="font-black text-slate-400">{{ $monthly->sum('belum') }}</td>
                <td class="font-black text-red-400">{{ $monthly->sum('ditolak') }}</td>
                <td class="font-black text-violet-300">Rp {{ number_format($monthly->sum('revenue'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- ══════ DAFTAR ORDER LUNAS ══════ --}}
<div class="tr-section-label fade-up stagger-4">Semua Pesanan Lunas</div>
<div class="tr-card overflow-x-auto fade-up stagger-4">
    @if($lunasOrders->isEmpty())
    <div class="py-16 text-center">
        <p class="text-slate-500">Belum ada pesanan lunas.</p>
    </div>
    @else
    <table class="tr-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Undangan</th>
                <th>Paket</th>
                <th>Nilai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lunasOrders as $o)
            @php
                $oPrice = $o->packageAmount();
                $oName  = $o->bride_name . ($o->groom_name ? ' & ' . $o->groom_name : '');
            @endphp
            <tr>
                <td class="text-slate-600 text-xs">{{ $o->id }}</td>
                <td class="text-slate-400 text-xs whitespace-nowrap">{{ $o->created_at->format('d M Y') }}<br><span class="text-slate-600">{{ $o->created_at->format('H:i') }}</span></td>
                <td>
                    <p class="font-semibold text-slate-200 text-sm">{{ $o->customer_name ?: '—' }}</p>
                    @if($o->customer_phone)
                    <p class="text-xs text-slate-500">{{ $o->customer_phone }}</p>
                    @endif
                </td>
                <td>
                    <p class="font-semibold text-slate-200 text-sm">{{ $oName }}</p>
                    <p class="text-xs text-slate-500">{{ $o->template }}</p>
                </td>
                <td>
                    <span class="pill pill-{{ $o->package ?? 'basic' }}">
                        {{ strtoupper($o->package ?? 'basic') }}
                    </span>
                </td>
                <td class="font-black text-emerald-400">Rp {{ number_format($oPrice, 0, ',', '.') }}</td>
                <td>
                    @if($o->wedding_id)
                    <a href="{{ route('admin.weddings.edit', $o->wedding_id) }}"
                       class="text-xs text-blue-400 hover:text-blue-300 font-semibold transition">Edit</a>
                    @else
                    <a href="{{ route('admin.orders.index') }}"
                       class="text-xs text-slate-500 hover:text-slate-300 font-semibold transition">Pesanan</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($lunasOrders->hasPages())
    <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between px-2">
        <p class="text-xs text-slate-500">
            Menampilkan {{ $lunasOrders->firstItem() }}–{{ $lunasOrders->lastItem() }} dari {{ $lunasOrders->total() }} pesanan lunas
        </p>
        <div class="flex gap-1 text-xs">
            @if($lunasOrders->onFirstPage())
            <span class="px-3 py-1.5 rounded-lg border border-white/5 text-slate-700">← Sebelumnya</span>
            @else
            <a href="{{ $lunasOrders->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-white/10 text-slate-400 hover:text-slate-200 hover:bg-white/5 transition">← Sebelumnya</a>
            @endif

            @if($lunasOrders->hasMorePages())
            <a href="{{ $lunasOrders->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-white/10 text-slate-400 hover:text-slate-200 hover:bg-white/5 transition">Berikutnya →</a>
            @else
            <span class="px-3 py-1.5 rounded-lg border border-white/5 text-slate-700">Berikutnya →</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

@endsection
