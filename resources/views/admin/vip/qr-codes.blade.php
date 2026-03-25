@extends('admin.layout')

@section('title', 'QR Code Tamu — ' . $wedding->bride_name)

@push('styles')
<style @nonce>
@media print {
    .no-print { display: none !important; }
    .qr-card { break-inside: avoid; page-break-inside: avoid; }
    body { background: white !important; }
}
</style>
@endpush

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-6 no-print">
    <div>
        <a href="{{ route('admin.vip.dashboard', ['wedding_id' => $wedding->id]) }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">← VIP Dashboard</a>
        <h1 class="text-2xl font-semibold text-slate-100">🔲 QR Code per Tamu</h1>
        <p class="text-slate-400 text-sm mt-1">{{ $guests->count() }} tamu terdaftar — scan QR untuk buka undangan personal</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.vip.scan', ['wedding_id' => $wedding->id]) }}"
           class="px-4 py-2 rounded-lg bg-teal-700 hover:bg-teal-600 text-white text-sm font-bold transition shadow flex items-center gap-2">
            📷 Scan Check-In
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-bold transition shadow">
            🖨️ Print Semua
        </button>
    </div>
</div>

@if($guests->isEmpty())
<div class="text-center py-20 text-slate-500">
    <div class="text-4xl mb-3">👥</div>
    <p>Belum ada tamu terdaftar.</p>
    <a href="{{ route('admin.guests.create', ['wedding_id' => $wedding->id]) }}" class="mt-4 inline-block text-amber-400 hover:underline text-sm">+ Tambah Tamu</a>
</div>
@else

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4">
    @foreach($guests as $guest)
    @php
        $guestUrl = url("/{$wedding->slug}?to=" . Str::slug($guest->guest_name));
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&ecc=M&data=" . urlencode($guestUrl);
    @endphp
    <div class="qr-card bg-white rounded-xl p-4 text-center shadow-md border border-slate-200">
        <img src="{{ $qrUrl }}" alt="QR {{ $guest->guest_name }}"
             class="w-40 h-40 mx-auto mb-3 rounded" loading="lazy">
        <p class="text-sm font-semibold text-slate-800 leading-tight truncate" title="{{ $guest->guest_name }}">
            {{ $guest->guest_name }}
        </p>
        @if($guest->group_name)
        <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $guest->group_name }}</p>
        @endif
        <a href="{{ $guestUrl }}" target="_blank"
           class="mt-2 inline-block text-xs text-indigo-600 hover:underline no-print truncate max-w-full">
            Buka link →
        </a>
    </div>
    @endforeach
</div>

@endif
@endsection

@push('scripts')
<script @nonce>
// Tambah Str::slug PHP equivalent di blade sudah handle — tidak perlu JS tambahan
</script>
@endpush
