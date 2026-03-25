@extends('admin.layout')

@section('title', 'Scan Check-In — ' . $wedding->bride_name)

@push('styles')
<style @nonce>
    #qr-reader { width: 100%; max-width: 420px; margin: 0 auto; border-radius: 16px; overflow: hidden; }
    #qr-reader video { border-radius: 16px; }
    #qr-reader img { display: none !important; }  /* sembunyikan icon upload bawaan */

    .result-box {
        transition: all .3s ease;
        border-radius: 16px;
        padding: 20px 24px;
        margin-top: 24px;
    }
    .result-ok      { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.35); }
    .result-already { background: rgba(245,158,11,.10); border: 1px solid rgba(245,158,11,.35); }
    .result-error   { background: rgba(239, 68, 68,.10); border: 1px solid rgba(239,68,68,.35); }

    .scan-overlay {
        position: absolute; top: 0; right: 0; bottom: 0; left: 0;
        pointer-events: none; z-index: 10;
        display: flex; align-items: center; justify-content: center;
    }
    .scan-frame {
        width: 220px; height: 220px;
        border: 2px solid rgba(251,191,36,.6);
        border-radius: 12px;
        box-shadow: 0 0 0 9999px rgba(0,0,0,.45);
        position: relative;
    }
    .scan-frame::before, .scan-frame::after,
    .scan-corner::before, .scan-corner::after {
        content: '';
        position: absolute;
        width: 22px; height: 22px;
        border-color: #fbbf24;
        border-style: solid;
    }
    .scan-frame::before { top:-1px; left:-1px; border-width:3px 0 0 3px; border-radius:4px 0 0 0; }
    .scan-frame::after  { top:-1px; right:-1px; border-width:3px 3px 0 0; border-radius:0 4px 0 0; }
    .scan-corner::before { bottom:-1px; left:-1px; border-width:0 0 3px 3px; border-radius:0 0 0 4px; }
    .scan-corner::after  { bottom:-1px; right:-1px; border-width:0 3px 3px 0; border-radius:0 0 4px 0; }

    @keyframes scanline {
        0%   { top: 8px; }
        100% { top: calc(100% - 8px); }
    }
    .scan-line {
        position: absolute; left: 6px; right: 6px; height: 2px;
        background: linear-gradient(90deg, transparent, #fbbf24, transparent);
        animation: scanline 2s linear infinite;
        top: 8px;
    }

    #log-list .log-item { animation: fadeSlide .35s ease; }
    @keyframes fadeSlide {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.vip.dashboard', ['wedding_id' => $wedding->id]) }}"
       class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">← VIP Dashboard</a>
    <h1 class="text-2xl font-semibold text-slate-100">📷 Scan QR Check-In</h1>
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
                    ✕ Hentikan
                </button>
            </div>

            {{-- Camera container --}}
            <div class="relative bg-slate-900 rounded-2xl overflow-hidden min-h-[280px] flex items-center justify-center" id="cam-wrap">
                <div id="cam-placeholder" class="text-center text-slate-600">
                    <div class="text-5xl mb-3">📷</div>
                    <p class="text-sm">Kamera belum aktif</p>
                </div>
                <div id="qr-reader"></div>

                {{-- Scan frame overlay (visible saat kamera aktif) --}}
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
                📷 Mulai Kamera
            </button>

            {{-- Result box --}}
            <div id="result-box" class="result-box hidden"></div>
        </div>

        {{-- Manual input (fallback) --}}
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

    {{-- ── Panel Kanan: Log check-in hari ini ── --}}
    <div>
        <div class="bg-slate-800 rounded-2xl p-6 border border-white/5 h-full">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-slate-200 text-sm">📋 Log Check-In Sesi Ini</h2>
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

{{-- Data PHP dipisahkan ke JSON block agar JS linter tidak salah parse Blade expressions --}}
<script type="application/json" id="scan-data">
{
    "weddingId":  {{ $wedding->id }},
    "checkinUrl": "{{ route('admin.vip.scan.checkin') }}",
    "csrf":       "{{ csrf_token() }}",
    "total":      {{ $stats['total'] }},
    "checkedIn":  {{ $stats['checked_in'] }}
}
</script>

<script @nonce>
const _d          = JSON.parse(document.getElementById('scan-data').textContent);
const WEDDING_ID  = _d.weddingId;
const CHECKIN_URL = _d.checkinUrl;
const CSRF        = _d.csrf;
const TOTAL       = _d.total;

let html5QrCode   = null;
let checkedInCount = _d.checkedIn;
let logCount      = 0;
let lastScanned   = null;   // anti-duplikat cepat

// ─── Scanner ────────────────────────────────────────────────────────────────

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
        () => {}  // error diabaikan (scanning noise)
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

// ─── Success callback ────────────────────────────────────────────────────────

function onScanSuccess(decodedText) {
    // Anti-duplikat: abaikan scan yg sama dalam 3 detik
    if (decodedText === lastScanned) return;
    lastScanned = decodedText;
    setTimeout(() => { lastScanned = null; }, 3000);

    doCheckIn(decodedText);
}

// ─── Check-in API ────────────────────────────────────────────────────────────

function doCheckIn(url) {
    fetch(CHECKIN_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ wedding_id: WEDDING_ID, url: url }),
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

// ─── UI Helpers ─────────────────────────────────────────────────────────────

function showResult(data) {
    const box = document.getElementById('result-box');
    box.classList.remove('hidden', 'result-ok', 'result-already', 'result-error');

    if (data.status === 'ok') {
        box.classList.add('result-ok');
        box.innerHTML = `
            <div class="flex items-center gap-3">
                <span class="text-3xl">✅</span>
                <div>
                    <div class="font-bold text-green-300 text-base">${data.guest.name}</div>
                    ${data.guest.group ? `<div class="text-xs text-green-400/70">${data.guest.group}</div>` : ''}
                    <div class="text-xs text-green-400 mt-1">✓ Check-in berhasil — ${data.guest.pax ?? 1} orang</div>
                </div>
            </div>`;
        playBeep('ok');
    } else if (data.status === 'already') {
        box.classList.add('result-already');
        box.innerHTML = `
            <div class="flex items-center gap-3">
                <span class="text-3xl">⚠️</span>
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
                <span class="text-3xl">❌</span>
                <div class="text-red-300 text-sm">${data.message}</div>
            </div>`;
        playBeep('error');
    }

    // Auto-hide setelah 5 detik
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
        (data.status === 'ok' ? 'bg-green-900/20 border border-green-500/20' :
         data.status === 'already' ? 'bg-amber-900/20 border border-amber-500/20' :
         'bg-red-900/20 border border-red-500/20');

    const icon = data.status === 'ok' ? '✅' : data.status === 'already' ? '⚠️' : '❌';
    const name = data.guest ? data.guest.name : (data.message ?? 'Error');
    const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

    li.innerHTML = `
        <span class="text-lg shrink-0">${icon}</span>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-slate-200 truncate">${name}</div>
            ${data.guest?.group ? `<div class="text-xs text-slate-500 truncate">${data.guest.group}</div>` : ''}
        </div>
        <span class="text-xs text-slate-500 shrink-0">${now}</span>`;

    list.insertBefore(li, list.firstChild);
}

function updateProgress() {
    document.getElementById('prog-in').textContent = checkedInCount;
    const pct = TOTAL > 0 ? Math.round(checkedInCount / TOTAL * 100) : 0;
    document.getElementById('prog-bar').style.width = pct + '%';
}

function setCamStatus(msg, color) {
    const el = document.getElementById('cam-status');
    const dot = document.getElementById('cam-dot');
    el.childNodes[1].textContent = ' ' + msg;
    dot.className = 'w-2 h-2 rounded-full inline-block ' +
        { green: 'bg-green-400 animate-pulse', yellow: 'bg-yellow-400 animate-pulse',
          red: 'bg-red-400', slate: 'bg-slate-500' }[color];
}

function playBeep(type) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = type === 'ok' ? 880 : type === 'warn' ? 440 : 220;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
        osc.start(); osc.stop(ctx.currentTime + 0.3);
    } catch (_) {}
}

// ─── Manual form ─────────────────────────────────────────────────────────────

document.getElementById('manual-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const url = document.getElementById('manual-url').value.trim();
    if (!url) return;
    doCheckIn(url);
    document.getElementById('manual-url').value = '';
});
</script>
@endpush
