@extends('admin.layout')

@section('title', 'Live RSVP — ' . $wedding->bride_name)

@push('styles')
<style @nonce>
.rsvp-badge-hadir  { background: #16a34a22; color: #4ade80; border: 1px solid #16a34a55; }
.rsvp-badge-tidak  { background: #dc262622; color: #f87171; border: 1px solid #dc262655; }
.rsvp-row { animation: rsvpFadeIn .4s ease; }
@keyframes rsvpFadeIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }
</style>
@endpush

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.vip.dashboard', ['wedding_id' => $wedding->id]) }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">← VIP Dashboard</a>
        <h1 class="text-2xl font-semibold text-slate-100 flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
            Live RSVP Dashboard
        </h1>
        <p class="text-slate-400 text-sm mt-1">{{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }} — auto-refresh setiap 15 detik</p>
    </div>
    <button onclick="fetchRsvp()" class="px-4 py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">
        🔄 Refresh
    </button>
</div>

{{-- Summary Bar --}}
<div class="grid grid-cols-3 gap-4 mb-6" id="summary-bar">
    <div class="rounded-xl bg-green-900/30 border border-green-700/30 p-4 text-center">
        <div class="text-3xl font-bold text-green-400" id="cnt-hadir">—</div>
        <div class="text-xs text-slate-400 mt-1">✅ Hadir</div>
    </div>
    <div class="rounded-xl bg-red-900/30 border border-red-700/30 p-4 text-center">
        <div class="text-3xl font-bold text-red-400" id="cnt-tidak">—</div>
        <div class="text-xs text-slate-400 mt-1">❌ Tidak Hadir</div>
    </div>
    <div class="rounded-xl bg-blue-900/30 border border-blue-700/30 p-4 text-center">
        <div class="text-3xl font-bold text-blue-400" id="cnt-pax">—</div>
        <div class="text-xs text-slate-400 mt-1">🪑 Total Pax</div>
    </div>
</div>

{{-- Table --}}
<div class="bg-slate-800 rounded-xl border border-white/5 overflow-hidden">
    <div class="px-5 py-3 border-b border-white/5 flex items-center gap-2">
        <span class="text-slate-300 text-sm font-medium">Konfirmasi Terbaru (50)</span>
        <span id="last-update" class="text-slate-500 text-xs ml-auto"></span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-slate-300">
            <thead>
                <tr class="text-xs text-slate-500 uppercase border-b border-white/5">
                    <th class="px-5 py-3 text-left">Tamu</th>
                    <th class="px-4 py-3 text-left">Grup</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Pax</th>
                    <th class="px-4 py-3 text-left">Catatan</th>
                    <th class="px-4 py-3 text-right">Waktu</th>
                </tr>
            </thead>
            <tbody id="rsvp-body">
                <tr><td colspan="6" class="text-center py-10 text-slate-500">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script @nonce>
const RSVP_URL = '{{ route('admin.vip.rsvp-live.data', $wedding) }}';

async function fetchRsvp() {
    try {
        const res  = await fetch(RSVP_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        document.getElementById('cnt-hadir').textContent = data.summary.hadir;
        document.getElementById('cnt-tidak').textContent = data.summary.tidak;
        document.getElementById('cnt-pax').textContent   = data.summary.total_pax;
        document.getElementById('last-update').textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID');

        const tbody = document.getElementById('rsvp-body');
        if (!data.guests.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10 text-slate-500">Belum ada RSVP.</td></tr>';
            return;
        }
        tbody.innerHTML = data.guests.map(g => `
            <tr class="rsvp-row border-b border-white/5 hover:bg-white/3 transition">
                <td class="px-5 py-3 font-medium text-slate-100">${esc(g.name)}</td>
                <td class="px-4 py-3 text-slate-400">${esc(g.group || '—')}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold ${g.attending ? 'rsvp-badge-hadir' : 'rsvp-badge-tidak'}">
                        ${g.attending ? '✅ Hadir' : '❌ Tidak'}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">${g.pax || '—'}</td>
                <td class="px-4 py-3 text-slate-400 max-w-xs truncate">${esc(g.notes || '—')}</td>
                <td class="px-4 py-3 text-right text-slate-500 text-xs">${esc(g.time)}</td>
            </tr>
        `).join('');
    } catch(e) {
        console.error('Live RSVP error:', e);
    }
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

fetchRsvp();
setInterval(fetchRsvp, 15000);
</script>
@endpush
