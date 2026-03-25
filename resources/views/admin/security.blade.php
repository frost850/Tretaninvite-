@extends('admin.layout')

@section('title', 'Security Monitor')

@push('styles')
<style @nonce>
.sec-stat {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px;
    padding: 20px 24px;
}
.sec-stat-danger { border-color: rgba(239,68,68,.3) !important; background: rgba(239,68,68,.05) !important; }
.sec-stat-warn   { border-color: rgba(245,158,11,.25) !important; background: rgba(245,158,11,.04) !important; }
.sec-stat-ok     { border-color: rgba(34,197,94,.2) !important;  background: rgba(34,197,94,.03) !important; }

.threat-row { transition: background .15s; }
.threat-row:hover td { background: rgba(239,68,68,.04); }

.badge-blocked { background: rgba(239,68,68,.15); color: #f87171; border: 1px solid rgba(239,68,68,.3); }
.badge-failed  { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
.badge-high    { background: rgba(239,68,68,.2);  color: #f87171; border: 1px solid rgba(239,68,68,.4); font-weight:800; }
.badge-medium  { background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }
.badge-low     { background: rgba(148,163,184,.1); color: #94a3b8; border: 1px solid rgba(148,163,184,.2); }

.pill { display:inline-flex; align-items:center; padding:2px 8px; border-radius:99px; font-size:.67rem; font-weight:700; white-space:nowrap; }
.ua-text { font-size:.68rem; color:#64748b; font-family:monospace; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
</style>
@endpush

@section('content')

{{-- ══ Header ══════════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold flex items-center gap-2" style="color:#fca5a5;">
            🛡️ Security Monitor
            <span class="text-sm font-semibold px-2.5 py-0.5 rounded-full bg-red-900/40 border border-red-700/40 text-red-300">
                Super Admin Only
            </span>
        </h1>
        <p class="text-slate-500 text-sm mt-0.5">Pantau percobaan akses ilegal, brute force, dan aktivitas mencurigakan</p>
    </div>
    <a href="{{ route('admin.audit-log', ['action' => 'login_failed']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 border border-white/10 hover:bg-white/05 transition">
        🔍 Lihat di Audit Log →
    </a>
</div>

{{-- ══ Stats ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="sec-stat {{ $totalFailed > 0 ? 'sec-stat-warn' : 'sec-stat-ok' }}">
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-2">Total Percobaan Gagal</div>
        <div class="text-3xl font-black {{ $totalFailed > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ number_format($totalFailed) }}</div>
        <div class="text-xs text-slate-600 mt-1">Sepanjang masa</div>
    </div>
    <div class="sec-stat {{ $failed24h > 5 ? 'sec-stat-danger' : ($failed24h > 0 ? 'sec-stat-warn' : 'sec-stat-ok') }}">
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-2">24 Jam Terakhir</div>
        <div class="text-3xl font-black {{ $failed24h > 5 ? 'text-red-400' : ($failed24h > 0 ? 'text-amber-400' : 'text-emerald-400') }}">{{ $failed24h }}</div>
        @if($failed24h > 5)
        <div class="text-xs text-red-400/70 mt-1 font-semibold">⚠️ Aktivitas tinggi</div>
        @else
        <div class="text-xs text-slate-600 mt-1">Percobaan login gagal</div>
        @endif
    </div>
    <div class="sec-stat {{ $failed7d > 20 ? 'sec-stat-danger' : ($failed7d > 0 ? 'sec-stat-warn' : 'sec-stat-ok') }}">
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-2">7 Hari Terakhir</div>
        <div class="text-3xl font-black {{ $failed7d > 20 ? 'text-red-400' : ($failed7d > 0 ? 'text-amber-400' : 'text-emerald-400') }}">{{ $failed7d }}</div>
        <div class="text-xs text-slate-600 mt-1">Percobaan login gagal</div>
    </div>
    <div class="sec-stat {{ $uniqueIps > 3 ? 'sec-stat-danger' : ($uniqueIps > 0 ? 'sec-stat-warn' : 'sec-stat-ok') }}">
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-2">IP Unik Penyerang</div>
        <div class="text-3xl font-black {{ $uniqueIps > 3 ? 'text-red-400' : ($uniqueIps > 0 ? 'text-amber-400' : 'text-emerald-400') }}">{{ $uniqueIps }}</div>
        <div class="text-xs text-slate-600 mt-1">IP berbeda yang pernah gagal</div>
    </div>
</div>

{{-- ══ Suspicious IPs ═══════════════════════════════════════════════════════ --}}
<div class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-bold text-slate-300 uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
            IP Mencurigakan
        </h2>
        <span class="text-xs text-slate-600">Top 50 berdasarkan jumlah percobaan</span>
    </div>

    @if($byIp->isEmpty())
    <div class="text-center py-12 rounded-2xl border border-white/06 bg-white/[.02]">
        <div class="text-4xl mb-3">✅</div>
        <div class="text-slate-400 font-semibold">Tidak ada percobaan gagal tercatat</div>
        <div class="text-slate-600 text-sm mt-1">Sistem aman</div>
    </div>
    @else
    <div class="rounded-2xl border border-white/08 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:rgba(255,255,255,.03);border-bottom:1px solid rgba(255,255,255,.06);">
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">IP Address</th>
                    <th class="text-center px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Percobaan</th>
                    <th class="text-center px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Devices</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Terakhir Terdeteksi</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Email Dicoba</th>
                    <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Ancaman</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[.04]">
            @foreach($byIp as $row)
                @php
                    // Ambil email-email yang dicoba dari IP ini
                    $emails = \App\Models\AdminAuditLog::whereIn('action', ['login_failed','login_blocked'])
                        ->where('ip_address', $row->ip_address)
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get()
                        ->map(fn($l) => $l->details['email'] ?? null)
                        ->filter()
                        ->unique()
                        ->values();

                    $threatLevel = $row->total >= 10 ? 'high' : ($row->total >= 5 ? 'medium' : 'low');
                    $threatLabel = ['high' => '🔴 Tinggi', 'medium' => '🟡 Sedang', 'low' => '🟢 Rendah'][$threatLevel];
                    $lastSeen = \Carbon\Carbon::parse($row->last_seen);
                @endphp
                <tr class="threat-row">
                    <td class="px-4 py-3">
                        <span class="font-mono text-slate-200 text-xs select-all">{{ $row->ip_address ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-lg font-black {{ $row->total >= 10 ? 'text-red-400' : ($row->total >= 5 ? 'text-amber-400' : 'text-slate-300') }}">
                            {{ $row->total }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-slate-400 text-sm">{{ $row->device_count }}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-slate-300 text-xs">{{ $lastSeen->format('d/m/Y H:i') }}</div>
                        <div class="text-slate-600 text-[.65rem]">{{ $lastSeen->diffForHumans() }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                        @forelse($emails as $email)
                            <span class="pill badge-failed text-[.63rem]">{{ $email }}</span>
                        @empty
                            <span class="text-slate-600 text-xs">—</span>
                        @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="pill badge-{{ $threatLevel }}">{{ $threatLabel }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ══ Recent Activity Timeline ═════════════════════════════════════════════ --}}
<div>
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-bold text-slate-300 uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            Aktivitas Terbaru (100 terakhir)
        </h2>
    </div>

    @if($recent->isEmpty())
    <div class="text-center py-12 rounded-2xl border border-white/06 bg-white/[.02]">
        <div class="text-4xl mb-3">🔒</div>
        <div class="text-slate-400 font-semibold">Tidak ada aktivitas mencurigakan</div>
    </div>
    @else
    <div class="rounded-2xl border border-white/08 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:rgba(255,255,255,.03);border-bottom:1px solid rgba(255,255,255,.06);">
                        <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider whitespace-nowrap">Waktu</th>
                        <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Aksi</th>
                        <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Email Dicoba</th>
                        <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">IP</th>
                        <th class="text-left px-4 py-3 text-slate-500 font-semibold text-[.7rem] uppercase tracking-wider">Perangkat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[.04]">
                @foreach($recent as $log)
                    @php
                        $emailTried = $log->details['email'] ?? null;
                        $isBlocked  = $log->action === 'login_blocked';
                        $ua = $log->user_agent;
                        // Simplify UA string
                        $device = '—';
                        if ($ua) {
                            if (preg_match('/(iPhone|iPad|Android|Windows Phone)/i', $ua, $m)) {
                                $device = $m[1];
                            } elseif (preg_match('/(Windows|Macintosh|Linux|CrOS)/i', $ua, $m)) {
                                $device = $m[1];
                            }
                            if (preg_match('/(Chrome|Firefox|Safari|Edge|OPR|MSIE)/i', $ua, $b)) {
                                $device .= ' / ' . ($b[1] === 'OPR' ? 'Opera' : $b[1]);
                            }
                        }
                    @endphp
                    <tr class="threat-row">
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <div class="text-slate-300 font-mono text-xs">{{ $log->created_at->format('d/m/y H:i:s') }}</div>
                            <div class="text-slate-600 text-[.63rem]">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="pill {{ $isBlocked ? 'badge-blocked' : 'badge-failed' }}">
                                {{ $isBlocked ? '🚫 BLOCKED' : '❌ FAILED' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="text-slate-300 text-xs font-mono">{{ $emailTried ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="text-slate-400 font-mono text-xs">{{ $log->ip_address ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-2.5">
                            @if($ua)
                                <div class="text-slate-400 text-xs">{{ $device }}</div>
                                <div class="ua-text" title="{{ $ua }}">{{ $ua }}</div>
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
    @endif
</div>

@endsection
