<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coba Gratis — Undangan Ulang Tahun {{ $templateInfo['label'] }}</title>
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
        <div class="text-4xl mb-2">🎂</div>
        <h1 class="text-2xl font-bold text-stone-800">Coba Gratis — Undangan Ulang Tahun</h1>
        <p class="text-stone-500 text-sm mt-1">Template: <strong>{{ $templateInfo['label'] }}</strong></p>
    </div>

    {{-- Info banner --}}
    <div class="bg-pink-50 border border-pink-200 rounded-xl px-4 py-3 mb-6 text-sm text-pink-800">
        <div class="font-semibold mb-1">🎉 Yang kamu dapat saat coba:</div>
        <ul class="space-y-1 text-pink-700">
            <li>✅ Undangan langsung aktif setelah isi form</li>
            <li>✅ Bisa dibagikan ke tamu (maks <strong>3 tamu</strong>)</li>
            <li>✅ RSVP dari tamu aktif</li>
            <li>⏱ Aktif selama <strong>1 hari</strong></li>
            <li class="text-pink-400">❌ Galeri foto, musik & fitur premium lainnya</li>
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

                {{-- Data anak --}}
                <div>
                    <h2 class="text-sm font-semibold text-stone-600 mb-2 pb-1 border-b border-stone-100">🎂 Yang Berulang Tahun</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="bride_name" value="{{ old('bride_name') }}"
                                   placeholder="cth: Aisyah Azzahra"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('bride_name') border-red-400 @enderror">
                            @error('bride_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Usia (Tahun ke-)</label>
                            <input type="number" name="bride_age" value="{{ old('bride_age') }}"
                                   placeholder="cth: 7" min="1" max="150"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400">
                        </div>
                    </div>
                </div>

                {{-- Waktu --}}
                <div>
                    <h2 class="text-sm font-semibold text-stone-600 mb-2 pb-1 border-b border-stone-100">📅 Waktu Acara</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Tanggal Acara <span class="text-red-500">*</span></label>
                            <input type="date" name="event_date" value="{{ old('event_date') }}"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('event_date') border-red-400 @enderror">
                            @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Jam Acara <span class="text-red-500">*</span></label>
                            <input type="text" name="reception_time" value="{{ old('reception_time') }}"
                                   placeholder="cth: 14:00 WIB"
                                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('reception_time') border-red-400 @enderror">
                            @error('reception_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Lokasi --}}
                <div>
                    <h2 class="text-sm font-semibold text-stone-600 mb-2 pb-1 border-b border-stone-100">📍 Lokasi</h2>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Gedung / Tempat Pesta <span class="text-red-500">*</span></label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               placeholder="cth: Rumah Keluarga, Jl. Melati No. 5, Surabaya"
                               class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('location') border-red-400 @enderror">
                        @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm transition">
                    🎉 Buat Undangan Ulang Tahun Gratis
                </button>
                <p class="text-center text-xs text-stone-400 mt-2">Tidak perlu daftar akun. Undangan langsung aktif.</p>
            </div>
        </form>
    </div>

    <div class="mt-4 text-center text-sm text-stone-500">
        Mau langsung pakai versi lengkap?
        <a href="{{ route('orders.create', ['template' => $template]) }}" class="text-pink-600 hover:underline font-medium">Pesan Sekarang →</a>
    </div>

</div>
</body>
</html>
