@extends('admin.layout')

@section('title', 'Guestbook — ' . $wedding->bride_name)

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.vip.dashboard', ['wedding_id' => $wedding->id]) }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">← VIP Dashboard</a>
        <h1 class="text-2xl font-semibold text-slate-100">💬 Guestbook Digital</h1>
        <p class="text-slate-400 text-sm mt-1">Ucapan & doa dari tamu di halaman undangan</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-green-900/40 border border-green-700/50 text-green-300 text-sm">
    ✅ {{ session('success') }}
</div>
@endif

@if($entries->isEmpty())
<div class="text-center py-20 text-slate-500">
    <div class="text-4xl mb-3">💬</div>
    <p>Belum ada ucapan masuk.</p>
    <p class="text-sm mt-2">Tamu yang membuka undangan dapat menulis ucapan di halaman undangan.</p>
</div>
@else
<div class="space-y-3">
    @foreach($entries as $entry)
    <div class="adm-entry-card bg-slate-800 rounded-xl border {{ $entry->is_approved ? 'border-white/5' : 'border-red-800/40 opacity-60' }} p-4 flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
            {{ mb_strtoupper(mb_substr($entry->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-semibold text-slate-100">{{ $entry->name }}</span>
                @if(!$entry->is_approved)
                <span class="px-2 py-0.5 rounded text-xs bg-red-900/50 text-red-400 border border-red-800/50">Disembunyikan</span>
                @endif
                <span class="text-slate-500 text-xs ml-auto">{{ $entry->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <p class="text-slate-300 text-sm mt-1 leading-relaxed">{{ $entry->message }}</p>
        </div>
        <div class="flex flex-col gap-1 flex-shrink-0">
            <form action="{{ route('admin.vip.guestbook.toggle', $entry) }}" method="POST">
                @csrf @method('PATCH')
                <button class="text-xs px-3 py-1.5 rounded border {{ $entry->is_approved ? 'border-yellow-700/50 text-yellow-400 hover:bg-yellow-900/20' : 'border-green-700/50 text-green-400 hover:bg-green-900/20' }} transition w-full">
                    {{ $entry->is_approved ? 'Sembunyikan' : 'Tampilkan' }}
                </button>
            </form>
            <form action="{{ route('admin.vip.guestbook.destroy', $entry) }}" method="POST"
                  data-adm-confirm="Hapus ucapan ini?"
                  data-adm-ajax data-adm-danger data-adm-remove-closest=".adm-entry-card">
                @csrf @method('DELETE')
                <button class="text-xs px-3 py-1.5 rounded border border-red-700/50 text-red-400 hover:bg-red-900/20 transition w-full">
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
