@extends('admin.layout')

@section('title', 'Recycle Bin')

@section('content')

    {{-- Flash banners handled globally via toast in layout --}}

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-100 flex items-center gap-2">
                🗑️ Recycle Bin
                @if($totalCount > 0)
                    <span class="text-sm font-semibold px-2.5 py-0.5 rounded-full bg-red-900/40 border border-red-700/40 text-red-300">{{ $totalCount }} item</span>
                @endif
            </h1>
            <p class="text-slate-500 text-sm mt-0.5">Item yang dihapus masih tersimpan 30 hari sebelum otomatis dihapus permanen</p>
        </div>
        @if(session('admin_is_super') && $totalCount > 0)
        <form method="POST" action="{{ route('admin.recycle.purge') }}"
              data-adm-confirm="⚠️ HAPUS SEMUA PERMANEN? Seluruh {{ $totalCount }} item di Recycle Bin akan dihapus selamanya termasuk semua file foto dan data tamu. Tindakan ini TIDAK DAPAT dibatalkan!"
              data-adm-danger>
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-red-400 border border-red-800/50 hover:bg-red-900/30 hover:text-red-300 transition">
                🗑️ Kosongkan Semua
            </button>
        </form>
        @endif
    </div>

    {{-- ── Auto-purge warning ──────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 px-4 py-3 mb-6 rounded-xl border border-amber-800/30 bg-amber-900/10 text-amber-400/80 text-xs">
        <span class="shrink-0">⏳</span>
        Item yang sudah lebih dari <strong>30 hari</strong> di Recycle Bin akan dihapus otomatis setiap malam pukul 04:00 WIB.
        Pulihkan item penting sebelum tenggat waktu tersebut.
    </div>

    @if($totalCount === 0)
        {{-- ── Empty state ────────────────────────────────────────────────── --}}
        <div class="text-center py-24 text-slate-500">
            <div class="text-5xl mb-4">🗑️</div>
            <div class="text-lg font-semibold text-slate-400 mb-1">Recycle Bin kosong</div>
            <div class="text-sm">Tidak ada item yang dihapus saat ini.</div>
        </div>
    @else

        {{-- ── Section: Undangan ──────────────────────────────────────────── --}}
        @if($weddings->count())
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                💍 Undangan
                <span class="px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-slate-300 text-xs font-bold">{{ $weddings->count() }}</span>
            </h2>
            <div class="space-y-3">
                @foreach($weddings as $wedding)
                @php
                    $tplData   = $allTemplates[$wedding->template] ?? null;
                    $tplLabel  = $tplData['label']    ?? $wedding->template;
                    $tplIcon   = $tplData['icon']     ?? '📄';
                    $tplCat    = $tplData['category'] ?? 'wedding';
                    $daysAgo   = (int) now()->diffInDays($wedding->deleted_at);
                    $daysLeft  = max(0, 30 - $daysAgo);
                    $urgency   = $daysLeft <= 3 ? 'text-red-400' : ($daysLeft <= 7 ? 'text-amber-400' : 'text-slate-500');
                    $pkgColors = ['trial' => 'bg-slate-700/60 text-slate-300', 'basic' => 'bg-blue-900/40 text-blue-300', 'premium' => 'bg-violet-900/40 text-violet-300', 'vip' => 'bg-amber-900/40 text-amber-300'];
                    $pkgColor  = $pkgColors[$wedding->package] ?? 'bg-slate-700/60 text-slate-300';
                    $title     = match($tplCat) {
                        'birthday'   => $wedding->bride_name,
                        'greeting'   => $wedding->bride_name,
                        default      => trim(($wedding->bride_name ?? '') . ($wedding->groom_name ? ' & ' . $wedding->groom_name : '')),
                    };
                @endphp
                <div class="adm-card p-4 flex flex-wrap items-center gap-4">
                    {{-- Icon + info --}}
                    <div class="text-2xl shrink-0 w-10 text-center">{{ $tplIcon }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-semibold text-slate-100 truncate max-w-xs">{{ $title ?: 'Tanpa Nama' }}</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $pkgColor }}">{{ strtoupper($wedding->package) }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-slate-400">{{ $tplLabel }}</span>
                        </div>
                        <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                            <span>🔗 /{{ $wedding->slug }}</span>
                            <span>🗑️ Dihapus {{ $wedding->deleted_at->diffForHumans() }}</span>
                            <span class="{{ $urgency }} font-medium">⏳ {{ $daysLeft }} hari tersisa</span>
                        </div>
                    </div>
                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if(session('admin_is_super'))
                        <form method="POST" action="{{ route('admin.recycle.weddings.restore', $wedding->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-400 border border-emerald-800/50 hover:bg-emerald-900/30 hover:text-emerald-300 transition">
                                ↩ Pulihkan
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.recycle.weddings.force-delete', $wedding->id) }}"
                              data-adm-confirm="Hapus permanen undangan '{{ addslashes($title) }}'? Semua foto dan data tamu akan dihapus selamanya. Tindakan ini TIDAK DAPAT dibatalkan."
                              data-adm-ajax data-adm-danger data-adm-remove-closest=".adm-card">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-400/80 border border-red-900/40 hover:bg-red-900/30 hover:text-red-300 transition">
                                🗑️ Hapus Permanen
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-slate-500 italic">Hanya Super Admin</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Section: Pesanan ───────────────────────────────────────────── --}}
        @if($orders->count())
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                📋 Pesanan
                <span class="px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-slate-300 text-xs font-bold">{{ $orders->count() }}</span>
            </h2>
            <div class="space-y-3">
                @foreach($orders as $order)
                @php
                    $tplData    = $allTemplates[$order->template] ?? null;
                    $tplLabel   = $tplData['label']   ?? $order->template;
                    $tplIcon    = $tplData['icon']    ?? '📄';
                    $orderId    = strtoupper(substr($order->public_token ?? '', 0, 8));
                    $daysAgo    = (int) now()->diffInDays($order->deleted_at);
                    $daysLeft   = max(0, 30 - $daysAgo);
                    $urgency    = $daysLeft <= 3 ? 'text-red-400' : ($daysLeft <= 7 ? 'text-amber-400' : 'text-slate-500');
                    $statusColors = [
                        'menunggu'  => 'bg-amber-900/40 text-amber-300',
                        'diproses'  => 'bg-blue-900/40 text-blue-300',
                        'selesai'   => 'bg-emerald-900/40 text-emerald-300',
                        'dibatalkan'=> 'bg-red-900/40 text-red-300',
                    ];
                    $statusColor = $statusColors[$order->status] ?? 'bg-slate-700/60 text-slate-300';
                    $pkgColors   = ['trial' => 'bg-slate-700/60 text-slate-300', 'basic' => 'bg-blue-900/40 text-blue-300', 'premium' => 'bg-violet-900/40 text-violet-300', 'vip' => 'bg-amber-900/40 text-amber-300'];
                    $pkgColor    = $pkgColors[$order->package ?? ''] ?? 'bg-slate-700/60 text-slate-300';
                    $orderName   = trim(($order->bride_name ?? '') . ($order->groom_name ? ' & ' . $order->groom_name : ''));
                    $payColors   = [
                        'lunas'               => 'bg-emerald-900/40 text-emerald-300',
                        'menunggu_konfirmasi'  => 'bg-blue-900/40 text-blue-300',
                        'ditolak'             => 'bg-red-900/40 text-red-300',
                        'belum_bayar'         => 'bg-amber-900/40 text-amber-300',
                    ];
                    $payColor    = $payColors[$order->payment_status ?? ''] ?? 'bg-slate-700/60 text-slate-300';
                    $payLabel    = match($order->payment_status ?? '') {
                        'lunas'              => '✅ Lunas',
                        'menunggu_konfirmasi'=> '🔍 Menunggu',
                        'ditolak'            => '❌ Ditolak',
                        'belum_bayar'        => '💳 Belum Bayar',
                        default              => ucfirst($order->payment_status ?? '–'),
                    };
                @endphp
                <div class="adm-card p-4 flex flex-wrap items-center gap-4">
                    {{-- Icon + info --}}
                    <div class="text-2xl shrink-0 w-10 text-center">{{ $tplIcon }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-semibold text-slate-100 truncate max-w-xs">{{ $order->customer_name ?? '–' }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full border {{ $statusColor }}">{{ ucfirst($order->status ?? '–') }}</span>
                            @if($order->payment_status)
                            <span class="text-[10px] px-2 py-0.5 rounded-full border {{ $payColor }}">{{ $payLabel }}</span>
                            @endif
                            @if($order->package)
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $pkgColor }}">{{ strtoupper($order->package) }}</span>
                            @endif
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-slate-400">{{ $tplLabel }}</span>
                        </div>
                        <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                            <span>#{{ $orderId }}</span>
                            @if($orderName)
                                <span>👤 {{ $orderName }}</span>
                            @endif
                            <span>🗑️ Dihapus {{ $order->deleted_at->diffForHumans() }}</span>
                            <span class="{{ $urgency }} font-medium">⏳ {{ $daysLeft }} hari tersisa</span>
                        </div>
                        @if($order->payment_status === 'ditolak' && $order->rejection_reason)
                        <div class="mt-1.5 text-xs text-red-300/70">❌ {{ $order->rejection_reason }}</div>
                        @endif
                    </div>
                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.recycle.orders.restore', $order->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-400 border border-emerald-800/50 hover:bg-emerald-900/30 hover:text-emerald-300 transition">
                                ↩ Pulihkan
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.recycle.orders.force-delete', $order->id) }}"
                              data-adm-confirm="Hapus permanen order #{{ $orderId }}? Bukti pembayaran akan dihapus selamanya. Tindakan ini TIDAK DAPAT dibatalkan."
                              data-adm-ajax data-adm-danger data-adm-remove-closest=".adm-card">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-400/80 border border-red-900/40 hover:bg-red-900/30 hover:text-red-300 transition">
                                🗑️ Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif

@endsection
