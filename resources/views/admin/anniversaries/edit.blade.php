@extends('admin.layout')

@section('title', 'Edit Undangan Anniversary — ' . $w->bride_name . ' & ' . $w->groom_name)

@section('content')

    @php
        $pkgMap = [
            'premium' => ['label' => 'Premium', 'bg' => 'bg-amber-900/40', 'text' => 'text-amber-300', 'border' => 'border-amber-600/40', 'icon' => '⭐'],
            'basic'   => ['label' => 'Basic',   'bg' => 'bg-blue-900/40',  'text' => 'text-blue-300',  'border' => 'border-blue-600/40',  'icon' => '📦'],
        ];
        $pkg     = $order ? ($order->package ?? 'basic') : ($w->package ?? 'basic');
        $pkgInfo = $pkgMap[$pkg] ?? $pkgMap['basic'];
        $pkgPrice = $order ? $order->packageAmount() : ($pkg === 'premium' ? 49999 : 39999);
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <p class="text-slate-400 text-sm">� Template: <strong class="text-rose-300">{{ $templateInfo['label'] }}</strong></p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $pkgInfo['bg'] }} {{ $pkgInfo['text'] }} {{ $pkgInfo['border'] }}">
                    {{ $pkgInfo['icon'] }} Paket {{ $pkgInfo['label'] }}
                    <span class="opacity-60 font-normal">· Rp {{ number_format($pkgPrice, 0, ',', '.') }}</span>
                </span>
                @if($order)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $order->payment_status === 'lunas' ? 'bg-emerald-900/40 text-emerald-300 border border-emerald-600/30'
                        : ($order->payment_status === 'menunggu_konfirmasi' ? 'bg-yellow-900/40 text-yellow-300 border border-yellow-600/30'
                        : 'bg-slate-800/60 text-slate-400 border border-slate-600/30') }}">
                    {{ $order->payment_status === 'lunas' ? '✅ Lunas'
                        : ($order->payment_status === 'menunggu_konfirmasi' ? '⏳ Menunggu Konfirmasi'
                        : '🕐 Belum Bayar') }}
                </span>
                @endif
            </div>
            <h1 class="text-2xl font-semibold text-slate-100">Edit Undangan Anniversary</h1>
            <p class="text-slate-400 text-sm mt-1">{{ $w->bride_name }} &amp; {{ $w->groom_name }}
                @if($order && $order->customer_name)
                · <span class="text-slate-500">Customer: <span class="text-slate-300">{{ $order->customer_name }}</span></span>
                @endif
                @if($order && $order->customer_phone)
                · <a href="https://wa.me/{{ preg_replace('/\D/', '', $order->customer_phone) }}" target="_blank" class="text-green-400 hover:text-green-300 transition text-xs">📱 WA</a>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('invitation.show', $w->slug) }}" target="_blank"
               class="px-3 py-1.5 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 hover:text-rose-300 transition">
                👁 Preview
            </a>
            <a href="{{ route('admin.weddings.index') }}"
               class="px-3 py-1.5 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">
                ← Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-green-500">
            <div class="text-green-300 text-sm">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-red-500">
            <div class="text-red-300 text-sm">{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-red-500">
            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.anniversaries.update', $w->id) }}" method="post" class="space-y-0">
        @csrf
        @method('PUT')
        @include('admin.anniversaries._form', ['w' => $w, 'template' => $w->template, 'templateInfo' => $templateInfo])

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-bold shadow-lg transition-all">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('admin.weddings.index') }}"
               class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition">
               Batal
            </a>
        </div>
    </form>

    {{-- Hapus Undangan --}}
    <div class="mt-8 p-5 rounded-xl border border-red-500/20 bg-red-900/10">
        <h3 class="text-sm font-semibold text-red-400 mb-1">⚠️ Hapus Undangan</h3>
        <p class="text-xs text-red-400/70 mb-3">Menghapus undangan akan menghapus semua data tamu dan RSVP secara permanen.</p>
        <form action="{{ route('admin.anniversaries.destroy', $w->id) }}" method="post"
              data-adm-confirm="Yakin hapus undangan anniversary {{ addslashes($w->bride_name) }} & {{ addslashes($w->groom_name) }}? Semua data tamu ikut terhapus."
              data-adm-danger>
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-700 hover:bg-red-600 text-white text-sm font-medium transition">
                Hapus Undangan
            </button>
        </form>
    </div>

@endsection
