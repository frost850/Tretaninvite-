<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coba Gratis — Undangan Pernikahan {{ $templateInfo['label'] }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 p-4 md:p-8">
<div class="max-w-xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-6">
        <a href="{{ route('packages.index') }}" class="text-stone-400 hover:text-stone-600 text-sm inline-flex items-center gap-1 mb-4">← Kembali ke Pilih Paket</a>
        <div class="text-4xl mb-2">💒</div>
        <h1 class="text-2xl font-bold text-stone-800">Coba Gratis — Undangan Pernikahan</h1>
        <p class="text-stone-500 text-sm mt-1">Template: <strong>{{ $templateInfo['label'] }}</strong></p>
    </div>

    {{-- Info banner --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6 text-sm text-amber-800">
        <div class="font-semibold mb-1">✨ Yang kamu dapat saat coba:</div>
        <ul class="space-y-1 text-amber-700">
            <li>✅ Undangan langsung aktif setelah isi form</li>
            <li>✅ Bisa dibagikan ke tamu (maks <strong>3 tamu</strong>)</li>
            <li>✅ RSVP dari tamu aktif</li>
            <li>⏱ Aktif selama <strong>1 hari</strong></li>
            <li class="text-amber-500">❌ Galeri foto, musik & live tracking (khusus Premium)</li>
        </ul>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('trial.store') }}" method="POST">
            @csrf
            {{-- Honeypot --}}
            <input type="text" name="website" id="website" value="" autocomplete="off" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
            <input type="hidden" name="template" value="{{ $template }}">

            <div class="space-y-4">

                {{-- Nama mempelai --}}
                <div>
                    <h2 class="text-sm font-semibold text-stone-600 mb-2 pb-1 border-b border-stone-100">💍 Data Mempelai</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Nama Mempelai Wanita <span class="text-red-500">*</span></label>
                            <input type="text" name="bride_name" value="{{ old('bride_name') }}"
                                   placeholder="cth: Siti Nurhaliza"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('bride_name') border-red-400 @enderror">
                            @error('bride_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Nama Mempelai Pria <span class="text-red-500">*</span></label>
                            <input type="text" name="groom_name" value="{{ old('groom_name') }}"
                                   placeholder="cth: Ahmad Dhani"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('groom_name') border-red-400 @enderror">
                            @error('groom_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Tanggal & Jam --}}
                <div>
                    <h2 class="text-sm font-semibold text-stone-600 mb-2 pb-1 border-b border-stone-100">📅 Waktu Acara</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Tanggal Acara <span class="text-red-500">*</span></label>
                            <input type="date" name="event_date" value="{{ old('event_date') }}"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('event_date') border-red-400 @enderror">
                            @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Jam Acara <span class="text-red-500">*</span></label>
                            <input type="text" name="reception_time" value="{{ old('reception_time') }}"
                                   placeholder="cth: 10:00 WIB"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('reception_time') border-red-400 @enderror">
                            @error('reception_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Lokasi --}}
                <div>
                    <h2 class="text-sm font-semibold text-stone-600 mb-2 pb-1 border-b border-stone-100">📍 Lokasi</h2>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Gedung / Tempat Acara <span class="text-red-500">*</span></label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               placeholder="cth: Ballroom Hotel Grand Sahid, Jakarta"
                               class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('location') border-red-400 @enderror">
                        @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm transition">
                    💒 Buat Undangan Pernikahan Gratis
                </button>
                <p class="text-center text-xs text-stone-400 mt-2">Tidak perlu daftar akun. Undangan langsung aktif.</p>
            </div>
        </form>
    </div>

    <div class="mt-4 text-center text-sm text-stone-500">
        Mau langsung pakai versi lengkap?
        <a href="{{ route('orders.create', ['template' => $template]) }}" class="text-amber-600 hover:underline font-medium">Pesan Basic/Premium →</a>
    </div>

</div>
</body>
</html>
