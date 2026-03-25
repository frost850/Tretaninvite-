@extends('admin.layout')

@section('title', 'Edit Kartu Ucapan — ' . $w->bride_name)

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-slate-400 text-sm">💌 Template: <strong class="text-violet-300">{{ $templateInfo['label'] }}</strong></p>
            <h1 class="text-2xl font-semibold text-slate-100">Edit Kartu Ucapan</h1>
            <p class="text-slate-400 text-sm mt-1">{{ $w->bride_name }}</p>
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

    <form action="{{ route('admin.greetings.update', $w->id) }}" method="post" class="space-y-0">
        @csrf
        @method('PUT')
        @include('admin.greetings._form', ['w' => $w, 'template' => $w->template, 'templateInfo' => $templateInfo])

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold shadow-lg transition-all">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('admin.weddings.index') }}"
               class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition">
               Batal
            </a>
        </div>
    </form>

    {{-- Bagikan Link --}}
    <div class="mt-6 p-5 rounded-xl border border-violet-500/20 bg-violet-900/10">
        <h3 class="text-sm font-semibold text-violet-300 mb-1">🔗 Link Kartu Ucapan</h3>
        <p class="text-xs text-slate-400 mb-2">Bagikan link ini kepada penerima kartu ucapan.</p>
        <div class="flex items-center gap-2">
            <code class="flex-1 text-xs bg-slate-900 text-violet-300 border border-white/10 rounded-lg px-3 py-2 break-all">{{ url('/' . $w->slug) }}</code>
            <button onclick="navigator.clipboard.writeText('{{ url('/' . $w->slug) }}').then(()=>this.textContent='✓ Disalin!')"
                    class="px-3 py-2 rounded-lg bg-violet-700 hover:bg-violet-600 text-white text-xs font-medium transition whitespace-nowrap">
                Salin
            </button>
        </div>
    </div>

    {{-- Hapus Kartu Ucapan --}}
    <div class="mt-4 p-5 rounded-xl border border-red-500/20 bg-red-900/10">
        <h3 class="text-sm font-semibold text-red-400 mb-1">⚠️ Hapus Kartu Ucapan</h3>
        <p class="text-xs text-red-400/70 mb-3">Menghapus kartu ucapan ini secara permanen.</p>
        <form action="{{ route('admin.greetings.destroy', $w->id) }}" method="post"
              data-adm-confirm="Yakin hapus kartu ucapan untuk {{ addslashes($w->bride_name) }}?"
              data-adm-danger>
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-700 hover:bg-red-600 text-white text-sm font-medium transition">
                Hapus Kartu
            </button>
        </form>
    </div>

@endsection
