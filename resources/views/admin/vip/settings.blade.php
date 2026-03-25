@extends('admin.layout')

@section('title', 'Pengaturan VIP — ' . $wedding->bride_name)

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.vip.dashboard', ['wedding_id' => $wedding->id]) }}" class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1 mb-1">← VIP Dashboard</a>
        <h1 class="text-2xl font-semibold text-slate-100">⚙️ Pengaturan VIP</h1>
        <p class="text-slate-400 text-sm mt-1">{{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-green-900/40 border border-green-700/50 text-green-300 text-sm">✅ {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-lg bg-red-900/40 border border-red-700/50 text-red-300 text-sm">
    <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('admin.vip.settings.update', $wedding) }}" method="POST" class="space-y-6 max-w-2xl">
    @csrf @method('PUT')

    {{-- 1. Video Embed --}}
    <div class="bg-slate-800 rounded-xl border border-white/5 p-5">
        <h3 class="text-slate-100 font-semibold mb-1 flex items-center gap-2">🎬 Video Embed</h3>
        <p class="text-slate-400 text-sm mb-4">URL YouTube atau Vimeo yang ditampilkan di halaman undangan.</p>
        <input type="url" name="video_url" value="{{ old('video_url', $wedding->video_url) }}"
               placeholder="https://www.youtube.com/watch?v=..." maxlength="500"
               class="w-full bg-slate-700 border border-white/10 rounded-lg px-4 py-2.5 text-slate-100 text-sm placeholder-slate-500 focus:ring-2 focus:ring-amber-500/50 focus:outline-none">
        @if($wedding->video_url)
        <p class="text-xs text-slate-500 mt-1">Aktif: <a href="{{ $wedding->video_url }}" target="_blank" class="text-amber-400 hover:underline truncate">{{ $wedding->video_url }}</a></p>
        @endif
    </div>

    {{-- 2. Password Protection --}}
    <div class="bg-slate-800 rounded-xl border border-white/5 p-5">
        <h3 class="text-slate-100 font-semibold mb-1 flex items-center gap-2">🔒 Password Protection</h3>
        <p class="text-slate-400 text-sm mb-4">Tamu wajib memasukkan password sebelum bisa melihat undangan. Kosongkan untuk tidak mengubah password saat ini.</p>
        <input type="text" name="vip_password" value=""
               placeholder="{{ $wedding->vip_password ? 'Isi untuk ganti password...' : 'Buat password baru...' }}" maxlength="100" minlength="4"
               class="w-full bg-slate-700 border border-white/10 rounded-lg px-4 py-2.5 text-slate-100 text-sm placeholder-slate-500 focus:ring-2 focus:ring-amber-500/50 focus:outline-none">
        <p class="text-xs text-slate-500 mt-1">Minimal 4 karakter jika diisi.</p>
        @if($wedding->vip_password)
        <div class="flex items-center justify-between mt-2">
            <p class="text-xs text-green-400">🔐 Password aktif — undangan dilindungi</p>
            <form action="{{ route('admin.vip.settings.clear-password', $wedding) }}" method="POST" class="inline"
                  data-adm-confirm="Hapus password? Undangan akan terbuka untuk semua orang." data-adm-danger>
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-400 hover:underline">Hapus password</button>
            </form>
        </div>
        @endif
    </div>

    {{-- 3. Guestbook --}}
    <div class="bg-slate-800 rounded-xl border border-white/5 p-5">
        <h3 class="text-slate-100 font-semibold mb-1 flex items-center gap-2">💬 Guestbook Digital</h3>
        <p class="text-slate-400 text-sm mb-4">Tampilkan kolom ucapan di halaman undangan agar tamu bisa menulis doa & pesan.</p>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="guestbook_enabled" value="0">
            <input type="checkbox" name="guestbook_enabled" value="1" {{ $wedding->guestbook_enabled ? 'checked' : '' }}
                   class="w-4 h-4 rounded accent-amber-500">
            <span class="text-slate-300 text-sm">Aktifkan Guestbook di halaman undangan</span>
        </label>
    </div>

    {{-- 4. Notifikasi Email --}}
    <div class="bg-slate-800 rounded-xl border border-white/5 p-5">
        <h3 class="text-slate-100 font-semibold mb-1 flex items-center gap-2">📧 Notifikasi Email RSVP</h3>
        <p class="text-slate-400 text-sm mb-4">Dapat email otomatis setiap kali tamu konfirmasi hadir/tidak. Kosongkan untuk nonaktifkan.</p>
        <input type="email" name="notify_email" value="{{ old('notify_email', $wedding->notify_email) }}"
               placeholder="email@kamu.com" maxlength="255"
               class="w-full bg-slate-700 border border-white/10 rounded-lg px-4 py-2.5 text-slate-100 text-sm placeholder-slate-500 focus:ring-2 focus:ring-amber-500/50 focus:outline-none">
    </div>

    {{-- 5. Extra Acara --}}
    <div class="bg-slate-800 rounded-xl border border-white/5 p-5">
        <h3 class="text-slate-100 font-semibold mb-1 flex items-center gap-2">📅 Extra Acara</h3>
        <p class="text-slate-400 text-sm mb-4">Tambah acara tambahan selain akad & resepsi (syukuran, pengajian, dll). Maks 5 acara.</p>
        <div class="space-y-3" id="extra-events-wrap">
            @php $events = $wedding->extra_events ?? []; @endphp
            @for($i = 0; $i < 5; $i++)
            @php $ev = $events[$i] ?? []; @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 p-3 rounded-lg bg-slate-700/50 border border-white/5">
                <input type="text" name="extra_events[{{ $i }}][label]" value="{{ $ev['label'] ?? '' }}"
                       placeholder="Nama Acara" maxlength="80"
                       class="col-span-2 md:col-span-1 bg-slate-700 border border-white/10 rounded px-3 py-2 text-slate-100 text-xs placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500/50">
                <input type="date" name="extra_events[{{ $i }}][date]" value="{{ $ev['date'] ?? '' }}"
                       class="bg-slate-700 border border-white/10 rounded px-3 py-2 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500/50">
                <input type="text" name="extra_events[{{ $i }}][time]" value="{{ $ev['time'] ?? '' }}"
                       placeholder="09:00 WIB" maxlength="20"
                       class="bg-slate-700 border border-white/10 rounded px-3 py-2 text-slate-100 text-xs placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500/50">
                <input type="text" name="extra_events[{{ $i }}][location]" value="{{ $ev['location'] ?? '' }}"
                       placeholder="Nama Gedung / Alamat" maxlength="255"
                       class="col-span-2 md:col-span-1 bg-slate-700 border border-white/10 rounded px-3 py-2 text-slate-100 text-xs placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500/50">
            </div>
            @endfor
        </div>
        <p class="text-xs text-slate-500 mt-2">Baris yang label-nya kosong akan diabaikan.</p>
    </div>

    <button type="submit"
            class="px-6 py-3 rounded-lg bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-500 hover:to-amber-600 text-slate-900 font-bold text-sm shadow-md transition">
        💾 Simpan Pengaturan VIP
    </button>
</form>

@endsection
