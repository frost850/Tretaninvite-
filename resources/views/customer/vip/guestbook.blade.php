@extends('customer.layout')

@section('title', 'Guestbook — ' . $wedding->bride_name . ($wedding->groom_name ? ' & ' . $wedding->groom_name : ''))

@section('nav')
    <a href="{{ route('my.vip.dashboard', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('my.vip.guestbook', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.guestbook') ? 'active' : '' }}">Guestbook</a>
@endsection

@section('content')

<div class="mb-6 fade-up">
    <a href="{{ route('my.vip.dashboard', $token) }}"
       class="text-amber-400 hover:text-amber-300 text-sm inline-flex items-center gap-1 mb-3 hover:underline">
        &larr; Portal VIP
    </a>
    <h1 class="text-2xl font-black text-slate-100">Guestbook Digital</h1>
    <p class="text-slate-400 text-sm mt-1">
        {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}
    </p>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-900/40 border border-green-700/40 text-green-300 text-sm fade-up">
    {{ session('success') }}
</div>
@endif

<div class="mb-6 px-4 py-3 rounded-xl bg-slate-800 border border-white/5 text-slate-400 text-sm fade-up">
    Anda dapat menyembunyikan ucapan yang tidak pantas. Ucapan tersembunyi tidak tampil di halaman undangan tamu.
</div>

@if($entries->isEmpty())
<div class="rounded-xl bg-slate-800/50 border border-white/5 p-16 text-center text-slate-500 fade-up">
    <p class="font-medium text-slate-400">Belum ada ucapan masuk.</p>
    <p class="text-sm mt-1">Tamu yang membuka undangan dapat menulis ucapan di halaman undangan.</p>
</div>
@else
<div class="space-y-3">
    @foreach($entries as $entry)
    @php
        $colors = ['from-purple-500 to-pink-500','from-blue-500 to-cyan-500','from-green-500 to-teal-500','from-amber-500 to-orange-500','from-rose-500 to-red-500','from-indigo-500 to-violet-500'];
        $ci = abs(crc32($entry->name)) % count($colors);
    @endphp
    <div class="flex items-start gap-4 rounded-xl bg-slate-800 border {{ $entry->is_approved ? 'border-white/5' : 'border-orange-700/40 opacity-70' }} p-4 fade-up">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $colors[$ci] }} flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
            {{ mb_strtoupper(mb_substr($entry->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-semibold text-slate-100">{{ $entry->name }}</span>
                @if(!$entry->is_approved)
                <span class="px-2 py-0.5 rounded-full text-xs bg-orange-900/50 text-orange-400 border border-orange-700/50">Disembunyikan</span>
                @endif
                <span class="text-slate-500 text-xs ml-auto">{{ $entry->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <p class="text-slate-300 text-sm mt-1 leading-relaxed">{{ $entry->message }}</p>
        </div>
        <div class="flex flex-col gap-1.5 flex-shrink-0">
            <form action="{{ route('my.vip.guestbook.toggle', [$token, $entry]) }}" method="POST">
                @csrf @method('PATCH')
                <button class="text-xs px-3 py-1.5 rounded-lg border transition w-full
                    {{ $entry->is_approved
                        ? 'border-yellow-700/50 text-yellow-400 hover:bg-yellow-900/20'
                        : 'border-green-700/50 text-green-400 hover:bg-green-900/20' }}">
                    {{ $entry->is_approved ? 'Sembunyikan' : 'Tampilkan' }}
                </button>
            </form>
            <form action="{{ route('my.vip.guestbook.destroy', [$token, $entry]) }}" method="POST"
                  onsubmit="return confirm('Hapus ucapan ini secara permanen?')">
                @csrf @method('DELETE')
                <button class="text-xs px-3 py-1.5 rounded-lg border border-red-700/50 text-red-400 hover:bg-red-900/20 transition w-full">
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $entries->withQueryString()->links() }}
</div>
@endif

@endsection