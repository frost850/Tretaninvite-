@extends('admin.layout')

@section('title', 'Daftar Tamu — ' . $wedding->bride_name . ($wedding->groom_name ? ' & ' . $wedding->groom_name : ''))

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.weddings.index') }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">← Kembali ke Daftar Undangan</a>
            <h1 class="text-2xl font-semibold text-slate-100">Daftar Tamu</h1>
            <p class="text-slate-400 text-sm mt-1">
                @if($wedding->groom_name)
                    {{ $wedding->bride_name }} & {{ $wedding->groom_name }}
                @else
                    🎂 {{ $wedding->bride_name }}
                @endif
                <span class="font-mono text-slate-500">({{ $wedding->slug }})</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.guests.create', ['wedding_id' => $wedding->id]) }}"
               class="px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-bold transition shadow-md">
               Tambah Tamu
            </a>
            <a href="{{ route('admin.guests.import', ['wedding_id' => $wedding->id]) }}"
               class="px-4 py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">
               Import Excel
            </a>
            <a href="{{ route('admin.guests.export', ['wedding_id' => $wedding->id]) }}"
               class="px-4 py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">
               Export Excel
            </a>
        </div>
    </div>

    @php $guestCount = $wedding->guests()->count(); $guestLimit = $wedding->guestLimit(); @endphp
    <div class="flex flex-wrap gap-4 text-sm text-slate-400 mb-4">
        <span>Total: <strong class="text-slate-200">{{ $guestCount }}</strong>{{ $guestLimit ? ' / ' . $guestLimit : '' }} tamu</span>
        <span>Sudah buka: <strong class="text-slate-200">{{ $wedding->guests()->whereNotNull('first_opened_at')->count() }}</strong></span>
        <span>Sudah konfirmasi: <strong class="text-slate-200">{{ $wedding->guests()->whereNotNull('replied_at')->count() }}</strong></span>
    </div>

    @if($wedding->isTrial())
    @php $sisa = max(0, $guestLimit - $guestCount); @endphp
    <div class="adm-card border-l-4 {{ $sisa === 0 ? 'border-red-500' : 'border-amber-500' }} p-5 mb-5">
        <div class="flex items-start gap-3">
            <span class="text-2xl">{{ $sisa === 0 ? '🚫' : '🧪' }}</span>
            <div class="flex-1 min-w-0">
                <p class="font-semibold {{ $sisa === 0 ? 'text-red-300' : 'text-amber-300' }} mb-1">
                    Paket Uji Coba — Sisa slot: <strong>{{ $sisa }} dari {{ $guestLimit }} tamu</strong>
                </p>
                @if($sisa === 0)
                <p class="text-red-300/80 text-sm mb-2">Batas tamu sudah tercapai. Upgrade paket untuk menambah lebih banyak tamu.</p>
                @endif
                <div class="text-xs text-slate-400 mt-2 space-y-1">
                    <p class="font-medium text-slate-300">📋 Format kolom nama yang diterima saat import Excel:</p>
                    <div class="flex flex-wrap gap-1.5 mt-1">
                        @foreach(['Nama_Tamu','Nama Tamu','nama','guest_name','guest','name'] as $fmt)
                        <code class="bg-white/5 border border-white/10 px-2 py-0.5 rounded text-amber-300">{{ $fmt }}</code>
                        @endforeach
                    </div>
                    <p class="text-slate-500 mt-1">Jika tanpa header, kolom pertama otomatis dianggap nama. Kolom WA opsional: <code class="bg-white/5 px-1 rounded">No_WA</code>, <code class="bg-white/5 px-1 rounded">whatsapp</code>, <code class="bg-white/5 px-1 rounded">HP</code></p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('success'))
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-green-500">
            <div class="text-green-300 text-sm">{{ session('success') }}</div>
        </div>
    @endif

    @if($guests->isEmpty())
        <div class="adm-card p-10 text-center text-slate-400">
            <div class="text-5xl mb-4">👥</div>
            <p class="mb-4 text-slate-300">Belum ada tamu. Tambah manual atau import dari Excel.</p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('admin.guests.create', ['wedding_id' => $wedding->id]) }}"
                   class="inline-block px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-bold">Tambah Tamu</a>
                <a href="{{ route('admin.guests.import', ['wedding_id' => $wedding->id]) }}"
                   class="inline-block px-4 py-2 rounded-lg border border-white/10 text-slate-300 text-sm">Import Excel</a>
            </div>
        </div>
    @else
        <div class="adm-card overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm adm-table">
                    <thead>
                        <tr>
                            <th class="text-left py-3 px-4">Nama</th>
                            <th class="text-left py-3 px-4">RSVP</th>
                            <th class="text-left py-3 px-4">Buka</th>
                            <th class="text-left py-3 px-4">Link & Share</th>
                            <th class="text-right py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guests as $g)
                            @php $url = $g->invitationUrl(); @endphp
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-200">{{ $g->guest_name }}</div>
                                    @if($g->group_name)
                                    <div class="text-xs text-amber-400 font-medium">👥 {{ $g->group_name }}</div>
                                    @endif
                                    <div class="text-xs text-slate-500 font-mono">{{ $g->slug_name ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($g->replied_at)
                                        <span class="{{ $g->is_attending ? 'text-green-400' : 'text-red-400' }} font-medium">{{ $g->is_attending ? 'Hadir' : 'Tidak' }}</span>
                                        @if($g->pax) <span class="text-slate-500 text-xs">({{ $g->pax }} org)</span> @endif
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($g->first_opened_at)
                                        <span class="text-green-400">{{ $g->open_count }}x</span>
                                    @else
                                        <span class="text-slate-500">Belum</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <button type="button"
                                            data-url="{{ $url }}"
                                            onclick="copyLink(this)"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs border border-white/10 text-slate-300 hover:bg-white/5 hover:border-amber-500/30 hover:text-amber-300 transition">
                                            <span class="copy-icon">📋</span> <span class="copy-text">Salin Link</span>
                                        </button>
                                        <a href="https://wa.me/?text={{ urlencode('Halo ' . $g->guest_name . ', berikut link ' . ($wedding->groom_name ? 'undangan pernikahan' : 'undangan ulang tahun') . ' kami: ' . $url) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs border border-green-500/30 text-green-300 hover:bg-green-900/20 transition">
                                            💬 WA
                                        </a>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.guests.edit', $g) }}" class="text-amber-400 hover:text-amber-300 hover:underline">Edit</a>
                                    <form action="{{ route('admin.guests.destroy', $g) }}" method="post" class="inline ml-2"
                                          data-adm-confirm="Hapus tamu ini?"
                                          data-adm-ajax data-adm-danger data-adm-remove-closest="tr">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mb-4">{{ $guests->appends(['wedding_id' => $wedding->id])->links() }}</div>

        {{-- Salin Semua Link --}}
        <div class="adm-card p-5">
            <p class="text-sm font-medium text-slate-300 mb-3">Salin semua link sekaligus:</p>
            <div class="flex flex-wrap gap-3">
                <button type="button" onclick="copyAllLinks()"
                        class="px-4 py-2 text-sm rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold transition shadow-md">
                    📋 Salin Semua Link
                </button>
                <button type="button" onclick="copyWaMessages()"
                        class="px-4 py-2 text-sm rounded-lg border border-green-500/30 text-green-300 hover:bg-green-900/20 transition">
                    💬 Salin Semua Pesan WA
                </button>
            </div>
            <textarea id="all-links-area" class="mt-3 w-full text-xs font-mono rounded-xl border border-white/10 p-3 bg-black/20 text-slate-300 hidden" rows="6" readonly></textarea>
        </div>
    @endif

    <p class="mt-6">
        <a href="{{ route('admin.weddings.index') }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm">← Kembali ke daftar undangan</a>
    </p>

@endsection

{{-- Toast --}}
@push('scripts')
<div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-800 border border-white/10 text-white text-sm px-4 py-2 rounded-full shadow-xl opacity-0 transition-opacity duration-300 pointer-events-none z-50">Link disalin!</div>

@php
    $eventLabel = $wedding->groom_name ? 'undangan pernikahan' : 'undangan ulang tahun';
    $allLinks   = $guests->map(fn($g) => $g->guest_name . ': ' . $g->invitationUrl())->implode("\n");
    $allWa      = $guests->map(fn($g) => 'Halo ' . $g->guest_name . ', berikut link ' . $eventLabel . ' kami: ' . $g->invitationUrl())->implode("\n\n");
@endphp
<div id="data-all-links" hidden>{{ $allLinks }}</div>
<div id="data-all-wa" hidden>{{ $allWa }}</div>

<script @nonce>
const ALL_LINKS = document.getElementById('data-all-links').textContent;
const ALL_WA    = document.getElementById('data-all-wa').textContent;

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.opacity = '1';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => { t.style.opacity = '0'; }, 2000);
}

function copyLink(btn) {
    const url = btn.dataset.url;
    const doIt = () => {
        const span = btn.querySelector('.copy-text');
        const icon = btn.querySelector('.copy-icon');
        span.textContent = 'Tersalin!';
        icon.textContent = '✅';
        setTimeout(() => { span.textContent = 'Salin Link'; icon.textContent = '📋'; }, 2000);
        showToast('Link berhasil disalin!');
    };
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(doIt).catch(() => fallbackCopy(url, doIt));
    } else {
        fallbackCopy(url, doIt);
    }
}

function fallbackCopy(text, callback) {
    const el = document.createElement('textarea');
    el.value = text;
    el.style.position = 'fixed';
    el.style.opacity = '0';
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    if (callback) callback();
}

function copyAllLinks() {
    const area = document.getElementById('all-links-area');
    area.value = ALL_LINKS;
    area.classList.remove('hidden');
    fallbackCopy(ALL_LINKS);
    showToast('Semua link disalin!');
}

function copyWaMessages() {
    const area = document.getElementById('all-links-area');
    area.value = ALL_WA;
    area.classList.remove('hidden');
    fallbackCopy(ALL_WA);
    showToast('Semua pesan WA disalin!');
}
</script>
@endpush

