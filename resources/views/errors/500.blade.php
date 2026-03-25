<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Terjadi Kesalahan</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap');
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-stone-50 flex items-center justify-center p-6">
    <div class="max-w-md mx-auto text-center">
        <div class="text-8xl mb-6 select-none">⚙️</div>

        <h1 class="text-6xl font-bold text-stone-200 mb-2 leading-none">500</h1>
        <h2 class="text-2xl font-semibold text-stone-700 mb-3">Terjadi Kesalahan Server</h2>
        <p class="text-stone-500 text-base mb-8 leading-relaxed">
            Maaf, sistem kami mengalami kendala sementara. Tim kami sudah mendapat notifikasi. Silakan coba lagi dalam beberapa menit.
        </p>

        <div class="space-y-3">
            <a href="{{ url('/') }}"
               class="block w-full py-3 px-6 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold transition">
                ← Kembali ke Beranda
            </a>
            <button onclick="location.reload()"
                    class="block w-full py-3 px-6 rounded-xl border-2 border-stone-200 hover:bg-stone-100 text-stone-600 font-medium transition">
                🔄 Coba Lagi
            </button>
        </div>

        @php $adminWa = config('admin.whatsapp'); @endphp
        @if($adminWa)
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <p class="text-sm text-stone-600 mb-2">Butuh bantuan segera?</p>
            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $adminWa) }}"
               target="_blank"
               class="inline-flex items-center gap-2 text-green-700 font-semibold text-sm hover:underline">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.119.555 4.107 1.523 5.83L.057 24l6.305-1.652A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.655-.505-5.19-1.39l-.372-.22-3.862 1.013 1.032-3.763-.241-.387A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                </svg>
                Hubungi Admin via WhatsApp
            </a>
        </div>
        @endif

        <p class="mt-6 text-xs text-stone-400">
            Error 500 &mdash; {{ config('app.name', 'TretanInvite') }}
        </p>
    </div>
</body>
</html>
