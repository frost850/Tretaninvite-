@extends('admin.layout')

@section('title', 'Import Daftar Tamu')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            @if(!empty($weddingId))
                <a href="{{ route('admin.guests.index', ['wedding_id' => $weddingId]) }}"
                   class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1">
                   ← Kembali ke Daftar Tamu
                </a>
            @else
                <a href="{{ route('admin.weddings.index') }}"
                   class="text-amber-400 hover:text-amber-300 hover:underline text-sm inline-flex items-center gap-1">
                   ← Kembali ke Daftar Undangan
                </a>
            @endif
        </div>
    </div>
    <h1 class="text-2xl font-semibold text-slate-100 mb-2">Import Daftar Tamu</h1>

    <div class="adm-card border-l-4 border-amber-500 p-5 mb-6">
        <p class="font-medium text-amber-300 mb-2">Langkah import:</p>
        <ol class="list-decimal list-inside space-y-1 text-sm text-slate-300">
            <li><a href="{{ route('admin.guests.import.template') }}" class="text-amber-400 hover:text-amber-300 underline font-medium">Download template Excel</a> (kolom: <strong>Nama Tamu</strong>)</li>
            <li>Isi nama tamu (satu nama per baris)</li>
            <li>Pilih acara undangan dan mode import di bawah</li>
            <li>Upload file lalu klik Import</li>
        </ol>
        <div class="mt-3 pt-3 border-t border-white/[0.06]">
            <p class="text-xs font-semibold text-slate-300 mb-1.5">📋 Nama kolom yang diterima:</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach(['Nama_Tamu','Nama Tamu','nama','guest_name','guest','name'] as $fmt)
                <code class="bg-white/5 border border-white/10 px-2 py-0.5 rounded text-xs text-amber-300">{{ $fmt }}</code>
                @endforeach
            </div>
            <p class="text-slate-500 text-xs mt-1.5">Kolom WA opsional: <code class="bg-white/5 px-1 rounded">No_WA</code>, <code class="bg-white/5 px-1 rounded">Nomor WA</code>, <code class="bg-white/5 px-1 rounded">whatsapp</code>, <code class="bg-white/5 px-1 rounded">HP</code>, <code class="bg-white/5 px-1 rounded">phone</code> · Jika tanpa header, kolom pertama = nama.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-green-500">
            <div class="text-green-300 text-sm">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-red-500">
            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.guests.import') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="adm-card p-6 space-y-5">
            <div>
                <label for="wedding_id" class="block text-sm font-medium text-slate-300 mb-1">Acara undangan *</label>
                <select name="wedding_id" id="wedding_id" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-slate-200 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition">
                    <option value="">-- Pilih acara --</option>
                    @foreach($weddings as $w)
                        <option value="{{ $w->id }}" {{ (old('wedding_id', $weddingId ?? '') == $w->id) ? 'selected' : '' }}>
                            {{ $w->slug }} ({{ $w->bride_name }}{{ $w->groom_name ? ' & ' . $w->groom_name : ' 🎂' }}) — {{ $w->guests()->count() }} tamu
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="mode" class="block text-sm font-medium text-slate-300 mb-1">Mode import *</label>
                <select name="mode" id="mode" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-slate-200 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition">
                    <option value="append"          {{ old('mode') === 'append'          ? 'selected' : '' }}>Tambah ke daftar (append)</option>
                    <option value="skip_duplicates" {{ old('mode') === 'skip_duplicates' ? 'selected' : '' }}>Tambah, lewati nama yang sudah ada</option>
                    <option value="replace"         {{ old('mode') === 'replace'         ? 'selected' : '' }}>Ganti semua (hapus daftar lama, import baru)</option>
                </select>
                <p class="text-slate-500 text-xs mt-1">Maks. {{ \App\Imports\GuestsImport::MAX_ROWS }} baris per file.</p>
            </div>
            <div>
                <label for="file" class="block text-sm font-medium text-slate-300 mb-1">File Excel *</label>
                <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                       class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-900/40 file:text-amber-300 hover:file:bg-amber-900/60 file:font-medium file:transition-all">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold shadow-lg transition-all">
                ⬆️ Import
            </button>
        </div>
    </form>

    <div class="mt-6 adm-card p-5">
        <p class="text-slate-400 text-xs">
            <strong class="text-slate-300">Format kolom yang didukung:</strong><br>
            • Kolom nama: <code class="bg-white/5 px-1 rounded">Nama_Tamu</code>, <code class="bg-white/5 px-1 rounded">Nama Tamu</code>, <code class="bg-white/5 px-1 rounded">nama</code>, <code class="bg-white/5 px-1 rounded">guest_name</code>, <code class="bg-white/5 px-1 rounded">guest</code>, <code class="bg-white/5 px-1 rounded">name</code><br>
            • Kolom WA: <code class="bg-white/5 px-1 rounded">No_WA</code>, <code class="bg-white/5 px-1 rounded">Nomor WA</code>, <code class="bg-white/5 px-1 rounded">whatsapp</code>, <code class="bg-white/5 px-1 rounded">phone</code>, <code class="bg-white/5 px-1 rounded">HP</code> (opsional)<br>
            • Baris pertama = header, baris berikutnya = data.<br>
            • Jika tanpa header, kolom pertama dianggap nama.
        </p>
        <a href="{{ route('admin.guests.import.template') }}" class="text-amber-400 hover:text-amber-300 hover:underline text-xs font-medium mt-2 inline-block">
            ⬇ Download template Excel (Nama_Tamu + No_WA)
        </a>
    </div>

@endsection

