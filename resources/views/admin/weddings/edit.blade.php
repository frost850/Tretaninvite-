@extends('admin.layout')

@section('title', 'Edit Undangan — ' . $w->bride_name . ($w->groom_name ? ' & ' . $w->groom_name : ''))

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-slate-400 text-sm">Template: <strong class="text-amber-300">{{ $templateInfo['label'] }}</strong></p>
            <h1 class="text-2xl font-semibold text-slate-100">Edit Undangan</h1>
            <p class="text-slate-400 text-sm mt-1">{{ $w->bride_name }}{{ $w->groom_name ? ' & ' . $w->groom_name : '' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('invitation.show', $w->slug) }}" target="_blank"
               class="px-3 py-1.5 rounded-lg border border-white/10 text-slate-300 text-sm hover:bg-white/5 hover:text-violet-300 transition">
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

    <form action="{{ route('admin.weddings.update', $w->id) }}" method="post" class="space-y-0">
        @csrf
        @method('PUT')
        @include('admin.weddings._form', ['w' => $w, 'template' => $w->template, 'templateInfo' => $templateInfo])

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold shadow-lg transition-all">
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
        <form action="{{ route('admin.weddings.destroy', $w->id) }}" method="post"
              data-adm-confirm="Yakin hapus undangan {{ addslashes($w->bride_name) }}{{ $w->groom_name ? ' &amp; ' . addslashes($w->groom_name) : '' }}? Semua data tamu ikut terhapus."
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

