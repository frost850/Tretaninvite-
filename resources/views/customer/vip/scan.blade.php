@extends('customer.layout')

@section('title', 'Scan Check-In — ' . $wedding->bride_name)

@section('nav')
<a href="{{ route('my.vip.dashboard', $token) }}" class="cust-nav-link">Dashboard</a>
@if($wedding->guestbook_enabled)
<a href="{{ route('my.vip.guestbook', $token) }}" class="cust-nav-link">Guestbook</a>
@endif
<a href="{{ route('my.vip.qr-codes', $token) }}" class="cust-nav-link">QR Code</a>
<a href="{{ route('my.vip.scan', $token) }}" class="cust-nav-link active">Scanner</a>
@endsection

@push('styles')
<style>
    /* ── Scanner overlay ─────────────────────────────────────────────── */
    #qr-reader {
        width: 100% !important;
        height: 100% !important;
        position: absolute;
        inset: 0;
        overflow: hidden;
    }
    #qr-reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
    }
    #qr-reader img, #qr-reader > div:not([id]) { display: none !important; }

    /* Frame overlay */
    .scan-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .scan-frame {
        position: relative;
        width: 220px;
        height: 220px;
    }
    .scan-corner {
        position: absolute;
        inset: 0;
        border: 2px solid transparent;
        border-radius: 6px;
        background:
            linear-gradient(#d97706, #d97706) top    left  / 28px 3px no-repeat,
            linear-gradient(#d97706, #d97706) top    left  / 3px 28px no-repeat,
            linear-gradient(#d97706, #d97706) top    right / 28px 3px no-repeat,
            linear-gradient(#d97706, #d97706) top    right / 3px 28px no-repeat,
            linear-gradient(#d97706, #d97706) bottom left  / 28px 3px no-repeat,
            linear-gradient(#d97706, #d97706) bottom left  / 3px 28px no-repeat,
            linear-gradient(#d97706, #d97706) bottom right / 28px 3px no-repeat,
            linear-gradient(#d97706, #d97706) bottom right / 3px 28px no-repeat;
    }
    .scan-line {
        position: absolute;
        left: 8px; right: 8px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #d97706, transparent);
        animation: scan-move 2s ease-in-out infinite;
        border-radius: 1px;
    }
    @keyframes scan-move {
        0%   { top: 8px; }
        50%  { top: calc(100% - 10px); }
        100% { top: 8px; }
    }

    /* Result box */
    .result-box {
        margin-top: 16px;
        padding: 14px 16px;
        border-radius: 12px;
        font-size: 0.875rem;
        animation: fadeUp .3s ease;
    }
    .result-ok     { background: rgba(22,163,74,.15); border: 1px solid rgba(22,163,74,.35); }
    .result-already{ background: rgba(217,119,6,.15); border: 1px solid rgba(217,119,6,.35); }
    .result-error  { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3); }

    .log-item { animation: fadeUp .25s ease; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')

<div class="mb-6">
    <a href="{{ route('my.vip.dashboard', $token) }}"
       class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">
        &larr; Portal VIP
    </a>
    <h1 class="text-2xl font-semibold text-slate-100">Scan QR Check-In</h1>
    <p class="text-slate-400 text-sm mt-1">
        {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}
        &mdash; <span class="text-green-400 font-semibold">{{ $stats['checked_in'] }}</span>/{{ $stats['total'] }} tamu check-in
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    {{-- ── Panel Kiri: Scanner ── --}}
    <div>
        <div class="bg-slate-800 rounded-2xl p-6 border border-white/5">

            {{-- Status badge --}}
            <div class="flex items-center justify-between mb-4">
                <span id="cam-status" class="inline-flex items-center gap-2 text-sm text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-slate-500 inline-block" id="cam-dot"></span>
                    Klik tombol untuk mulai kamera
                </span>
                <button id="btn-stop" onclick="stopScanner()"
                        class="hidden px-3 py-1.5 rounded-lg text-xs font-semibold text-red-400 border border-red-500/30 hover:bg-red-900/20 transition">
                    Hentikan
                </button>
            </div>

            {{-- Camera container --}}
            <div class="relative bg-slate-900 rounded-2xl overflow-hidden min-h-[280px] flex items-center justify-center" id="cam-wrap">
                <div id="cam-placeholder" class="text-center text-slate-600">
                    <div class="text-5xl mb-3">&#128247;</div>
                    <p class="text-sm">Kamera belum aktif</p>
                </div>
                <div id="qr-reader"></div>

                {{-- Scan frame overlay --}}
                <div class="scan-overlay hidden" id="scan-overlay">
                    <div class="scan-frame">
                        <span class="scan-corner"></span>
                        <div class="scan-line"></div>
                    </div>
                </div>
            </div>

            {{-- Start button --}}
            <button id="btn-start" onclick="startScanner()"
                    class="mt-4 w-full py-3 rounded-xl font-bold text-sm transition"
                    style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;box-shadow:0 2px 12px rgba(217,119,6,.3);">
                Mulai Kamera
            </button>

            {{-- Result box --}}
            <div id="result-box" class="result-box hidden"></div>
        </div>

        {{-- Manual input --}}
        <div class="mt-4 bg-slate-800 rounded-2xl p-5 border border-white/5">
            <p class="text-sm text-slate-400 mb-3 font-semibold">Manual Input (fallback)</p>
            <form id="manual-form" class="flex gap-2">
                @csrf
                <input type="text" id="manual-url" placeholder="Tempel URL undangan di sini…"
                       class="flex-1 bg-slate-700 border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:border-amber-400/50">
                <button type="submit"
                        class="px-4 py-2 rounded-lg font-bold text-sm bg-amber-600 hover:bg-amber-500 text-white transition">
                    Check-In
                </button>
            </form>
        </div>
    </div>

    {{-- ── Panel Kanan: Log check-in ── --}}
    <div>
        <div class="bg-slate-800 rounded-2xl p-6 border border-white/5 h-full">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-slate-200 text-sm">Log Check-In Sesi Ini</h2>
                <span id="log-count" class="text-xs px-2 py-0.5 rounded-full bg-slate-700 text-slate-400">0 tamu</span>
            </div>
            <ul id="log-list" class="space-y-2 max-h-[480px] overflow-y-auto pr-1">
                <li id="log-empty" class="text-slate-600 text-sm text-center py-8">Belum ada scan sesi ini</li>
            </ul>
        </div>
    </div>

</div>

{{-- Progress bar --}}
<div class="mt-6 bg-slate-800 rounded-2xl p-5 border border-white/5">
    <div class="flex items-center justify-between text-sm mb-2">
        <span class="text-slate-400">Progress Check-In</span>
        <span class="text-white font-bold">
            <span id="prog-in">{{ $stats['checked_in'] }}</span> / {{ $stats['total'] }} tamu
        </span>
    </div>
    <div class="w-full bg-slate-700 rounded-full h-3">
        <div id="prog-bar" class="h-3 rounded-full transition-all duration-500"
             style="width: {{ $stats['total'] > 0 ? round($stats['checked_in'] / $stats['total'] * 100) : 0 }}%;
                    background: linear-gradient(90deg, #d97706, #16a34a);">
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script type="application/json" id="scan-data">
{
    "checkinUrl": "{{ route('my.vip.scan.checkin', $token) }}",
    "csrf":       "{{ csrf_token() }}",
    "total":      {{ $stats['total'] }},
    "checkedIn":  {{ $stats['checked_in'] }}
}
</script>

<script>
const _d          = JSON.parse(document.getElementById('scan-data').textContent);
const CHECKIN_URL = _d.checkinUrl;
const CSRF        = _d.csrf;
const TOTAL       = _d.total;

let html5QrCode    = null;
let checkedInCount = _d.checkedIn;
let logCount       = 0;
let lastScanned    = null;

// ─── Scanner ─────────────────────────────────────────────────────────────────

function startScanner() {
    document.getElementById('cam-placeholder').style.display = 'none';
    document.getElementById('btn-start').style.display = 'none';
    document.getElementById('btn-stop').classList.remove('hidden');
    document.getElementById('scan-overlay').classList.remove('hidden');

    setCamStatus('Menginisialisasi kamera…', 'yellow');

    html5QrCode = new Html5Qrcode('qr-reader');
    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 },
        onScanSuccess,
        () => {}
    ).then(() => {
        setCamStatus('Kamera aktif — arahkan ke QR tamu', 'green');
    }).catch(err => {
        setCamStatus('Gagal akses kamera: ' + err, 'red');
        resetUI();
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().catch(() => {});
        html5QrCode = null;
    }
    resetUI();
    setCamStatus('Kamera dihentikan', 'slate');
}

function resetUI() {
    document.getElementById('cam-placeholder').style.display = 'block';
    document.getElementById('btn-start').style.display = 'block';
    document.getElementById('btn-stop').classList.add('hidden');
    document.getElementById('scan-overlay').classList.add('hidden');
}

// ─── Success callback ─────────────────────────────────────────────────────────

function onScanSuccess(decodedText) {
    if (decodedText === lastScanned) return;
    lastScanned = decodedText;
    setTimeout(() => { lastScanned = null; }, 3000);
    doCheckIn(decodedText);
}

// ─── Check-in API ─────────────────────────────────────────────────────────────

function doCheckIn(url) {
    fetch(CHECKIN_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ url: url }),
    })
    .then(r => r.json())
    .then(data => {
        showResult(data);
        addLog(data);
        if (data.status === 'ok') {
            checkedInCount++;
            updateProgress();
        }
    })
    .catch(() => {
        showResult({ status: 'error', message: 'Gagal terhubung ke server.' });
    });
}

// ─── UI Helpers ───────────────────────────────────────────────────────────────

function showResult(data) {
    const box = document.getElementById('result-box');
    box.classList.remove('hidden', 'result-ok', 'result-already', 'result-error');

    if (data.status === 'ok') {
        box.classList.add('result-ok');
        box.innerHTML = `
            <div class="flex items-center gap-3">
                <div>
                    <div class="font-bold text-green-300 text-base">${data.guest.name}</div>
                    ${data.guest.group ? `<div class="text-xs text-green-400/70">${data.guest.group}</div>` : ''}
                    <div class="text-xs text-green-400 mt-1">Check-in berhasil — ${data.guest.pax ?? 1} orang</div>
                </div>
            </div>`;
        playBeep('ok');
    } else if (data.status === 'already') {
        box.classList.add('result-already');
        box.innerHTML = `
            <div class="flex items-center gap-3">
                <div>
                    <div class="font-bold text-amber-300">${data.guest.name}</div>
                    <div class="text-xs text-amber-400 mt-1">Sudah check-in pukul ${data.guest.checked_in_at}</div>
                </div>
            </div>`;
        playBeep('warn');
    } else {
        box.classList.add('result-error');
        box.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="text-red-300 text-sm">${data.message}</div>
            </div>`;
        playBeep('error');
    }

    setTimeout(() => box.classList.add('hidden'), 5000);
}

function addLog(data) {
    const empty = document.getElementById('log-empty');
    if (empty) empty.remove();

    logCount++;
    document.getElementById('log-count').textContent = logCount + ' tamu';

    const list = document.getElementById('log-list');
    const li = document.createElement('li');
    li.className = 'log-item flex items-center gap-3 px-3 py-2.5 rounded-xl ' +
        (data.status === 'ok'      ? 'bg-green-900/20 border border-green-500/20' :
         data.status === 'already' ? 'bg-amber-900/20 border border-amber-500/20' :
                                     'bg-red-900/20 border border-red-500/20');

    const name = data.guest ? data.guest.name : (data.message ?? 'Error');
    const now  = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const badge = data.status === 'ok' ? 'OK' : data.status === 'already' ? 'Ulang' : 'Error';
    const badgeClass = data.status === 'ok' ? 'text-green-400' : data.status === 'already' ? 'text-amber-400' : 'text-red-400';

    li.innerHTML = `
        <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-slate-200 truncate">${name}</div>
            ${data.guest?.group ? `<div class="text-xs text-slate-500 truncate">${data.guest.group}</div>` : ''}
        </div>
        <span class="text-xs font-bold ${badgeClass} shrink-0">${badge}</span>
        <span class="text-xs text-slate-500 shrink-0">${now}</span>`;

    list.insertBefore(li, list.firstChild);
}

function updateProgress() {
    document.getElementById('prog-in').textContent = checkedInCount;
    const pct = TOTAL > 0 ? Math.round(checkedInCount / TOTAL * 100) : 0;
    document.getElementById('prog-bar').style.width = pct + '%';
}

function setCamStatus(msg, color) {
    const el  = document.getElementById('cam-status');
    const dot = document.getElementById('cam-dot');
    el.childNodes[1].textContent = ' ' + msg;
    dot.className = 'w-2 h-2 rounded-full inline-block ' +
        ({ green: 'bg-green-400 animate-pulse', yellow: 'bg-yellow-400 animate-pulse',
           red: 'bg-red-400', slate: 'bg-slate-500' })[color];
}

function playBeep(type) {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = type === 'ok' ? 880 : type === 'warn' ? 440 : 220;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
        osc.start(); osc.stop(ctx.currentTime + 0.3);
    } catch (_) {}
}

// ─── Manual form ──────────────────────────────────────────────────────────────

document.getElementById('manual-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const url = document.getElementById('manual-url').value.trim();
    if (!url) return;
    doCheckIn(url);
    document.getElementById('manual-url').value = '';
});
</script>
@endpush
