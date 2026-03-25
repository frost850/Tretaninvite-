@extends('admin.layout')

@section('title', 'Pilih Template Undangan')

@section('content')

    <h1 class="text-2xl font-semibold text-slate-100 mb-2">Pilih Template Undangan</h1>
    <p class="text-slate-400 text-sm mb-8">Pilih desain yang Anda suka. Setelah itu Anda akan mengisi data acara.</p>

    {{-- ─── Undangan Pernikahan ────────────────────────────────────── --}}
    <h2 class="text-lg font-semibold text-slate-300 mb-3">💒 Undangan Pernikahan</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        @foreach($templates as $key => $info)
            @if(($info['category'] ?? 'wedding') === 'wedding')
            <div class="adm-card overflow-hidden hover:border-amber-500/40 transition-all">
                <a href="{{ route('preview.template', $key) }}" target="_blank"
                   class="block h-44 border-b border-white/5 overflow-hidden @unless(isset($info['preview_image'])) bg-gradient-to-br {{ $info['preview'] }} flex items-center justify-center @endunless">
                    @if(isset($info['preview_image']))
                        <img src="{{ asset($info['preview_image']) }}" alt="{{ $info['label'] }}"
                             class="w-full h-full object-cover object-top">
                    @else
                        <span class="text-4xl font-light">{{ $info['icon'] ?? '💒' }}</span>
                    @endif
                </a>
                <div class="p-4">
                    <h3 class="font-semibold text-slate-100 mb-1">{{ $info['label'] }}</h3>
                    <p class="text-slate-400 text-sm mb-4">{{ $info['description'] }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('preview.template', $key) }}" target="_blank"
                           class="flex-1 text-center py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">Contoh</a>
                        <a href="{{ route('admin.weddings.create.form', $key) }}"
                           class="flex-1 text-center py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-medium hover:from-amber-600 hover:to-orange-600 transition">Buat</a>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

    {{-- ─── Undangan Ulang Tahun ───────────────────────────────────── --}}
    <h2 class="text-lg font-semibold text-slate-300 mb-3">🎂 Undangan Ulang Tahun</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        @foreach($templates as $key => $info)
            @if(($info['category'] ?? 'wedding') === 'birthday')
            <div class="adm-card overflow-hidden hover:border-pink-500/40 transition-all">
                <a href="{{ route('preview.template', $key) }}" target="_blank"
                   class="block h-44 border-b border-white/5 overflow-hidden @unless(isset($info['preview_image'])) bg-gradient-to-br {{ $info['preview'] }} flex items-center justify-center @endunless">
                    @if(isset($info['preview_image']))
                        <img src="{{ asset($info['preview_image']) }}" alt="{{ $info['label'] }}"
                             class="w-full h-full object-cover object-top">
                    @else
                        <span class="text-4xl font-light">{{ $info['icon'] ?? '🎂' }}</span>
                    @endif
                </a>
                <div class="p-4">
                    <h3 class="font-semibold text-slate-100 mb-1">{{ $info['label'] }}</h3>
                    <p class="text-slate-400 text-sm mb-4">{{ $info['description'] }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('preview.template', $key) }}" target="_blank"
                           class="flex-1 text-center py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">Contoh</a>
                        <a href="{{ route('admin.birthdays.create.form', $key) }}"
                           class="flex-1 text-center py-2 rounded-lg bg-gradient-to-r from-pink-600 to-rose-600 text-white text-sm font-medium hover:from-pink-700 hover:to-rose-700 transition">Buat</a>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

    {{-- ─── Kartu Ucapan Ulang Tahun ─────────────────────────────────── --}}
    <h2 class="text-lg font-semibold text-slate-300 mb-3">💌 Kartu Ucapan Ulang Tahun</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        @foreach($templates as $key => $info)
            @if(($info['category'] ?? 'wedding') === 'greeting')
            <div class="adm-card overflow-hidden hover:border-violet-500/40 transition-all">
                <a href="{{ route('preview.template', $key) }}" target="_blank"
                   class="block h-44 border-b border-white/5 overflow-hidden @unless(isset($info['preview_image'])) bg-gradient-to-br {{ $info['preview'] }} flex items-center justify-center @endunless">
                    @if(isset($info['preview_image']))
                        <img src="{{ asset($info['preview_image']) }}" alt="{{ $info['label'] }}"
                             class="w-full h-full object-cover object-top">
                    @else
                        <span class="text-4xl font-light">{{ $info['icon'] ?? '💌' }}</span>
                    @endif
                </a>
                <div class="p-4">
                    <h3 class="font-semibold text-slate-100 mb-1">{{ $info['label'] }}</h3>
                    <p class="text-slate-400 text-sm mb-4">{{ $info['description'] }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('preview.template', $key) }}" target="_blank"
                           class="flex-1 text-center py-2 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 transition">Contoh</a>
                        <a href="{{ route('admin.greetings.create.form', $key) }}"
                           class="flex-1 text-center py-2 rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 text-white text-sm font-medium hover:from-violet-700 hover:to-purple-700 transition">Buat</a>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

    <p class="mt-4">
        <a href="{{ route('admin.weddings.index') }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm">← Kembali ke daftar undangan</a>
    </p>

@endsection

