@extends('admin.layout')

@section('title', 'Kelola Pesanan')

@section('content')

    {{-- Flash banners handled globally via toast in layout --}}

    {{-- ── WA Notification ─────────────────────────────────────────── --}}
    @if(session('wa_notification'))
    @php
        $waNotif = session('wa_notification');
        $waType  = $waNotif['type'] ?? 'lunas';
        [$waBorder, $waGrad, $waSubtext, $waTextBtn, $waTip] = match ($waType) {
            'selesai'         => ['border-violet-400', 'from-violet-600 to-purple-600', 'text-violet-100', 'text-violet-700', '🎉 Undangan selesai! Kirim link undangan ke pelanggan.'],
            'diproses'        => ['border-amber-400',  'from-amber-500 to-orange-500',  'text-amber-100',  'text-amber-700',  '⚙️ Beritahu pelanggan bahwa undangan sedang dikerjakan.'],
            'ditolak'         => ['border-red-400',    'from-red-600 to-rose-600',      'text-red-100',    'text-red-700',    '❌ Beritahu pelanggan bahwa pembayarannya ditolak beserta alasannya.'],
            'pengingat_bayar' => ['border-blue-400',   'from-blue-600 to-indigo-600',   'text-blue-100',   'text-blue-700',   '🔄 Pesanan dibuka ulang! Kirim link pembayaran ke customer untuk upload ulang bukti bayar.'],
            default           => ['border-green-400',  'from-green-500 to-emerald-500', 'text-green-100',  'text-green-700',  '💡 Klik tombol di atas untuk membuka WhatsApp kepada pelanggan.'],
        };
        $waIcon = ['selesai' => '🎉', 'diproses' => '⚙️', 'lunas' => '✅', 'ditolak' => '❌', 'pengingat_bayar' => '🔄'][$waType] ?? '✅';
    @endphp
    <div class="mb-6 rounded-2xl overflow-hidden border-2 {{ $waBorder }} shadow-lg" x-data="{ show: true }" x-show="show">
        <div class="bg-gradient-to-r {{ $waGrad }} px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-2xl shrink-0">{{ $waIcon }}</div>
                <div>
                    <div class="text-white font-bold text-base">{{ $waNotif['label'] ?? 'Notifikasi' }} — Order #{{ $waNotif['order_id'] }}</div>
                    <div class="{{ $waSubtext }} text-sm">Kirim notifikasi ke <strong>{{ $waNotif['name'] }}</strong> via WhatsApp</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ $waNotif['url'] }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white {{ $waTextBtn }} text-sm font-bold rounded-xl hover:bg-white/90 shadow-md transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                    Kirim via WhatsApp
                </a>
                <button @click="show = false" class="text-white/70 hover:text-white text-xl leading-none transition">✕</button>
            </div>
        </div>
        <div class="bg-black/20 px-6 py-2 text-xs text-white/80">{{ $waTip }}</div>
    </div>
    @endif

    {{-- ── Header + Stats ──────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">Kelola Pesanan</h1>
            <p class="text-slate-500 text-sm mt-0.5">Semua pesanan masuk dari pelanggan</p>
        </div>
    </div>

    {{-- ── Stats Row ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="adm-card px-4 py-3 flex items-center gap-3">
            <div class="text-2xl">📋</div>
            <div>
                <div class="text-xl font-black text-slate-100">{{ $statCounts['total'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Total Order</div>
            </div>
        </div>
        <div class="adm-card px-4 py-3 flex items-center gap-3">
            <div class="text-2xl">🆕</div>
            <div>
                <div class="text-xl font-black text-amber-400">{{ $statCounts['baru'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Order Baru</div>
            </div>
        </div>
        <div class="adm-card px-4 py-3 flex items-center gap-3 {{ $statCounts['menunggu'] > 0 ? 'border border-blue-500/30' : '' }}">
            <div class="text-2xl">💳</div>
            <div>
                <div class="text-xl font-black text-blue-400">{{ $statCounts['menunggu'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Perlu Konfirmasi</div>
            </div>
        </div>
        <div class="adm-card px-4 py-3 flex items-center gap-3">
            <div class="text-2xl">📅</div>
            <div>
                <div class="text-xl font-black text-green-400">{{ $statCounts['today'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Masuk Hari Ini</div>
            </div>
        </div>
    </div>

    {{-- ── QRIS Settings Card ────────────────────────────────────────── --}}
    @php
        $qrisStoragePath = storage_path('app/public/qris.png');
        $qrisExists      = file_exists($qrisStoragePath);
        $qrisPreviewUrl  = $qrisExists ? asset('storage/qris.png') . '?v=' . filemtime($qrisStoragePath) : null;
    @endphp
    <div class="adm-card mb-5 border border-amber-500/20" id="qris" x-data="{ qrisOpen: false }">
        <button @click="qrisOpen = !qrisOpen"
                class="w-full px-4 py-3 flex items-center justify-between gap-3 text-left hover:bg-white/[0.04] transition-colors rounded-2xl">
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-200">
                <span class="text-base">💳</span>
                <span>Pengaturan QRIS</span>
                @if($qrisPreviewUrl)
                    <span class="px-2 py-0.5 bg-green-500/20 text-green-400 text-xs font-bold rounded-full border border-green-700/30">✅ Aktif</span>
                @else
                    <span class="px-2 py-0.5 bg-red-500/20 text-red-400 text-xs font-bold rounded-full border border-red-700/30">❌ Belum diupload</span>
                @endif
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <span x-text="qrisOpen ? 'Sembunyikan' : 'Klik untuk buka'"></span>
                <svg class="w-4 h-4 text-amber-400 transition-transform duration-200 shrink-0" :class="qrisOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </button>
        <div x-show="qrisOpen" x-transition class="px-4 pb-4 border-t border-white/[0.06] pt-4" style="display:none">
            <div class="flex flex-wrap gap-5 items-start">
                <div>
                    @if($qrisPreviewUrl)
                        <img src="{{ $qrisPreviewUrl }}" alt="QRIS" class="h-28 w-auto rounded-lg border border-amber-500/30 object-contain">
                        <p class="text-xs text-green-400 mt-1.5 font-medium">✅ QRIS aktif</p>
                    @else
                        <div class="h-28 w-28 rounded-lg border-2 border-dashed border-white/20 flex items-center justify-center text-slate-400 text-xs text-center p-3 bg-white/5">
                            <div><div class="text-2xl mb-1">📱</div>Belum ada QRIS</div>
                        </div>
                    @endif
                </div>
                <form action="{{ route('admin.settings.qris') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col gap-2.5">
                    @csrf
                    <label class="text-xs text-amber-300 font-semibold">Upload QRIS Baru</label>
                    <input type="file" name="qris" accept=".jpg,.jpeg,.png" required
                           class="text-sm border border-white/10 bg-white/5 rounded-lg px-3 py-2 text-slate-300 file:mr-2 file:text-xs file:border-0 file:bg-amber-500 file:text-white file:rounded file:px-3 file:py-1.5 file:font-medium hover:file:bg-amber-600 file:transition-all">
                    @error('qris')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    <button type="submit" class="self-start px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all">
                        ⬆️ Upload QRIS
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Category tabs ────────────────────────────────────────────── --}}
    @php
        $currentCat = $category ?? '';
        $catBase = array_filter(['status' => $status ?: null, 'payment' => $paymentFilter ?: null, 'date' => ($date ?? '') ?: null, 'search' => $search ?: null]);
        $catTabs = [
            ''           => ['label' => 'Semua',       'icon' => '📋', 'color' => 'amber'],
            'wedding'    => ['label' => 'Pernikahan',   'icon' => '💍', 'color' => 'rose'],
            'birthday'   => ['label' => 'Ulang Tahun',  'icon' => '🎂', 'color' => 'orange'],
            'greeting'     => ['label' => 'Greeting',     'icon' => '💌', 'color' => 'teal'],
            'anniversary'  => ['label' => 'Anniversary',   'icon' => '💕', 'color' => 'rose'],
        ];
    @endphp
    <div class="flex gap-2 mb-3 flex-wrap">
        @foreach($catTabs as $catKey => $catInfo)
        @php
            $catActive = $currentCat === $catKey;
            $catCount  = $catKey === '' ? $statCounts['total'] : ($categoryCounts[$catKey] ?? 0);
            $catParams = array_filter(array_merge($catBase, ['category' => $catKey ?: null]));
            $activeCls = match($catInfo['color']) {
                'rose'   => 'bg-gradient-to-r from-rose-600 to-pink-600 text-white border-rose-500',
                'orange' => 'bg-gradient-to-r from-orange-500 to-amber-500 text-white border-orange-400',
                'violet' => 'bg-gradient-to-r from-violet-600 to-purple-600 text-white border-violet-500',
                'teal'   => 'bg-gradient-to-r from-teal-600 to-cyan-600 text-white border-teal-500',
                'rose'   => 'bg-gradient-to-r from-rose-600 to-pink-600 text-white border-rose-500',
                default  => 'bg-gradient-to-r from-amber-500 to-orange-500 text-white border-amber-400',
            };
            $inactiveCls = 'bg-white/5 text-slate-300 border-white/10 hover:border-amber-500/40 hover:text-amber-300';
        @endphp
        <a href="{{ route('admin.orders.index', $catParams) }}"
           class="px-3.5 py-1.5 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5
                  {{ $catActive ? $activeCls : $inactiveCls }}">
            <span>{{ $catInfo['icon'] }}</span>
            <span>{{ $catInfo['label'] }}</span>
            <span class="px-1.5 py-0.5 rounded-full text-xs {{ $catActive ? 'bg-white/20' : 'bg-white/10 text-slate-400' }}">{{ $catCount }}</span>
        </a>
        @endforeach
    </div>

    {{-- ── Search + Filter Row ───────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-2 mb-5 items-center">
        {{-- Search field --}}
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-1.5 flex-1 min-w-[200px]">
            @foreach(array_filter(['status' => $status ?: null, 'payment' => $paymentFilter ?: null, 'date' => ($date ?? '') ?: null, 'category' => $category ?: null]) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <input type="search" name="search" value="{{ $search }}"
                   placeholder="Cari nama / no HP / nama acara…"
                   class="flex-1 text-xs border border-white/10 bg-white/5 rounded-lg px-3 py-1.5 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/40 min-w-0">
            <button type="submit"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white/10 border border-white/10 text-slate-300 hover:bg-white/15 hover:text-slate-100 transition-all">
                🔍
            </button>
            @if($search)
            <a href="{{ route('admin.orders.index', array_filter(['status' => $status ?: null, 'payment' => $paymentFilter ?: null, 'date' => ($date ?? '') ?: null, 'category' => $category ?: null])) }}"
               class="px-2.5 py-1.5 rounded-lg text-xs border border-white/10 bg-white/5 text-slate-400 hover:text-slate-200 hover:bg-white/10 transition-all">✕</a>
            @endif
        </form>

        {{-- Status filter tabs --}}
        @php
            $statuses  = ['' => 'Semua', 'baru' => '🆕 Baru', 'diproses' => '⚙️ Diproses', 'selesai' => '✅ Selesai'];
            $current   = request('status', '');
            $ditolakOn = ($paymentFilter ?? '') === 'ditolak';
            $todayOn   = ($date ?? '') === 'today';
            $dateParam = $todayOn ? ['date' => 'today'] : [];
            $todayToggleParams = array_filter(['status' => $current ?: null, 'date' => $todayOn ? null : 'today', 'category' => $category ?: null, 'search' => $search ?: null]);
        @endphp
        @foreach($statuses as $key => $label)
            @php $tabParams = array_filter(array_merge(['status' => $key ?: null, 'category' => $category ?: null, 'search' => $search ?: null], $dateParam)); @endphp
            <a href="{{ route('admin.orders.index', $tabParams) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold border transition-all
                      {{ (!$ditolakOn && $current === $key)
                         ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white border-amber-400'
                         : 'bg-white/5 text-slate-300 border-white/10 hover:border-amber-500/40 hover:text-amber-300' }}">
                {{ $label }}
                @if($key === '' && !$ditolakOn)
                    <span class="ml-1 px-1.5 py-0.5 bg-white/10 rounded-full text-xs">{{ $orders->total() }}</span>
                @endif
            </a>
        @endforeach
        <div class="h-5 w-px bg-white/10 mx-0.5"></div>
        @php $ditolakTabParams = array_filter(['payment' => 'ditolak', 'date' => $dateParam['date'] ?? null, 'category' => $category ?: null, 'search' => $search ?: null]); @endphp
        <a href="{{ route('admin.orders.index', $ditolakTabParams) }}"
           class="px-3.5 py-1.5 rounded-lg text-xs font-bold border transition-all
                  {{ $ditolakOn
                     ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white border-red-500'
                     : 'bg-white/5 text-slate-300 border-white/10 hover:border-red-500/40 hover:text-red-300' }}">
            ❌ Ditolak
            @if($ditolakOn)
                <span class="ml-1 px-1.5 py-0.5 bg-white/20 rounded-full text-xs">{{ $orders->total() }}</span>
            @elseif(($ditolakCount ?? 0) > 0)
                <span class="ml-1 px-1.5 py-0.5 bg-red-900/40 text-red-400 rounded-full text-xs">{{ $ditolakCount }}</span>
            @endif
        </a>
        <div class="h-5 w-px bg-white/10 mx-0.5"></div>
        <a href="{{ route('admin.orders.index', $todayToggleParams) }}"
           class="px-3.5 py-1.5 rounded-lg text-xs font-bold border transition-all
                  {{ $todayOn
                     ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white border-green-400'
                     : 'bg-white/5 text-slate-300 border-white/10 hover:border-green-500/40 hover:text-green-300' }}">
            📅 Hari Ini
            @if($todayOn)
                <span class="ml-1 px-1.5 py-0.5 bg-white/20 rounded-full text-xs">{{ $orders->total() }}</span>
            @endif
        </a>
    </div>

    {{-- ── Orders list ───────────────────────────────────────────────── --}}
    @if($orders->isEmpty())
    <div class="adm-card p-12 text-center">
        <div class="text-5xl mb-3">📋</div>
        <p class="text-slate-400 font-medium">
            @if($search)
                Tidak ada pesanan yang cocok dengan "{{ $search }}".
            @elseif($category)
                Belum ada pesanan untuk kategori ini.
            @else
                Belum ada pesanan.
            @endif
        </p>
        <p class="text-slate-500 text-sm mt-1">Pesanan yang masuk akan tampil di sini.</p>
    </div>
    @else

    @php
        $lastDate = null;
        $todayStr = now()->toDateString();
        $yestStr  = now()->subDay()->toDateString();
        $grouped  = $orders->groupBy(fn($o) => match($o->created_at->toDateString()) {
            $todayStr => 'today', $yestStr => 'yesterday', default => 'older'
        });
    @endphp

    <div class="space-y-3">
    @foreach($orders as $order)
    @php
        $dateKey   = $order->created_at->toDateString();
        $isNewDate = $dateKey !== $lastDate;
        $lastDate  = $dateKey;
        if ($dateKey === $todayStr) {
            $dateLabel = 'Hari Ini'; $dateDetail = now()->translatedFormat('l, d M Y');
            $dateIcon = '📅'; $textCls = 'text-green-400';
            $badgeCls = 'bg-green-500/20 text-green-400 border border-green-700/30';
            $lineCls  = 'from-green-500/30 to-transparent';
        } elseif ($dateKey === $yestStr) {
            $dateLabel = 'Kemarin'; $dateDetail = now()->subDay()->translatedFormat('l, d M Y');
            $dateIcon = '📆'; $textCls = 'text-blue-400';
            $badgeCls = 'bg-blue-500/20 text-blue-400 border border-blue-700/30';
            $lineCls  = 'from-blue-500/30 to-transparent';
        } else {
            $dateLabel  = \Carbon\Carbon::parse($dateKey)->translatedFormat('d M Y');
            $dateDetail = \Carbon\Carbon::parse($dateKey)->translatedFormat('l');
            $dateIcon = '🗓️'; $textCls = 'text-slate-400';
            $badgeCls = 'bg-white/10 text-slate-400';
            $lineCls  = 'from-slate-500/30 to-transparent';
        }
        $group      = $dateKey === $todayStr ? 'today' : ($dateKey === $yestStr ? 'yesterday' : 'older');
        $groupCount = ($grouped[$group] ?? collect())->count();

        $isSpam = $order->payment_status === 'belum_bayar' && $order->created_at->diffInHours(now()) >= 2;

        // Category label
        $tpl = $order->template ?? '';
        if (str_starts_with($tpl, 'birthday'))   { $catLabel = '🎂 Ulang Tahun'; $catCls = 'bg-orange-900/40 text-orange-300 border-orange-700/30'; }
        elseif (str_starts_with($tpl, 'greeting'))     { $catLabel = '💌 Greeting';    $catCls = 'bg-teal-900/40 text-teal-300 border-teal-700/30'; }
        elseif (str_starts_with($tpl, 'anniversary'))   { $catLabel = '� Anniversary'; $catCls = 'bg-pink-900/40 text-pink-300 border-pink-700/30'; }
        else                                            { $catLabel = '💍 Pernikahan';  $catCls = 'bg-rose-900/40 text-rose-300 border-rose-700/30'; }

        $borderColor = match($order->payment_status) {
            'lunas'               => 'rgba(52,211,153,.5)',
            'menunggu_konfirmasi' => 'rgba(96,165,250,.5)',
            'ditolak'             => 'rgba(239,68,68,.6)',
            'belum_bayar'         => $isSpam ? 'rgba(239,68,68,.4)' : 'rgba(245,158,11,.35)',
            default               => 'rgba(255,255,255,.08)',
        };

        $domain  = rtrim(config('app.url'), '/');
        $waLinks = [];
        if ($order->payment_status === 'belum_bayar') {
            $waLinks[] = ['icon' => '💳', 'label' => 'Pengingat Bayar',
                'url' => $order->customerWhatsappLink('pengingat_bayar', ['payment_url' => $domain.'/pesan/bayar/'.$order->public_token])];
        }
        if ($order->payment_status === 'menunggu_konfirmasi') {
            $waLinks[] = ['icon' => '✅', 'label' => 'Pembayaran Dikonfirmasi', 'url' => $order->customerWhatsappLink('lunas')];
        }
        if ($order->payment_status === 'lunas' && $order->status === 'diproses') {
            $waLinks[] = ['icon' => '⚙️', 'label' => 'Sedang Diproses', 'url' => $order->customerWhatsappLink('diproses')];
        }
        if ($order->status === 'selesai') {
            $waLinks[] = ['icon' => '🎉', 'label' => 'Undangan Selesai', 'url' => $order->customerWhatsappLink('selesai', [
                'wedding_url'  => $order->wedding ? $domain.'/'.$order->wedding->slug : null,
                'tracking_url' => $order->wedding ? $domain.'/tracking/'.$order->wedding->tracking_token : null,
            ])];
        }
        if ($order->payment_status === 'ditolak') {
            $waLinks[] = ['icon' => '❌', 'label' => 'Info Penolakan + Minta Upload Ulang',
                'url' => $order->customerWhatsappLink('ditolak', ['reason' => $order->rejection_reason ?? ''])];
        }
        $plainPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($plainPhone, '0')) $plainPhone = '62'.substr($plainPhone, 1);
        $waPlainUrl = 'https://wa.me/'.$plainPhone;
        $orderId    = strtoupper(substr($order->public_token, 0, 8));
    @endphp

    {{-- Date separator --}}
    @if($isNewDate)
    <div class="flex items-center gap-2.5 pt-2 pb-1">
        <span>{{ $dateIcon }}</span>
        <span class="font-bold text-xs {{ $textCls }}">{{ $dateLabel }}</span>
        <span class="text-xs text-slate-500">{{ $dateDetail }}</span>
        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $badgeCls }}">{{ $groupCount }} order</span>
        <div class="flex-1 h-px bg-gradient-to-r {{ $lineCls }}"></div>
    </div>
    @endif

    {{-- ── Order Card ──────────────────────────────── --}}
    <div class="adm-card overflow-hidden" style="border-left: 3px solid {{ $borderColor }}">

        <div class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row gap-4">

                {{-- LEFT: Info --}}
                <div class="flex-1 min-w-0">

                    {{-- Name row --}}
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        @if($isSpam)
                            <span class="badge bg-red-900/50 text-red-300 border-red-700/40">⚠️ Tidak Bayar</span>
                        @endif
                        <span class="badge {{ $catCls }}">{{ $catLabel }}</span>
                        <span class="font-mono text-xs font-bold text-amber-300 bg-black/30 px-2.5 py-1 rounded-md border border-amber-500/20">#{{ $orderId }}</span>
                        <span class="font-bold text-slate-100 text-[15px]">
                            {{ $order->bride_name }}{{ $order->groom_name ? ' & '.$order->groom_name : '' }}
                        </span>
                        <span class="text-xs text-slate-500 capitalize">{{ $order->template }}</span>
                    </div>

                    {{-- Status badges --}}
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span class="badge {{ $order->packageColor() }}">{{ $order->packageLabel() }}</span>
                        <span class="badge {{ $order->paymentStatusColor() }}">{{ $order->paymentStatusLabel() }}</span>
                        <span class="badge {{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
                    </div>

                    {{-- Meta --}}
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mb-3">
                        <span>👤 {{ $order->customer_name }}</span>
                        <span>📱 {{ $order->customer_phone }}</span>
                        @if($order->event_date)<span>📅 {{ $order->event_date->format('d M Y') }}</span>@endif
                        @if($order->location)<span>📍 {{ Str::limit($order->location, 40) }}</span>@endif
                        <span class="text-slate-600">🕐 {{ $order->created_at->diffForHumans() }}</span>
                    </div>

                    @if($order->notes)
                    <div class="text-xs text-slate-400 bg-white/5 rounded-lg p-2.5 italic border border-white/[0.06] mb-3">
                        💬 {{ $order->notes }}
                    </div>
                    @endif

                    @if($order->payment_status === 'ditolak' && $order->rejection_reason)
                    <div class="text-xs text-red-300 bg-red-900/20 rounded-lg p-2.5 border border-red-700/30 mb-3">
                        ❌ <span class="font-semibold">Alasan penolakan:</span> {{ $order->rejection_reason }}
                    </div>
                    @endif

                    {{-- Koneksi undangan --}}
                    @if($order->wedding_id && $order->wedding)
                    <div class="text-xs bg-green-900/20 border border-green-700/30 text-green-300 rounded-lg px-3 py-1.5 font-medium inline-block">
                        ✅ Terhubung: <strong>{{ $order->wedding->bride_name }}{{ $order->wedding->groom_name ? ' & '.$order->wedding->groom_name : '' }}</strong>
                    </div>
                    @else
                    <div class="text-xs bg-amber-900/20 border border-amber-700/30 text-amber-300 rounded-lg px-3 py-1.5 font-medium inline-block">
                        ⚠️ Belum terhubung ke undangan
                    </div>
                    @endif
                </div>

                {{-- RIGHT: Actions --}}
                <div class="shrink-0 flex flex-col gap-2 sm:items-end">

                    {{-- Baris 1: Lihat / Edit / Buat --}}
                    <div class="flex flex-wrap gap-1.5">
                        @if($order->wedding_id && $order->wedding)
                            <a href="{{ route('invitation.show', $order->wedding->slug) }}" target="_blank"
                               class="btn-adm border border-white/10 text-slate-400 hover:bg-white/5 hover:text-slate-200">👁 Lihat</a>
                            <a href="{{ route('admin.weddings.edit', $order->wedding_id) }}"
                               class="btn-adm bg-gradient-to-r from-violet-600 to-indigo-600 text-white hover:from-violet-700 hover:to-indigo-700">✏️ Edit Undangan</a>
                        @else
                            <a href="{{ route('admin.weddings.create.form', $order->template) }}?order_id={{ $order->id }}"
                               class="btn-adm text-white" style="background:linear-gradient(135deg,#7c3aed,#9333ea)">🛠️ Buat Undangan</a>
                        @endif
                    </div>

                    {{-- Baris 2: Konfirmasi + Update Status --}}
                    <div class="flex flex-wrap gap-1.5 items-center">
                        @if($order->payment_status === 'menunggu_konfirmasi')
                        <form action="{{ route('admin.orders.confirm-payment', $order) }}" method="POST"
                              data-adm-confirm="Konfirmasi pembayaran {{ addslashes($order->customer_name) }} sebagai LUNAS?"
                              data-adm-ajax data-adm-reload>
                            @csrf @method('PATCH')
                            <button class="btn-adm bg-gradient-to-r from-green-500 to-emerald-500 text-white hover:from-green-600 hover:to-emerald-600">
                                ✅ Konfirmasi Lunas
                            </button>
                        </form>
                        {{-- Tolak button with modal --}}
                        <div x-data="{ rejectOpen: false }">
                            <button @click="rejectOpen = true"
                                    class="btn-adm border border-red-500/40 text-red-400 hover:bg-red-900/20">
                                ❌ Tolak
                            </button>
                            {{-- Modal overlay --}}
                            <div x-show="rejectOpen" x-transition.opacity
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
                                 style="display:none" @keydown.escape.window="rejectOpen = false">
                                <div @click.stop
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="bg-gray-900 border border-red-500/30 rounded-2xl shadow-2xl w-full max-w-md p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 bg-red-900/50 rounded-full flex items-center justify-center text-xl">❌</div>
                                        <div>
                                            <div class="font-bold text-slate-100">Tolak Pembayaran</div>
                                            <div class="text-xs text-slate-400">#{{ $orderId }} — {{ $order->customer_name }}</div>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.orders.reject-payment', $order) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <label class="block text-xs font-semibold text-red-300 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="reason" required maxlength="500" rows="3"
                                                  placeholder="Contoh: Bukti pembayaran tidak jelas / nominal tidak sesuai..."
                                                  class="w-full text-sm border border-white/10 bg-white/5 rounded-lg px-3 py-2 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-red-500/50 resize-none"></textarea>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">Alasan ini akan dikirimkan ke pelanggan via WhatsApp.</p>
                                        <div class="flex gap-2 justify-end">
                                            <button type="button" @click="rejectOpen = false"
                                                    class="btn-adm border border-white/10 text-slate-400 hover:bg-white/5">Batal</button>
                                            <button type="submit"
                                                    class="btn-adm bg-gradient-to-r from-red-600 to-rose-600 text-white hover:from-red-700 hover:to-rose-700">
                                                ❌ Konfirmasi Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @elseif($order->payment_status === 'lunas')
                        <span class="btn-adm bg-green-900/20 text-green-300 border border-green-700/30 cursor-default">✅ Sudah Lunas</span>
                        @elseif($order->payment_status === 'ditolak')
                        <form action="{{ route('admin.orders.reset-payment', $order) }}" method="POST"
                              data-adm-confirm="Buka ulang pesanan #{{ $orderId }} agar pelanggan bisa upload ulang bukti bayar?">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="btn-adm bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700">
                                🔄 Buka Ulang
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex gap-1 items-center">
                            @csrf @method('PATCH')
                            <select name="status" class="text-xs border border-white/10 rounded-lg px-2.5 py-1.5 bg-white/5 text-slate-200 font-medium focus:border-amber-500/50 focus:outline-none">
                                <option value="baru"     {{ $order->status === 'baru'     ? 'selected' : '' }}>🆕 Baru</option>
                                <option value="diproses" {{ $order->status === 'diproses' ? 'selected' : '' }}>⚙️ Diproses</option>
                                <option value="selesai"  {{ $order->status === 'selesai'  ? 'selected' : '' }}>✅ Selesai</option>
                            </select>
                            <button type="submit" class="btn-adm border border-white/10 text-slate-300 hover:bg-white/10">Update</button>
                        </form>
                    </div>

                    {{-- Baris 3: Tautkan undangan --}}
                    <form action="{{ route('admin.orders.link', $order) }}" method="POST" class="flex gap-1 items-center flex-wrap">
                        @csrf @method('PATCH')
                        <select name="wedding_id" class="text-xs border border-white/10 rounded-lg px-2.5 py-1.5 bg-white/5 text-slate-200 font-medium focus:border-blue-500/50 focus:outline-none max-w-[200px]">
                            <option value="">🔗 Tautkan undangan...</option>
                            @foreach($weddings as $w)
                            <option value="{{ $w->id }}" {{ $order->wedding_id == $w->id ? 'selected' : '' }}>
                                {{ $w->bride_name }}{{ $w->groom_name ? ' & '.$w->groom_name : '' }} ({{ $w->slug }})
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-adm bg-blue-600/80 hover:bg-blue-500 text-white">Tautkan</button>
                    </form>

                    {{-- Baris 4: WA + Hapus --}}
                    <div class="flex flex-wrap gap-1.5 items-center">
                        <div class="relative" x-data="{ waOpen: false }">
                            <button @click="waOpen = !waOpen"
                                    class="btn-adm bg-green-700/80 hover:bg-green-600 text-white flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                Kirim WA
                                <svg class="w-3 h-3 transition-transform" :class="waOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="waOpen" @click.outside="waOpen = false" x-transition
                                 class="absolute right-0 top-full mt-1.5 z-40 w-64 bg-gray-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden" style="display:none">
                                @foreach($waLinks as $wl)
                                <a href="{{ $wl['url'] }}" target="_blank"
                                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-green-900/30 hover:text-green-300 border-b border-white/5 last:border-0 transition-colors">
                                    <span>{{ $wl['icon'] }}</span><span class="font-medium">{{ $wl['label'] }}</span>
                                </a>
                                @endforeach
                                <a href="{{ $waPlainUrl }}" target="_blank"
                                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-colors border-t border-white/[0.05]">
                                    📞 <span>Hubungi saja (tanpa template)</span>
                                </a>
                            </div>
                        </div>

                        @php
                            $delMsg = 'Hapus order #'.$orderId.' ('.$order->customer_name.')? Permanen!';
                            if (in_array($order->payment_status, ['lunas','menunggu_konfirmasi'])) {
                                $delMsg .= ' Order sudah '.($order->payment_status === 'lunas' ? 'LUNAS' : 'menunggu konfirmasi').' — yakin?';
                            }
                        @endphp
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline"
                              data-adm-confirm="{{ addslashes($delMsg) }}"
                              data-adm-ajax data-adm-danger data-adm-remove-closest=".adm-card">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn-adm border border-red-500/30 text-red-400 hover:bg-red-900/20">
                                🗑️ Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Bukti Bayar bar ──────────────────────────────────── --}}
        @if($order->payment_proof)
        @php $ext = pathinfo($order->payment_proof, PATHINFO_EXTENSION); @endphp
        <div class="border-t border-white/5 bg-slate-900/40 px-4 sm:px-5 py-3 flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold text-amber-300">💳 Bukti Pembayaran:</span>
            @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                <a href="{{ route('admin.orders.proof', $order) }}" target="_blank" class="group inline-block">
                    <img src="{{ route('admin.orders.proof', $order) }}" alt="Bukti"
                         class="h-12 w-auto rounded border border-amber-500/30 object-contain group-hover:scale-105 transition-transform">
                </a>
                <a href="{{ route('admin.orders.proof', $order) }}" target="_blank"
                   class="text-xs text-amber-400 hover:text-amber-300 font-medium hover:underline">Lihat penuh ↗</a>
            @else
                <a href="{{ route('admin.orders.proof', $order) }}" target="_blank"
                   class="btn-adm border border-blue-500/30 text-blue-400 hover:bg-blue-900/20">📄 Lihat PDF</a>
            @endif
        </div>
        @endif


        {{-- ── Status hint bar ─────────────────────────────────── --}}
        @if($order->payment_status === 'belum_bayar' && !$isSpam)
        <div class="border-t border-amber-700/20 bg-amber-900/10 px-4 sm:px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-amber-400 text-xs">💡 Menunggu pembayaran dari pelanggan</span>
            <a href="{{ $waPlainUrl }}" target="_blank" class="text-xs font-semibold text-amber-300 hover:text-amber-200 shrink-0">Hubungi via WA →</a>
        </div>
        @elseif($isSpam)
        <div class="border-t border-red-700/20 bg-red-900/10 px-4 sm:px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-red-400 text-xs">⚠️ Sudah {{ $order->created_at->diffInHours(now()) }} jam belum bayar</span>
            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline"
                  data-adm-confirm="Order #{{ $orderId }} sudah {{ $order->created_at->diffInHours(now()) }} jam belum bayar. Hapus permanen?"
                  data-adm-ajax data-adm-danger data-adm-remove-closest=".adm-card">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-semibold text-red-300 hover:text-red-200 shrink-0 cursor-pointer">
                    Hapus →
                </button>
            </form>
        </div>
        @elseif($order->payment_status === 'menunggu_konfirmasi')
        <div class="border-t border-blue-700/20 bg-blue-900/10 px-4 sm:px-5 py-2">
            <span class="text-blue-400 text-xs">💳 Bukti bayar sudah dikirim — konfirmasi sekarang</span>
        </div>
        @elseif($order->payment_status === 'lunas' && !$order->wedding_id)
        <div class="border-t border-purple-700/20 bg-purple-900/10 px-4 sm:px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-purple-400 text-xs">🛠️ Sudah lunas — buat undangannya sekarang</span>
            <a href="{{ route('admin.weddings.create.form', $order->template) }}?order_id={{ $order->id }}"
               class="text-xs font-semibold text-purple-300 hover:text-purple-200 shrink-0">Buat Undangan →</a>
        </div>
        @elseif($order->status === 'selesai')
        <div class="border-t border-green-700/20 bg-green-900/10 px-4 sm:px-5 py-2">
            <span class="text-green-400 text-xs font-medium">✅ Order selesai</span>
        </div>
        @elseif($order->payment_status === 'ditolak')
        <div class="border-t border-red-700/20 bg-red-900/10 px-4 sm:px-5 py-2 flex items-center justify-between gap-2">
            <span class="text-red-400 text-xs">❌ Pembayaran ditolak{{ $order->rejection_reason ? ' — '.$order->rejection_reason : '' }}</span>
            <a href="{{ $waPlainUrl }}" target="_blank" class="text-xs font-semibold text-red-300 hover:text-red-200 shrink-0">Hubungi via WA →</a>
        </div>
        @endif

    </div>{{-- end card --}}
    @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">{{ $orders->appends(request()->only(['status','payment','date','category','search']))->links() }}</div>

    @endif

@endsection

@push('styles')
<style @nonce>
    .btn-adm {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 12px; border-radius: 8px; font-size: 0.72rem;
        font-weight: 700; transition: all 0.15s; white-space: nowrap;
        border: 1px solid transparent; cursor: pointer; font-family: inherit;
    }
    .badge {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 6px; font-size: 0.7rem;
        font-weight: 700; border: 1px solid transparent;
    }
</style>
@endpush
