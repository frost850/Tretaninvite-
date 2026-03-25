@extends('admin.layout')

@section('title', 'Daftar Undangan')

@section('content')

{{-- ═══ PAGE HEADER ═══ --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-100">Daftar Undangan</h1>
        <p class="text-slate-500 text-sm mt-0.5">Kelola semua undangan dan pesanan</p>
    </div>
    <a href="{{ route('admin.weddings.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold shadow-lg transition-all text-sm">
        + Buat Undangan Baru
    </a>
</div>

@if(session('success'))
    {{-- Shown as toast via layout --}}
@endif
@if(session('error'))
    {{-- Shown as toast via layout --}}
@endif

{{-- ═══ STATISTIK ═══ --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <div class="adm-card p-4 text-center">
        <div class="text-2xl font-bold text-violet-400">{{ $weddings->count() }}</div>
        <div class="text-xs text-slate-500 mt-1 font-medium">Total Undangan</div>
    </div>
    <div class="adm-card p-4 text-center">
        <div class="text-2xl font-bold text-blue-400">{{ $orderStats['total'] }}</div>
        <div class="text-xs text-slate-500 mt-1 font-medium">Total Pesanan</div>
    </div>
    <a href="{{ route('admin.orders.index', ['status' => 'baru']) }}"
       class="adm-card p-4 text-center {{ $orderStats['baru'] > 0 ? 'ring-1 ring-orange-500/40' : '' }}">
        <div class="text-2xl font-bold {{ $orderStats['baru'] > 0 ? 'text-orange-400' : 'text-slate-400' }}">{{ $orderStats['baru'] }}</div>
        <div class="text-xs mt-1 font-medium {{ $orderStats['baru'] > 0 ? 'text-orange-400' : 'text-slate-500' }}">Pesanan Baru</div>
    </a>
    <a href="{{ route('admin.orders.index') }}"
       class="adm-card p-4 text-center {{ $orderStats['menunggu_pembayaran'] > 0 ? 'ring-1 ring-blue-500/40' : '' }}">
        <div class="text-2xl font-bold {{ $orderStats['menunggu_pembayaran'] > 0 ? 'text-blue-400' : 'text-slate-400' }}">{{ $orderStats['menunggu_pembayaran'] }}</div>
        <div class="text-xs mt-1 font-medium {{ $orderStats['menunggu_pembayaran'] > 0 ? 'text-blue-400' : 'text-slate-500' }}">Bukti Dikirim</div>
    </a>
    <div class="adm-card p-4 text-center">
        <div class="text-2xl font-bold text-green-400">{{ $orderStats['lunas'] }}</div>
        <div class="text-xs text-slate-500 mt-1 font-medium">Lunas</div>
    </div>
    <div class="adm-card p-4 text-center">
        <div class="text-2xl font-bold text-indigo-400">{{ $orderStats['diproses'] }}</div>
        <div class="text-xs text-slate-500 mt-1 font-medium">Diproses</div>
    </div>
</div>

{{-- ═══ PESANAN BUTUH PERHATIAN ═══ --}}
@if($urgentOrders->isNotEmpty())
<div class="adm-card p-0 mb-5 overflow-hidden border-l-4 border-orange-500">
    <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-white/5">
        <h2 class="font-bold text-orange-400 flex items-center gap-2 text-sm">
            &#x1F514; Perlu Tindakan
            <span class="px-2 py-0.5 text-xs rounded-full bg-orange-900/50 text-orange-300 border border-orange-700/50">{{ $urgentOrders->count() }}</span>
        </h2>
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium">Lihat semua &#x2192;</a>
    </div>
    <div class="divide-y divide-white/5">
        @foreach($urgentOrders as $o)
        <div class="px-5 py-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-slate-200">{{ $o->customer_name }}<span class="text-slate-500 font-normal text-xs ml-1">&#8212; {{ $o->template }}</span></div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $o->created_at->diffForHumans() }} &nbsp;&middot;&nbsp;
                    @if($o->payment_status === 'menunggu_konfirmasi')
                        <span class="text-blue-400 font-medium">&#x1F4B3; Bukti bayar dikirim &#8212; konfirmasi sekarang</span>
                    @else
                        <span class="text-orange-400 font-medium">&#x1F195; Pesanan baru masuk</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 items-center shrink-0">
                @if($o->payment_status === 'menunggu_konfirmasi')
                    <form action="{{ route('admin.orders.confirm-payment', $o->id) }}" method="POST"
                          data-adm-confirm="Konfirmasi lunas pesanan {{ addslashes($o->customer_name ?? '') }}?"
                          data-adm-ajax data-adm-reload>
                        @csrf @method('PATCH')
                        <button class="btn-adm bg-gradient-to-r from-green-500 to-emerald-500 text-white hover:from-green-600 hover:to-emerald-600">&#x2705; Konfirmasi Lunas</button>
                    </form>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="btn-adm border border-white/10 text-slate-400 hover:bg-white/5">Detail</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ═══ PESANAN LUNAS SIAP DIBUAT ═══ --}}
@if($readyOrders->isNotEmpty())
<div class="adm-card p-0 mb-5 overflow-hidden border-l-4 border-purple-500">
    <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-white/5">
        <h2 class="font-bold text-purple-400 flex items-center gap-2 text-sm">
            &#x1F6E0; Siap Dibuat Undangan
            <span class="px-2 py-0.5 text-xs rounded-full bg-purple-900/50 text-purple-300 border border-purple-700/50">{{ $readyOrders->count() }}</span>
        </h2>
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium">Lihat semua &#x2192;</a>
    </div>
    <div class="divide-y divide-white/5">
        @foreach($readyOrders as $o)
        @php
            $isPkg   = $o->package ?? 'basic';
            $pkgColor = match($isPkg) { 'vip' => 'text-purple-400', 'premium' => 'text-amber-400', default => 'text-blue-400' };
            $pkgLabel = match($isPkg) { 'vip' => '&#x265B; VIP', 'premium' => '&#x2B50; Premium', default => '&#x1F499; Basic' };
            $nama    = $o->bride_name . ($o->groom_name ? ' & ' . $o->groom_name : '');
        @endphp
        <div class="px-5 py-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-slate-200">
                    {{ $nama }} <span class="text-xs ml-1 {{ $pkgColor }}">{!! $pkgLabel !!}</span>
                    <span class="text-slate-500 font-normal text-xs ml-1">&#8212; {{ $o->template }}</span>
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    &#x2705; Lunas {{ $o->updated_at->diffForHumans() }}
                    @if($o->event_date) &nbsp;&middot;&nbsp; &#x1F4C5; {{ $o->event_date->format('d M Y') }} @endif
                </div>
            </div>
            <a href="{{ route('admin.weddings.create.form', $o->template) }}?order_id={{ $o->id }}"
               class="shrink-0 btn-adm text-white" style="background:linear-gradient(135deg,#7c3aed,#9333ea);">
                &#x1F6E0; Buat Undangan &#x2192;
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ═══ FILTER CATEGORIES ═══ --}}
@php
    $allTemplates = \App\Services\TemplateRegistry::all();
    $weddingList    = $weddings->filter(fn($w) => ($allTemplates[$w->template]['category'] ?? 'wedding') === 'wedding');
    $birthdayList   = $weddings->filter(fn($w) => ($allTemplates[$w->template]['category'] ?? 'wedding') === 'birthday');
    $greetingList   = $weddings->filter(fn($w) => ($allTemplates[$w->template]['category'] ?? 'wedding') === 'greeting');
@endphp

{{-- ═══ SEARCH & FILTER BAR ═══ --}}
<div class="adm-card px-4 py-3 mb-5 flex flex-wrap items-center gap-3" x-data="{ q: '', pkg: 'all' }">
    {{-- Search --}}
    <div class="relative flex-1 min-w-[180px]">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
        <input type="text" x-model="q" placeholder="Cari nama, slug..."
               class="w-full pl-8 pr-3 py-1.5 text-sm bg-white/5 border border-white/10 rounded-lg text-slate-200 placeholder-slate-600 focus:border-amber-500/50 focus:outline-none">
    </div>
    {{-- Paket filter --}}
    <div class="flex items-center gap-1 flex-wrap">
        <span class="text-xs text-slate-500 mr-1">Paket:</span>
        @foreach(['all'=>'Semua','trial'=>'Trial','basic'=>'Basic','premium'=>'Premium','vip'=>'VIP'] as $key=>$lbl)
        <button @click="pkg='{{ $key }}'"
                :class="pkg==='{{ $key }}' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'text-slate-500 border-white/10 hover:text-slate-300'"
                class="px-2.5 py-1 text-xs rounded-lg border font-medium transition">{{ $lbl }}</button>
        @endforeach
    </div>
    {{-- Counters (live) --}}
    <div class="text-xs text-slate-600 shrink-0" x-text="'Menampilkan ' + document.querySelectorAll('.inv-card:not([style*=none])').length + ' undangan'"></div>

    {{-- Filtering logic --}}
    <script @nonce>
    document.addEventListener('alpine:init', () => {
        // We'll apply filtering via mutation / watches below
    });
    </script>
    <template x-effect="
        document.querySelectorAll('.inv-card').forEach(el => {
            const name   = (el.dataset.name   || '').toLowerCase();
            const slug   = (el.dataset.slug   || '').toLowerCase();
            const pkgVal = (el.dataset.pkg    || '').toLowerCase();
            const qMatch  = q === '' || name.includes(q.toLowerCase()) || slug.includes(q.toLowerCase());
            const pkgMatch = pkg === 'all' || pkgVal === pkg;
            el.style.display = (qMatch && pkgMatch) ? '' : 'none';
        });
    "></template>
</div>


{{-- ==================================================== --}}
{{-- ==  BAGIAN 1: UNDANGAN PERNIKAHAN                 == --}}
{{-- ==================================================== --}}
<div class="flex items-center justify-between mb-3 mt-1">
    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
        &#x1F48D; Undangan Pernikahan
        <span class="text-xs font-normal text-slate-500 bg-white/5 px-2 py-0.5 rounded-full border border-white/10">{{ $weddingList->count() }}</span>
    </h2>
    <span class="text-slate-600 text-xs hidden sm:block">&#x2460; Buat &#x2192; &#x2461; Import Tamu &#x2192; &#x2462; Bagikan Link</span>
</div>

@if($weddingList->isEmpty())
    <div class="adm-card p-10 text-center mb-8">
        <div class="text-5xl mb-3">&#x1F48D;</div>
        <p class="mb-4 text-slate-400 font-medium">Belum ada undangan pernikahan.</p>
        <a href="{{ route('admin.weddings.create') }}" class="inline-block px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold text-sm">Buat Undangan Pernikahan</a>
    </div>
@else
    <div class="space-y-4 mb-10">
    @foreach($weddingList as $w)
    @php
        $guestCount  = $w->guests()->count();
        $openedCount = $w->guests()->whereNotNull('first_opened_at')->count();
        $rsvpHadir   = $w->guests()->where('is_attending', true)->count();
        $rsvpTidak   = $w->guests()->where('is_attending', false)->count();
        $rsvpBelum   = $guestCount - $w->guests()->whereNotNull('replied_at')->count();
        $checkinCount = $w->guests()->whereNotNull('checked_in_at')->count();
        $step        = $guestCount === 0 ? 1 : ($openedCount === 0 ? 2 : 3);
        $isVip       = $w->package === 'vip';
        $isPremiumOrVip = $w->isPremium() || $w->isVip();
        $portalToken = $isPremiumOrVip ? \App\Models\Order::where('wedding_id', $w->id)->latest()->value('public_token') : null;
        $pkgMeta = match($w->package ?? 'basic') {
            'trial'   => ['label'=>'Trial',      'cls'=>'bg-amber-900/40 text-amber-300 border-amber-700/40'],
            'basic'   => ['label'=>'Basic',      'cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
            'vip'     => ['label'=>'&#x265B; VIP',   'cls'=>'bg-purple-900/40 text-purple-300 border-purple-700/40'],
            'premium' => ['label'=>'&#x2B50; Premium','cls'=>'bg-yellow-900/40 text-yellow-300 border-yellow-700/40'],
            default   => ['label'=>'Basic',      'cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
        };
        $stepMeta = match($step) {
            1 => ['label'=>'&#x2460; Belum ada tamu',    'cls'=>'bg-orange-900/40 text-orange-300 border-orange-700/40'],
            2 => ['label'=>'&#x2461; Tamu belum membuka','cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
            3 => ['label'=>'&#x2462; Ada yang membuka',  'cls'=>'bg-green-900/40 text-green-300 border-green-700/40'],
        };
    @endphp
    <div class="adm-card overflow-hidden inv-card"
         data-name="{{ strtolower($w->bride_name . ' ' . $w->groom_name) }}"
         data-slug="{{ $w->slug }}"
         data-pkg="{{ $w->package }}"
         x-data="{ extOpen: false, copiedTrack: false, copiedLink: false }">

        {{-- ── CARD BODY ── --}}
        <div class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row gap-4">

                {{-- LEFT: Info --}}
                <div class="flex-1 min-w-0">
                    {{-- Name + slug + aktif --}}
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        @if(!$w->is_active)
                            <span class="badge bg-red-900/50 text-red-300 border-red-700/40">&#x23F8; Nonaktif</span>
                        @endif
                        <span class="font-bold text-slate-100 text-[15px]">{{ $w->bride_name }} &amp; {{ $w->groom_name }}</span>
                        <span class="font-mono text-xs text-slate-500 bg-white/5 px-2 py-0.5 rounded border border-white/10">{{ $w->slug }}</span>
                        @if($isVip && $w->vip_password)
                            <span class="badge bg-amber-900/40 text-amber-300 border-amber-700/40">&#x1F512; Berpassword</span>
                        @endif
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span class="badge {!! $pkgMeta['cls'] !!}">{!! $pkgMeta['label'] !!}{{ $w->isExpired() ? ' (Kadaluarsa)' : '' }}</span>
                        <span class="badge {!! $stepMeta['cls'] !!}">{!! $stepMeta['label'] !!}</span>
                    </div>

                    {{-- Meta row --}}
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mb-3">
                        <span>&#x1F492; {{ $allTemplates[$w->template]['label'] ?? ucfirst($w->template) }}</span>
                        @if($w->event_date)<span>&#x1F4C5; {{ $w->event_date->format('d M Y') }}</span>@endif
                        <span>&#x1F465; {{ $guestCount }} tamu{{ $w->guestLimit() !== null ? ' / maks '.$w->guestLimit() : '' }}</span>
                        @if($guestCount > 0)<span>&#x1F4EC; {{ $openedCount }} dibuka</span>@endif
                        @if($w->trial_expires_at)<span class="{{ $w->isExpired() ? 'text-red-400 font-semibold' : 'text-amber-400' }}">&#x23F1; s/d {{ $w->trial_expires_at->format('d M Y') }}</span>@endif
                    </div>

                    {{-- RSVP Stats mini bar (tampil jika ada tamu) --}}
                    @if($guestCount > 0)
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <span class="font-semibold text-slate-400">RSVP:</span>
                        <span class="flex items-center gap-1 text-green-400"><span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>Hadir {{ $rsvpHadir }}</span>
                        <span class="flex items-center gap-1 text-red-400"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>Tidak {{ $rsvpTidak }}</span>
                        <span class="flex items-center gap-1 text-slate-500"><span class="w-2 h-2 rounded-full bg-slate-600 inline-block"></span>Belum {{ $rsvpBelum }}</span>
                        @if($isVip && $checkinCount > 0)
                        <span class="flex items-center gap-1 text-indigo-400"><span class="w-2 h-2 rounded-full bg-indigo-400 inline-block"></span>Check-in {{ $checkinCount }}</span>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- RIGHT: Actions --}}
                <div class="shrink-0 flex flex-col gap-2 sm:items-end">

                    {{-- Baris 1: Primary --}}
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('invitation.show', $w->slug) }}" target="_blank" class="btn-adm border border-white/10 text-slate-400 hover:bg-white/5 hover:text-slate-200">&#x1F441; Lihat</a>
                        <a href="{{ route('admin.weddings.edit', $w->id) }}" class="btn-adm bg-gradient-to-r from-violet-600 to-indigo-600 text-white hover:from-violet-700 hover:to-indigo-700">&#x270F; Edit</a>
                        <a href="{{ route('admin.guests.index', ['wedding_id' => $w->id]) }}" class="btn-adm border border-violet-500/30 text-violet-300 hover:bg-violet-900/20">&#x1F465; Tamu ({{ $guestCount }})</a>
                        <a href="{{ route('admin.guests.create', ['wedding_id' => $w->id]) }}" class="btn-adm border border-violet-400/30 text-violet-400 hover:bg-violet-900/20">+ Tamu</a>
                    </div>

                    {{-- Baris 2: Import / Export / Tracking --}}
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('admin.guests.import', ['wedding_id' => $w->id]) }}" class="btn-adm bg-gradient-to-r from-orange-500 to-pink-500 text-white hover:from-orange-600 hover:to-pink-600">&#x2B06; Import</a>
                        @if($guestCount > 0)
                        <a href="{{ route('admin.guests.export') }}?wedding_id={{ $w->id }}" class="btn-adm border border-emerald-500/30 text-emerald-400 hover:bg-emerald-900/20">&#x2B07; Export</a>
                        @endif
                        <a href="{{ route('tracking.show', $w->tracking_token) }}" target="_blank" class="btn-adm border border-green-500/30 text-green-400 hover:bg-green-900/20">&#x1F4CA; Tracking</a>
                    </div>

                    {{-- Baris 3: VIP Features (hanya VIP) --}}
                    @if($isVip)
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('admin.vip.rsvp-live', $w->id) }}" class="btn-adm border border-purple-500/30 text-purple-300 hover:bg-purple-900/20">&#x1F7E3; RSVP Live</a>
                        <a href="{{ route('admin.vip.qr-codes') }}?wedding_id={{ $w->id }}" class="btn-adm border border-indigo-500/30 text-indigo-300 hover:bg-indigo-900/20">&#x1F4F1; QR Code</a>
                        <a href="{{ route('admin.vip.scan') }}?wedding_id={{ $w->id }}" class="btn-adm border border-cyan-500/30 text-cyan-300 hover:bg-cyan-900/20">&#x1F4F7; Scan</a>
                        <a href="{{ route('admin.vip.guestbook') }}?wedding_id={{ $w->id }}" class="btn-adm border border-teal-500/30 text-teal-300 hover:bg-teal-900/20">&#x1F4DD; Guestbook</a>
                    </div>
                    @endif

                    {{-- Baris 4: Manajemen --}}
                    <div class="flex flex-wrap gap-1.5">
                        {{-- Toggle Aktif --}}
                        <form action="{{ route('admin.weddings.toggle-active', $w->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            @if($w->is_active)
                                <button class="btn-adm border border-slate-500/40 text-slate-400 hover:bg-slate-800/40">&#x23F8; Nonaktifkan</button>
                            @else
                                <button class="btn-adm border border-green-500/40 text-green-400 hover:bg-green-900/20">&#x25B6; Aktifkan</button>
                            @endif
                        </form>

                        {{-- Paksa Expired --}}
                        @if(!$w->isExpired())
                        <form action="{{ route('admin.weddings.force-expire', $w->id) }}" method="POST" class="inline"
                              data-adm-confirm="Paksa expired undangan {{ addslashes($w->bride_name) }}? Link tetap bisa dibuka dalam mode arsip (RSVP ditutup)."
                              data-adm-ajax data-adm-reload>
                            @csrf @method('PATCH')
                            <button class="btn-adm border border-orange-500/40 text-orange-400 hover:bg-orange-900/20">&#x26A1; Paksa Expired</button>
                        </form>
                        @endif

                        {{-- Perpanjang --}}
                        <div class="relative">
                            <button @click="extOpen = !extOpen" class="btn-adm border border-amber-500/30 text-amber-300 hover:bg-amber-900/20">&#x23F1; Perpanjang</button>
                            <div x-show="extOpen" @click.outside="extOpen = false"
                                 class="absolute right-0 top-9 z-30 w-52 bg-slate-900 border border-white/10 rounded-xl shadow-2xl p-3" style="display:none">
                                <p class="text-xs font-semibold text-amber-300 mb-2">Tambah masa aktif:</p>
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach([7, 14, 21, 30] as $d)
                                    <form action="{{ route('admin.weddings.extend', $w->id) }}" method="post">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="days" value="{{ $d }}">
                                        <button class="px-2 py-1 rounded-lg bg-amber-900/40 hover:bg-amber-900/60 text-amber-300 text-xs font-semibold border border-amber-700/40">+{{ $d }}h</button>
                                    </form>
                                    @endforeach
                                </div>
                                <form action="{{ route('admin.weddings.extend', $w->id) }}" method="post" class="flex gap-1">
                                    @csrf @method('PATCH')
                                    <input type="number" name="days" min="1" max="365" placeholder="Kustom hari" class="flex-1 border border-white/10 rounded-lg px-2 py-1 text-xs bg-white/5 text-slate-200" required>
                                    <button class="px-2 py-1 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold">OK</button>
                                </form>
                            </div>
                        </div>

                        {{-- Hapus --}}
                        <form action="{{ route('admin.weddings.destroy', $w->id) }}" method="post" class="inline"
                              data-adm-confirm="Hapus undangan {{ addslashes($w->bride_name) }} &amp; {{ addslashes($w->groom_name) }}? Semua data tamu ikut terhapus."
                              data-adm-ajax data-adm-danger data-adm-remove-closest=".adm-card">
                            @csrf @method('DELETE')
                            <button class="btn-adm border border-red-500/30 text-red-400 hover:bg-red-900/20">&#x1F5D1; Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── LINK BAR ── --}}
        <div class="border-t border-white/5 bg-slate-900/40 px-4 sm:px-5 py-3 flex flex-wrap gap-3">
            {{-- Link Undangan Publik --}}
            <div class="flex-1 min-w-[200px]" x-data="{ copiedLink: false }">
                <div class="text-xs text-slate-500 font-semibold mb-1">&#x1F310; Link Undangan:</div>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ url('/' . $w->slug) }}"
                           class="flex-1 text-xs bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-slate-400 font-mono cursor-pointer" onclick="this.select()">
                    <button @click="navigator.clipboard.writeText('{{ url('/' . $w->slug) }}'); copiedLink = true; setTimeout(() => copiedLink = false, 2000)"
                            class="shrink-0 px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold transition">
                        <span x-show="!copiedLink">&#x1F4CB;</span>
                        <span x-show="copiedLink" style="display:none" class="text-green-300">&#x2713;</span>
                    </button>
                </div>
            </div>
            {{-- Link Tracking --}}
            <div class="flex-1 min-w-[200px]" x-data="{ copiedTrack: false }">
                <div class="text-xs text-violet-500 font-semibold mb-1">&#x1F4CA; Link Tracking (untuk pelanggan):</div>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ route('tracking.show', $w->tracking_token) }}"
                           class="flex-1 text-xs bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-slate-400 font-mono cursor-pointer" onclick="this.select()">
                    <button @click="navigator.clipboard.writeText('{{ route('tracking.show', $w->tracking_token) }}'); copiedTrack = true; setTimeout(() => copiedTrack = false, 2000)"
                            class="shrink-0 px-3 py-1.5 rounded-lg bg-violet-700 hover:bg-violet-600 text-white text-xs font-semibold transition">
                        <span x-show="!copiedTrack">&#x1F4CB;</span>
                        <span x-show="copiedTrack" style="display:none" class="text-green-300">&#x2713;</span>
                    </button>
                </div>
            </div>
            {{-- Link Portal Pelanggan (Premium & VIP) --}}
            @if($isPremiumOrVip && $portalToken)
            <div class="flex-1 min-w-[200px]" x-data="{ copiedPortal: false }">
                <div class="text-xs font-semibold mb-1 {{ $isVip ? 'text-yellow-500' : 'text-sky-500' }}">
                    {{ $isVip ? '&#x265B;' : '&#x2B50;' }} Link Portal Pelanggan {{ $isVip ? '(VIP)' : '(Premium)' }}:
                </div>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ route('my.vip.dashboard', $portalToken) }}"
                           class="flex-1 text-xs bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-slate-400 font-mono cursor-pointer" onclick="this.select()">
                    <button @click="navigator.clipboard.writeText('{{ route('my.vip.dashboard', $portalToken) }}'); copiedPortal = true; setTimeout(() => copiedPortal = false, 2000)"
                            class="shrink-0 px-3 py-1.5 rounded-lg {{ $isVip ? 'bg-yellow-700 hover:bg-yellow-600' : 'bg-sky-700 hover:bg-sky-600' }} text-white text-xs font-semibold transition">
                        <span x-show="!copiedPortal">&#x1F4CB;</span>
                        <span x-show="copiedPortal" style="display:none" class="text-green-300">&#x2713;</span>
                    </button>
                </div>
            </div>
            @elseif($isPremiumOrVip && !$portalToken)
            <div class="flex-1 min-w-[200px]">
                <div class="text-xs font-semibold mb-1 {{ $isVip ? 'text-yellow-500' : 'text-sky-500' }}">
                    {{ $isVip ? '&#x265B;' : '&#x2B50;' }} Link Portal {{ $isVip ? 'VIP' : 'Premium' }}:
                </div>
                <div class="text-xs text-slate-500 italic px-1 py-1.5">Belum ada pesanan terhubung.</div>
            </div>
            @endif
        </div>

        {{-- ── STEP HINT ── --}}
        @if($step === 1)
        <div class="border-t border-orange-700/20 bg-orange-900/10 px-4 sm:px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-orange-400 text-xs">&#x1F4A1; Import daftar tamu terlebih dahulu</span>
            <a href="{{ route('admin.guests.import', ['wedding_id' => $w->id]) }}" class="text-xs font-semibold text-orange-300 hover:text-orange-200 shrink-0">Import Sekarang &#x2192;</a>
        </div>
        @elseif($step === 2)
        <div class="border-t border-blue-700/20 bg-blue-900/10 px-4 sm:px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-blue-400 text-xs">&#x1F4A1; Bagikan link undangan ke tamu</span>
            <a href="{{ route('admin.guests.index', ['wedding_id' => $w->id]) }}" class="text-xs font-semibold text-blue-300 hover:text-blue-200 shrink-0">Lihat Link Tamu &#x2192;</a>
        </div>
        @else
        <div class="border-t border-green-700/20 bg-green-900/10 px-4 sm:px-5 py-2">
            <span class="text-green-400 text-xs font-medium">&#x2705; {{ $openedCount }} dari {{ $guestCount }} tamu sudah membuka undangan</span>
        </div>
        @endif
    </div>
    @endforeach
    </div>
@endif


{{-- ==================================================== --}}
{{-- ==  BAGIAN 2: UNDANGAN ULANG TAHUN                == --}}
{{-- ==================================================== --}}
<div class="flex items-center justify-between mb-3 mt-2">
    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
        &#x1F382; Undangan Ulang Tahun
        <span class="text-xs font-normal text-slate-500 bg-white/5 px-2 py-0.5 rounded-full border border-white/10">{{ $birthdayList->count() }}</span>
    </h2>
</div>

@if($birthdayList->isEmpty())
    <div class="adm-card p-10 text-center mb-8" style="border-left:3px solid rgba(249,168,212,.35)">
        <div class="text-5xl mb-3">&#x1F382;</div>
        <p class="text-slate-400 font-medium">Belum ada undangan ulang tahun.</p>
    </div>
@else
    <div class="space-y-4 mb-10">
    @foreach($birthdayList as $w)
    @php
        $guestCount  = $w->guests()->count();
        $openedCount = $w->guests()->whereNotNull('first_opened_at')->count();
        $rsvpHadir   = $w->guests()->where('is_attending', true)->count();
        $rsvpTidak   = $w->guests()->where('is_attending', false)->count();
        $rsvpBelum   = $guestCount - $w->guests()->whereNotNull('replied_at')->count();
        $step        = $guestCount === 0 ? 1 : ($openedCount === 0 ? 2 : 3);
        $pkgMeta = match($w->package ?? 'basic') {
            'trial'   => ['label'=>'Trial',      'cls'=>'bg-amber-900/40 text-amber-300 border-amber-700/40'],
            'basic'   => ['label'=>'Basic',      'cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
            'vip'     => ['label'=>'&#x265B; VIP',   'cls'=>'bg-purple-900/40 text-purple-300 border-purple-700/40'],
            'premium' => ['label'=>'&#x2B50; Premium','cls'=>'bg-yellow-900/40 text-yellow-300 border-yellow-700/40'],
            default   => ['label'=>'Basic',      'cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
        };
        $stepMeta = match($step) {
            1 => ['label'=>'&#x2460; Belum ada tamu',    'cls'=>'bg-orange-900/40 text-orange-300 border-orange-700/40'],
            2 => ['label'=>'&#x2461; Tamu belum membuka','cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
            3 => ['label'=>'&#x2462; Ada yang membuka',  'cls'=>'bg-green-900/40 text-green-300 border-green-700/40'],
        };
    @endphp
    <div class="adm-card overflow-hidden inv-card" style="border-left:3px solid rgba(249,168,212,.35)"
         data-name="{{ strtolower($w->bride_name) }}"
         data-slug="{{ $w->slug }}"
         data-pkg="{{ $w->package }}"
         x-data="{ extOpen: false }">
        <div class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        @if(!$w->is_active)<span class="badge bg-red-900/50 text-red-300 border-red-700/40">&#x23F8; Nonaktif</span>@endif
                        <span class="badge bg-pink-900/40 text-pink-300 border-pink-700/40">&#x1F382; Ulang Tahun</span>
                        <span class="font-bold text-slate-100">{{ $w->bride_name }}</span>
                        <span class="font-mono text-xs text-slate-500 bg-white/5 px-2 py-0.5 rounded border border-white/10">{{ $w->slug }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span class="badge {!! $pkgMeta['cls'] !!}">{!! $pkgMeta['label'] !!}{{ $w->isExpired() ? ' (Kadaluarsa)' : '' }}</span>
                        <span class="badge {!! $stepMeta['cls'] !!}">{!! $stepMeta['label'] !!}</span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mb-3">
                        <span>&#x1F370; {{ $allTemplates[$w->template]['label'] ?? ucfirst($w->template) }}</span>
                        @if($w->event_date)<span>&#x1F4C5; {{ $w->event_date->format('d M Y') }}</span>@endif
                        <span>&#x1F465; {{ $guestCount }} tamu{{ $w->guestLimit() !== null ? ' / maks '.$w->guestLimit() : '' }}</span>
                        @if($guestCount > 0)<span>&#x1F4EC; {{ $openedCount }} dibuka</span>@endif
                        @if($w->trial_expires_at)<span class="{{ $w->isExpired() ? 'text-red-400 font-semibold':'text-amber-400' }}">&#x23F1; s/d {{ $w->trial_expires_at->format('d M Y') }}</span>@endif
                    </div>
                    @if($guestCount > 0)
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <span class="font-semibold text-slate-400">RSVP:</span>
                        <span class="flex items-center gap-1 text-green-400"><span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>Hadir {{ $rsvpHadir }}</span>
                        <span class="flex items-center gap-1 text-red-400"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>Tidak {{ $rsvpTidak }}</span>
                        <span class="flex items-center gap-1 text-slate-500"><span class="w-2 h-2 rounded-full bg-slate-600 inline-block"></span>Belum {{ $rsvpBelum }}</span>
                    </div>
                    @endif
                </div>
                <div class="shrink-0 flex flex-col gap-2 sm:items-end">
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('invitation.show', $w->slug) }}" target="_blank" class="btn-adm border border-white/10 text-slate-400 hover:bg-white/5 hover:text-slate-200">&#x1F441; Lihat</a>
                        <a href="{{ route('admin.birthdays.edit', $w->id) }}" class="btn-adm bg-gradient-to-r from-pink-600 to-rose-600 text-white hover:from-pink-700 hover:to-rose-700">&#x270F; Edit</a>
                        <a href="{{ route('admin.guests.index', ['wedding_id' => $w->id]) }}" class="btn-adm border border-pink-500/30 text-pink-300 hover:bg-pink-900/20">&#x1F465; Tamu ({{ $guestCount }})</a>
                        <a href="{{ route('admin.guests.create', ['wedding_id' => $w->id]) }}" class="btn-adm border border-pink-400/30 text-pink-400 hover:bg-pink-900/20">+ Tamu</a>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('admin.guests.import', ['wedding_id' => $w->id]) }}" class="btn-adm bg-gradient-to-r from-orange-500 to-pink-500 text-white hover:from-orange-600 hover:to-pink-600">&#x2B06; Import</a>
                        @if($guestCount > 0)
                        <a href="{{ route('admin.guests.export') }}?wedding_id={{ $w->id }}" class="btn-adm border border-emerald-500/30 text-emerald-400 hover:bg-emerald-900/20">&#x2B07; Export</a>
                        @endif
                        <a href="{{ route('tracking.show', $w->tracking_token) }}" target="_blank" class="btn-adm border border-green-500/30 text-green-400 hover:bg-green-900/20">&#x1F4CA; Tracking</a>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <form action="{{ route('admin.weddings.toggle-active', $w->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            @if($w->is_active)
                                <button class="btn-adm border border-slate-500/40 text-slate-400 hover:bg-slate-800/40">&#x23F8; Nonaktifkan</button>
                            @else
                                <button class="btn-adm border border-green-500/40 text-green-400 hover:bg-green-900/20">&#x25B6; Aktifkan</button>
                            @endif
                        </form>
                        <div class="relative">
                            <button @click="extOpen = !extOpen" class="btn-adm border border-amber-500/30 text-amber-300 hover:bg-amber-900/20">&#x23F1; Perpanjang</button>
                            <div x-show="extOpen" @click.outside="extOpen = false"
                                 class="absolute right-0 top-9 z-30 w-52 bg-slate-900 border border-white/10 rounded-xl shadow-2xl p-3" style="display:none">
                                <p class="text-xs font-semibold text-amber-300 mb-2">Tambah masa aktif:</p>
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach([7, 14, 21, 30] as $d)
                                    <form action="{{ route('admin.weddings.extend', $w->id) }}" method="post">
                                        @csrf @method('PATCH') <input type="hidden" name="days" value="{{ $d }}">
                                        <button class="px-2 py-1 rounded-lg bg-amber-900/40 hover:bg-amber-900/60 text-amber-300 text-xs font-semibold border border-amber-700/40">+{{ $d }}h</button>
                                    </form>
                                    @endforeach
                                </div>
                                <form action="{{ route('admin.weddings.extend', $w->id) }}" method="post" class="flex gap-1">
                                    @csrf @method('PATCH')
                                    <input type="number" name="days" min="1" max="365" placeholder="Kustom hari" class="flex-1 border border-white/10 rounded-lg px-2 py-1 text-xs bg-white/5 text-slate-200" required>
                                    <button class="px-2 py-1 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold">OK</button>
                                </form>
                            </div>
                        </div>
                        <form action="{{ route('admin.birthdays.destroy', $w->id) }}" method="post" class="inline"
                              data-adm-confirm="Hapus undangan ulang tahun {{ addslashes($w->bride_name) }}? Semua data tamu ikut terhapus."
                              data-adm-ajax data-adm-danger data-adm-remove-closest=".adm-card">
                            @csrf @method('DELETE')
                            <button class="btn-adm border border-red-500/30 text-red-400 hover:bg-red-900/20">&#x1F5D1; Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- Link bar --}}
        <div class="border-t border-white/5 bg-slate-900/40 px-4 sm:px-5 py-3 flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]" x-data="{ copiedLink: false }">
                <div class="text-xs text-slate-500 font-semibold mb-1">&#x1F310; Link Undangan:</div>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ url('/' . $w->slug) }}"
                           class="flex-1 text-xs bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-slate-400 font-mono cursor-pointer" onclick="this.select()">
                    <button @click="navigator.clipboard.writeText('{{ url('/' . $w->slug) }}'); copiedLink = true; setTimeout(() => copiedLink = false, 2000)"
                            class="shrink-0 px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold transition">
                        <span x-show="!copiedLink">&#x1F4CB;</span><span x-show="copiedLink" style="display:none" class="text-green-300">&#x2713;</span>
                    </button>
                </div>
            </div>
            <div class="flex-1 min-w-[200px]" x-data="{ copiedTrack: false }">
                <div class="text-xs text-pink-500 font-semibold mb-1">&#x1F4CA; Link Tracking:</div>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ route('tracking.show', $w->tracking_token) }}"
                           class="flex-1 text-xs bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-slate-400 font-mono cursor-pointer" onclick="this.select()">
                    <button @click="navigator.clipboard.writeText('{{ route('tracking.show', $w->tracking_token) }}'); copiedTrack = true; setTimeout(() => copiedTrack = false, 2000)"
                            class="shrink-0 px-3 py-1.5 rounded-lg bg-pink-700 hover:bg-pink-600 text-white text-xs font-semibold transition">
                        <span x-show="!copiedTrack">&#x1F4CB;</span><span x-show="copiedTrack" style="display:none" class="text-green-300">&#x2713;</span>
                    </button>
                </div>
            </div>
        </div>
        @if($step === 1)
        <div class="border-t border-orange-700/20 bg-orange-900/10 px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-orange-400 text-xs">&#x1F4A1; Import daftar tamu terlebih dahulu</span>
            <a href="{{ route('admin.guests.import', ['wedding_id' => $w->id]) }}" class="text-xs font-semibold text-orange-300 hover:text-orange-200 shrink-0">Import Sekarang &#x2192;</a>
        </div>
        @elseif($step === 2)
        <div class="border-t border-blue-700/20 bg-blue-900/10 px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-blue-400 text-xs">&#x1F4A1; Bagikan link undangan ke tamu</span>
            <a href="{{ route('admin.guests.index', ['wedding_id' => $w->id]) }}" class="text-xs font-semibold text-blue-300 hover:text-blue-200 shrink-0">Lihat Link Tamu &#x2192;</a>
        </div>
        @else
        <div class="border-t border-green-700/20 bg-green-900/10 px-5 py-2">
            <span class="text-green-400 text-xs font-medium">&#x2705; {{ $openedCount }} dari {{ $guestCount }} tamu sudah membuka undangan</span>
        </div>
        @endif
    </div>
    @endforeach
    </div>
@endif


{{-- ==================================================== --}}
{{-- ==  BAGIAN 3: KARTU UCAPAN ULANG TAHUN            == --}}
{{-- ==================================================== --}}
    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
        &#x1F48C; Kartu Ucapan Ulang Tahun
        <span class="text-xs font-normal text-slate-500 bg-white/5 px-2 py-0.5 rounded-full border border-white/10">{{ $greetingList->count() }}</span>
    </h2>
</div>

@if($greetingList->isEmpty())
    <div class="adm-card p-10 text-center mb-8" style="border-left:3px solid rgba(167,139,250,.35)">
        <div class="text-5xl mb-3">&#x1F48C;</div>
        <p class="text-slate-400 font-medium">Belum ada kartu ucapan ulang tahun.</p>
    </div>
@else
    <div class="space-y-4 mb-10">
    @foreach($greetingList as $w)
    @php
        $pkgMeta = match($w->package ?? 'basic') {
            'trial'   => ['label'=>'Trial',      'cls'=>'bg-amber-900/40 text-amber-300 border-amber-700/40'],
            'basic'   => ['label'=>'Basic',      'cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
            'vip'     => ['label'=>'&#x265B; VIP',   'cls'=>'bg-purple-900/40 text-purple-300 border-purple-700/40'],
            'premium' => ['label'=>'&#x2B50; Premium','cls'=>'bg-yellow-900/40 text-yellow-300 border-yellow-700/40'],
            default   => ['label'=>'Basic',      'cls'=>'bg-blue-900/40 text-blue-300 border-blue-700/40'],
        };
    @endphp
    <div class="adm-card overflow-hidden inv-card" style="border-left:3px solid rgba(167,139,250,.35)"
         data-name="{{ strtolower($w->bride_name) }}"
         data-slug="{{ $w->slug }}"
         data-pkg="{{ $w->package }}">
        <div class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        @if(!$w->is_active)<span class="badge bg-red-900/50 text-red-300 border-red-700/40">&#x23F8; Nonaktif</span>@endif
                        <span class="badge bg-violet-900/40 text-violet-300 border-violet-700/40">&#x1F48C; Kartu Ucapan</span>
                        <span class="font-bold text-slate-100">{{ $w->bride_name }}</span>
                        @if($w->groom_name)<span class="text-slate-500 text-xs">dari {{ $w->groom_name }}</span>@endif
                        <span class="font-mono text-xs text-slate-500 bg-white/5 px-2 py-0.5 rounded border border-white/10">{{ $w->slug }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span class="badge {!! $pkgMeta['cls'] !!}">{!! $pkgMeta['label'] !!}{{ $w->isExpired() ? ' (Kadaluarsa)' : '' }}</span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                        <span>&#x1F48C; {{ $allTemplates[$w->template]['label'] ?? ucfirst($w->template) }}</span>
                        @if($w->bride_age)<span>&#x1F382; {{ $w->bride_age }} tahun</span>@endif
                        @if($w->trial_expires_at)<span class="{{ $w->isExpired() ? 'text-red-400 font-semibold':'text-amber-400' }}">&#x23F1; s/d {{ $w->trial_expires_at->format('d M Y') }}</span>@endif
                    </div>
                </div>
                <div class="shrink-0 flex flex-wrap gap-1.5 sm:justify-end sm:items-start">
                    <a href="{{ route('invitation.show', $w->slug) }}" target="_blank" class="btn-adm border border-white/10 text-slate-400 hover:bg-white/5 hover:text-slate-200">&#x1F441; Lihat</a>
                    <a href="{{ route('admin.greetings.edit', $w->id) }}" class="btn-adm bg-gradient-to-r from-violet-600 to-purple-600 text-white hover:from-violet-700 hover:to-purple-700">&#x270F; Edit</a>
                    <form action="{{ route('admin.weddings.toggle-active', $w->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        @if($w->is_active)
                            <button class="btn-adm border border-slate-500/40 text-slate-400 hover:bg-slate-800/40">&#x23F8; Nonaktifkan</button>
                        @else
                            <button class="btn-adm border border-green-500/40 text-green-400 hover:bg-green-900/20">&#x25B6; Aktifkan</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <div class="border-t border-white/5 bg-slate-900/40 px-4 sm:px-5 py-3 flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]" x-data="{ copiedLink: false }">
                <div class="text-xs text-slate-500 font-semibold mb-1">&#x1F310; Link Kartu:</div>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ url('/' . $w->slug) }}"
                           class="flex-1 text-xs bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-slate-400 font-mono cursor-pointer" onclick="this.select()">
                    <button @click="navigator.clipboard.writeText('{{ url('/' . $w->slug) }}'); copiedLink = true; setTimeout(() => copiedLink = false, 2000)"
                            class="shrink-0 px-3 py-1.5 rounded-lg bg-violet-700 hover:bg-violet-600 text-white text-xs font-semibold transition">
                        <span x-show="!copiedLink">&#x1F4CB;</span><span x-show="copiedLink" style="display:none" class="text-green-300">&#x2713;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </div>
@endif

<p class="mt-2 mb-2">
    <a href="{{ route('welcome') }}" class="text-amber-400 hover:text-amber-300 font-medium text-sm">&#x2190; Kembali ke beranda</a>
</p>

@endsection

@push('styles')
<style @nonce>
.btn-adm {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 4px 10px;
    border-radius: 7px;
    font-size: 0.7rem;
    font-weight: 600;
    transition: all 0.15s ease;
    white-space: nowrap;
    line-height: 1.5;
    cursor: pointer;
}
.badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.68rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 9999px;
    border-width: 1px;
    border-style: solid;
    line-height: 1.6;
}
</style>
@endpush
