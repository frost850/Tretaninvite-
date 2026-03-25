<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan Coba Siap! 🎉</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 p-4 md:p-8 flex items-center justify-center">
    <div class="max-w-md mx-auto w-full">

        <div class="bg-white rounded-2xl border border-stone-200 p-8 text-center shadow-sm">
            <div class="text-5xl mb-4">🎉</div>
            <h1 class="text-2xl font-bold text-stone-800 mb-2">Undangan Coba Siap!</h1>
            <p class="text-stone-500 text-sm mb-6">
                Undangan percobaan untuk
                <strong>{{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}</strong>
                sudah aktif dan bisa langsung dibagikan.
            </p>

            {{-- Masa berlaku --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6 text-sm text-amber-800 text-left">
                <div class="font-semibold mb-1">⏱ Masa Percobaan</div>
                <p>Aktif sampai: <strong>{{ $wedding->trial_expires_at->format('d M Y, H:i') }} WIB</strong></p>
                <p class="text-amber-600 text-xs mt-1">Setelah itu undangan otomatis nonaktif.</p>
            </div>

            {{-- Link undangan --}}
            <div class="mb-4">
                <p class="text-xs text-stone-500 font-medium mb-1">🔗 Link Undangan Kamu:</p>
                <div class="flex items-center gap-2">
                    <input type="text" readonly
                           id="invitation-url"
                           value="{{ url('/' . $wedding->slug) }}"
                           class="flex-1 px-3 py-2 text-xs font-mono bg-stone-50 border border-stone-300 rounded-lg text-stone-700 focus:outline-none">
                    <button onclick="copyUrl('invitation-url', 'btn-inv')" id="btn-inv"
                            class="px-3 py-2 bg-amber-600 text-white text-xs font-medium rounded-lg hover:bg-amber-700 transition whitespace-nowrap">
                        📋 Salin
                    </button>
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex flex-col gap-3 mb-6">
                <a href="{{ url('/' . $wedding->slug) }}" target="_blank"
                   class="block py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition">
                    👁 Lihat Undangan
                </a>
                <a href="{{ route('trial.guests.index', $wedding->slug) }}"
                   class="block py-3 rounded-xl border-2 border-stone-300 text-stone-700 font-semibold text-sm hover:bg-stone-50 transition">
                    👥 Tambah Tamu (maks 3)
                </a>
            </div>

            {{-- Upgrade CTA --}}
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-4 text-left">
                <div class="font-semibold text-purple-800 mb-2 text-sm">⬆️ Upgrade ke Berbayar</div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-white rounded-lg p-3 border border-purple-100 text-center">
                        <div class="font-bold text-stone-800 text-sm">Basic</div>
                        <div class="text-purple-600 font-bold text-lg">Rp 49k</div>
                        <div class="text-xs text-stone-500 mt-1">Maks 100 tamu<br>Permanent</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 border border-amber-200 text-center">
                        <div class="font-bold text-stone-800 text-sm">Premium ⭐</div>
                        <div class="text-amber-600 font-bold text-lg">Rp 99k</div>
                        <div class="text-xs text-stone-500 mt-1">Unlimited tamu<br>Galeri + Musik + Tracking</div>
                    </div>
                </div>
                <a href="{{ route('orders.create', ['template' => $wedding->template]) }}"
                   class="block text-center py-2.5 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-500 text-white font-semibold text-sm hover:from-purple-600 hover:to-indigo-600 transition">
                    Pesan Sekarang →
                </a>
            </div>
        </div>

        {{-- Simpan link reminder --}}
        <p class="text-center text-xs text-stone-400 mt-4">
            💡 Simpan link halaman ini agar bisa kembali kapan saja.
        </p>

    </div>

    <script>
    function copyUrl(inputId, btnId) {
        const val = document.getElementById(inputId).value;
        const btn = document.getElementById(btnId);
        const original = btn.textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(val);
        } else {
            const el = document.createElement('textarea');
            el.value = val; document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
        }
        btn.textContent = '✅ Tersalin';
        setTimeout(() => { btn.textContent = original; }, 2000);
    }
    </script>
</body>
</html>
