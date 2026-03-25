<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masa Percobaan Habis</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-stone-100 flex items-center justify-center p-4">
    <div class="max-w-md mx-auto w-full">
        <div class="bg-white rounded-2xl border border-stone-200 p-8 text-center shadow-sm">
            <div class="text-5xl mb-4">⏰</div>
            <h1 class="text-2xl font-bold text-stone-800 mb-2">Masa Percobaan Habis</h1>
            <p class="text-stone-500 text-sm mb-6">
                Undangan percobaan untuk
                <strong>{{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}</strong>
                sudah nonaktif karena masa 3 hari percobaan telah berakhir.
            </p>

            <div class="bg-stone-50 border border-stone-200 rounded-xl p-4 mb-6 text-left text-sm text-stone-600">
                <div class="font-medium text-stone-700 mb-2">Data undangan kamu masih tersimpan:</div>
                <ul class="space-y-1">
                    <li>📅 Tanggal: {{ $wedding->event_date?->format('d M Y') }}</li>
                    <li>📍 Lokasi: {{ $wedding->location }}</li>
                    <li>🎨 Template: {{ $wedding->template }}</li>
                </ul>
            </div>

            <p class="text-stone-500 text-sm mb-4">Upgrade sekarang untuk mengaktifkan kembali undangan ini secara permanen:</p>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="border border-stone-200 rounded-xl p-4 text-center">
                    <div class="font-bold text-stone-800">Basic</div>
                    <div class="text-purple-600 font-bold text-xl mt-1">Rp 49.000</div>
                    <ul class="text-xs text-stone-500 mt-2 space-y-1 text-left">
                        <li>✅ Maks 100 tamu</li>
                        <li>✅ Permanent (tidak expired)</li>
                        <li>❌ Galeri foto</li>
                        <li>❌ Musik & Tracking</li>
                    </ul>
                </div>
                <div class="border-2 border-amber-400 rounded-xl p-4 text-center bg-amber-50">
                    <div class="font-bold text-stone-800">Premium ⭐</div>
                    <div class="text-amber-600 font-bold text-xl mt-1">Rp 99.000</div>
                    <ul class="text-xs text-stone-500 mt-2 space-y-1 text-left">
                        <li>✅ Unlimited tamu</li>
                        <li>✅ Permanent</li>
                        <li>✅ Galeri foto</li>
                        <li>✅ Musik & Live Tracking</li>
                    </ul>
                </div>
            </div>

            <a href="{{ route('orders.create', ['template' => $wedding->template]) }}"
               class="block py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold text-sm hover:from-amber-600 hover:to-amber-700 transition mb-3">
                Upgrade Sekarang →
            </a>
            <a href="{{ route('packages.index') }}"
               class="block py-2 text-stone-400 hover:text-stone-600 text-sm transition">
                ← Lihat semua template
            </a>
        </div>
    </div>
</body>
</html>
