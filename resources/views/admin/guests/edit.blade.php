@extends('admin.layout')

@section('title', 'Edit Tamu — ' . $guest->guest_name)

@section('content')

    <a href="{{ route('admin.guests.index', ['wedding_id' => $guest->wedding_id]) }}"
       class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-3">
       ← Kembali ke Daftar Tamu
    </a>
    <h1 class="text-2xl font-semibold text-slate-100 mb-2">Edit Tamu</h1>
    <p class="text-slate-400 text-sm mb-6">Kode: <span class="font-mono text-slate-300">{{ $guest->slug_name ?? '-' }}</span> —
        @if($guest->wedding->groom_name)
            {{ $guest->wedding->bride_name }} & {{ $guest->wedding->groom_name }}
        @else
            🎂 {{ $guest->wedding->bride_name }}
        @endif
    </p>

    @if($errors->any())
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-red-500">
            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.guests.update', $guest) }}" method="post" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="adm-card p-6 space-y-4">
            <div>
                <label for="guest_name" class="block text-sm font-medium text-slate-300 mb-1">Nama tamu *</label>
                <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name', $guest->guest_name) }}" required maxlength="255"
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-slate-200 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition">
            </div>
            <div>
                <label for="group_name" class="block text-sm font-medium text-slate-300 mb-1">Grup / Keluarga (opsional)</label>
                <input type="text" name="group_name" id="group_name" value="{{ old('group_name', $guest->group_name) }}" maxlength="100"
                       placeholder="cth: Keluarga Budi, Rekan Kerja, Panitia"
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-slate-200 placeholder-slate-500 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-300 mb-1">No. WA / Telepon (opsional)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $guest->phone) }}" maxlength="20"
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-slate-200 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email (opsional)</label>
                <input type="email" name="email" id="email" value="{{ old('email', $guest->email) }}" maxlength="100"
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-slate-200 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition">
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-slate-300 mb-1">Catatan (opsional)</label>
                <textarea name="notes" id="notes" rows="2" maxlength="500"
                          class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-slate-200 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition">{{ old('notes', $guest->notes) }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold shadow-lg transition-all">
                Simpan
            </button>
            <a href="{{ route('admin.guests.index', ['wedding_id' => $guest->wedding_id]) }}"
               class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition">
               Batal
            </a>
        </div>
    </form>

@endsection

