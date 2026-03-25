@extends('admin.layout')

@section('title', 'Audit Log')

@push('styles')
<style @nonce>
.action-pill {
    display: inline-flex; align-items: center;
    padding: 2px 8px; border-radius: 99px;
    font-size: .68rem; font-weight: 700; letter-spacing: .04em;
    white-space: nowrap;
}
.action-login   { background: rgba(34,197,94,.12);  color: #4ade80; border: 1px solid rgba(34,197,94,.25); }
.action-logout  { background: rgba(148,163,184,.1); color: #94a3b8; border: 1px solid rgba(148,163,184,.2); }
.action-create  { background: rgba(59,130,246,.12); color: #60a5fa; border: 1px solid rgba(59,130,246,.25); }
.action-update  { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
.action-delete  { background: rgba(239,68,68,.12);  color: #f87171; border: 1px solid rgba(239,68,68,.25); }
.action-restore { background: rgba(139,92,246,.12); color: #a78bfa; border: 1px solid rgba(139,92,246,.25); }
.action-other   { background: rgba(255,255,255,.06); color: #94a3b8; border: 1px solid rgba(255,255,255,.1); }
.actor-super { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
.actor-sub   { background: rgba(99,102,241,.12);  color: #818cf8; border: 1px solid rgba(99,102,241,.25); }
.log-row { transition: background .15s; }
.log-row:hover td { background: rgba(124,58,237,.06); }
.filter-input {
    background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);
    border-radius: 8px; padding: 6px 10px; font-size: .8rem; color: #f1f5f9;
    width: 100%; outline: none; transition: border-color .2s;
}
.filter-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
.filter-select {
    background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);
    border-radius: 8px; padding: 6px 10px; font-size: .8rem; color: #f1f5f9;
    outline: none; cursor: pointer; transition: border-color .2s;
}
.filter-select:focus { border-color: #f59e0b; }
.filter-select option { background: #1e1b4b; }
details > summary { list-style: none; cursor: pointer; }
details > summary::-webkit-details-marker { display: none; }
</style>
@endpush

@section('content')

{{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-100 flex items-center gap-2">
            🔍 Audit Log
            <span class="text-sm font-semibold px-2.5 py-0.5 rounded-full bg-purple-900/40 border border-purple-700/40 text-purple-300">
                Super Admin Only
            </span>
        </h1>
        <p class="text-slate-500 text-sm mt-0.5">Riwayat semua aktivitas admin — append-only, tidak bisa diubah</p>
    </div>
    <div class="text-xs text-slate-600 font-mono bg-white/5 border border-white/10 rounded-xl px-3 py-2">
        Total: <span class="text-slate-300 font-bold">{{ number_format($logs->total()) }}</span> entri
    </div>
</div>

{{-- ══ Filter bar ══════════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('admin.audit-log') }}" class="mb-5">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 p-4 rounded-2xl border border-white/08 bg-white/[.03]">
        {{-- Search --}}
        <div class="col-span-2 sm:col-span-3 lg:col-span-2">
            <label class="field-label">Cari</label>
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="email, action, IP..." class="filter-input">
        </div>
        {{-- Actor type --}}
        <div>
            <label class="field-label">Tipe Aktor</label>
            <select name="actor" class="filter-select w-full">
                <option value="">Semua</option>
                <option value="super_admin" {{ $actor === 'super_admin' ? 'selected' : '' }}>👑 Super Admin</option>
                <option value="sub_admin"   {{ $actor === 'sub_admin'   ? 'selected' : '' }}>👤 Sub Admin</option>
            </select>
        </div>
        {{-- Action --}}
        <div>
            <label class="field-label">Aksi</label>
            <select name="action" class="filter-select w-full">
                <option value="">Semua</option>
                @foreach($actions as $a)
                    <option value="{{ $a }}" {{ $action === $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
        {{-- Date range --}}
        <div class="col-span-2 sm:col-span-1 flex flex-col gap-1">
            <label class="field-label">Dari Tanggal</label>
            <input type="date" name="from" value="{{ $fromDate }}" class="filter-input" style="color-scheme:dark;">
        </div>
        <div class="col-span-2 sm:col-span-1 flex flex-col gap-1">
            <label class="field-label">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ $toDate }}" class="filter-input" style="color-scheme:dark;">
        </div>
        {{-- Buttons --}}
        <div class="col-span-2 sm:col-span-3 lg:col-span-5 flex gap-2 justify-end pt-1">
            @if($q || $actor || $action || $fromDate || $toDate)
            <a href="{{ route('admin.audit-log') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-400 border border-white/10 hover:bg-white/05 hover:text-slate-200 transition">
                ✕ Reset
            </a>
            @endif
            <button type="submit"
                    class="px-5 py-2 rounded-xl text-sm font-bold bg-amber-600/20 border border-amber-600/40 text-amber-300 hover:bg-amber-600/30 transition">
                🔍 Filter
            </button>
        </div>
    </div>
</form>

{{-- ══ Log table ════════════════════════════════════════════════════════════ --}}
@if($logs->isEmpty())
    <div class="text-center py-20 text-slate-500">
        <div class="text-5xl mb-4">🔍</div>
        <div class="text-lg font-semibold text-slate-400 mb-1">Tidak ada log yang ditemukan</div>
        <div class="text-sm">Coba ubah filter atau hapus pencarian.</div>
    </div>
@else
<div class="rounded-2xl border border-white/08 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:rgba(255,255,255,.03);border-bottom:1px solid rgba(255,255,255,.06);">
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider whitespace-nowrap">Waktu</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Aktor</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Aksi</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Target</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">IP</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[.04]">
            @foreach($logs as $log)
                @php
                    $actionLower = strtolower($log->action);
                    if (str_contains($actionLower, 'login_success') || str_contains($actionLower, '2fa')) {
                        $pillClass = 'action-login';
                    } elseif (str_contains($actionLower, 'logout') || str_contains($actionLower, 'login_failed')) {
                        $pillClass = 'action-logout';
                    } elseif (str_contains($actionLower, 'create') || str_contains($actionLower, 'store') || str_contains($actionLower, 'upload') || str_contains($actionLower, 'import')) {
                        $pillClass = 'action-create';
                    } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'edit') || str_contains($actionLower, 'extend') || str_contains($actionLower, 'toggle') || str_contains($actionLower, 'confirm') || str_contains($actionLower, 'link')) {
                        $pillClass = 'action-update';
                    } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy') || str_contains($actionLower, 'purge')) {
                        $pillClass = 'action-delete';
                    } elseif (str_contains($actionLower, 'restore')) {
                        $pillClass = 'action-restore';
                    } else {
                        $pillClass = 'action-other';
                    }
                @endphp
                <tr class="log-row">
                    {{-- Waktu --}}
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-slate-300 font-mono text-xs">{{ $log->created_at->format('d/m/y') }}</div>
                        <div class="text-slate-500 font-mono text-[.68rem]">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    {{-- Aktor --}}
                    <td class="px-4 py-3">
                        <div class="text-slate-200 text-xs font-medium truncate max-w-[140px]" title="{{ $log->actor_email }}">
                            {{ $log->actor_email }}
                        </div>
                        <div class="mt-1">
                            <span class="action-pill {{ $log->actor_type === 'super_admin' ? 'actor-super' : 'actor-sub' }}">
                                {{ $log->actor_type === 'super_admin' ? '👑 Super' : '👤 Sub' }}
                            </span>
                        </div>
                    </td>
                    {{-- Aksi --}}
                    <td class="px-4 py-3">
                        <span class="action-pill {{ $pillClass }}">{{ $log->action }}</span>
                    </td>
                    {{-- Target --}}
                    <td class="px-4 py-3">
                        @if($log->target_type)
                            <div class="text-slate-400 text-xs">{{ $log->target_type }}</div>
                            @if($log->target_id)
                            <div class="text-slate-500 font-mono text-[.68rem]">#{{ $log->target_id }}</div>
                            @endif
                        @else
                            <span class="text-slate-600 text-xs">—</span>
                        @endif
                    </td>
                    {{-- IP --}}
                    <td class="px-4 py-3">
                        <span class="text-slate-500 font-mono text-xs">{{ $log->ip_address ?? '—' }}</span>
                    </td>
                    {{-- Detail --}}
                    <td class="px-4 py-3">
                        @if(!empty($log->details))
                        <details>
                            <summary class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-200 cursor-pointer transition select-none">
                                <span class="text-[.65rem] border border-white/15 rounded px-1.5 py-0.5 bg-white/05 hover:bg-white/10 transition">JSON</span>
                            </summary>
                            <div class="mt-2 p-2 rounded-lg border border-white/08 bg-black/30 font-mono text-[.65rem] text-emerald-300 max-w-xs overflow-x-auto whitespace-pre">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                        </details>
                        @else
                            <span class="text-slate-600 text-xs">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ══ Pagination ═══════════════════════════════════════════════════════════ --}}
@if($logs->hasPages())
<div class="mt-5 flex items-center justify-between gap-4 flex-wrap">
    <div class="text-xs text-slate-500">
        Menampilkan <span class="text-slate-300">{{ $logs->firstItem() }}–{{ $logs->lastItem() }}</span>
        dari <span class="text-slate-300">{{ number_format($logs->total()) }}</span> entri
    </div>
    <div class="flex items-center gap-1">
        {{-- Prev --}}
        @if($logs->onFirstPage())
            <span class="px-3 py-1.5 rounded-lg text-xs text-slate-600 border border-white/05 cursor-not-allowed">← Sebelumnya</span>
        @else
            <a href="{{ $logs->previousPageUrl() }}"
               class="px-3 py-1.5 rounded-lg text-xs text-slate-300 border border-white/10 hover:bg-white/05 transition">← Sebelumnya</a>
        @endif

        {{-- Page numbers (compact: show at most 5 around current) --}}
        @php
            $current  = $logs->currentPage();
            $last     = $logs->lastPage();
            $start    = max(1, $current - 2);
            $end      = min($last, $current + 2);
        @endphp
        @if($start > 1)
            <a href="{{ $logs->url(1) }}" class="px-2.5 py-1.5 rounded-lg text-xs text-slate-400 border border-white/08 hover:bg-white/05 transition">1</a>
            @if($start > 2)<span class="text-slate-600 text-xs px-1">…</span>@endif
        @endif
        @for($p = $start; $p <= $end; $p++)
            @if($p === $current)
                <span class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-amber-300 border border-amber-600/40 bg-amber-600/15">{{ $p }}</span>
            @else
                <a href="{{ $logs->url($p) }}" class="px-2.5 py-1.5 rounded-lg text-xs text-slate-400 border border-white/08 hover:bg-white/05 transition">{{ $p }}</a>
            @endif
        @endfor
        @if($end < $last)
            @if($end < $last - 1)<span class="text-slate-600 text-xs px-1">…</span>@endif
            <a href="{{ $logs->url($last) }}" class="px-2.5 py-1.5 rounded-lg text-xs text-slate-400 border border-white/08 hover:bg-white/05 transition">{{ $last }}</a>
        @endif

        {{-- Next --}}
        @if($logs->hasMorePages())
            <a href="{{ $logs->nextPageUrl() }}"
               class="px-3 py-1.5 rounded-lg text-xs text-slate-300 border border-white/10 hover:bg-white/05 transition">Berikutnya →</a>
        @else
            <span class="px-3 py-1.5 rounded-lg text-xs text-slate-600 border border-white/05 cursor-not-allowed">Berikutnya →</span>
        @endif
    </div>
</div>
@endif

@endif {{-- end if logs not empty --}}

@endsection
