<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Tamu — {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 p-4 md:p-8 font-[Plus_Jakarta_Sans,sans-serif]">
    <div class="max-w-lg mx-auto w-full">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trial.success', $wedding->slug) }}"
               class="text-sm text-amber-700 hover:text-amber-800 font-medium inline-flex items-center gap-1">
                ← Kembali
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-stone-800 truncate">
                    👥 Daftar Tamu
                </h1>
                <p class="text-xs text-stone-500">
                    {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}
                    <span class="text-stone-400">&middot;</span>
                    <span class="font-medium {{ $guests->count() >= $limit ? 'text-red-600' : 'text-amber-600' }}">
                        {{ $guests->count() }} / {{ $limit }} tamu
                    </span>
                </p>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-300 text-green-800 text-sm rounded-xl px-4 py-3 mb-4 font-medium">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-800 text-sm rounded-xl px-4 py-3 mb-4">
            @foreach($errors->all() as $e)<p>⚠️ {{ $e }}</p>@endforeach
        </div>
        @endif

        {{-- Info batas --}}
        @if($guests->count() < $limit)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5 text-sm text-amber-800">
            <p class="font-semibold mb-1">🧪 Paket Ujicoba — sisa <strong>{{ $limit - $guests->count() }} slot</strong></p>
            <p class="text-xs text-amber-700">Setiap tamu mendapatkan link undangan unik dengan namanya sendiri.</p>
        </div>
        @else
        <div class="bg-red-50 border border-red-300 rounded-xl p-4 mb-5 text-sm text-red-800">
            <p class="font-semibold mb-1">🚫 Batas {{ $limit }} tamu sudah tercapai</p>
            <p class="text-xs text-red-700 mb-2">Upgrade ke paket berbayar untuk menambah lebih banyak tamu.</p>
            <a href="{{ route('orders.create', ['template' => $wedding->template]) }}"
               class="inline-block px-4 py-2 rounded-lg bg-purple-600 text-white text-xs font-bold hover:bg-purple-700 transition">
               ⬆️ Upgrade Sekarang
            </a>
        </div>
        @endif

        {{-- Form tambah tamu --}}
        @if($guests->count() < $limit)
        <div class="bg-white rounded-2xl border border-stone-200 p-5 mb-5 shadow-sm">
            <h2 class="font-bold text-stone-700 text-sm mb-3">➕ Tambah Tamu</h2>
            <form action="{{ route('trial.guests.store', $wedding->slug) }}" method="post">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                           placeholder="Nama tamu…"
                           class="flex-1 px-3 py-2.5 text-sm rounded-xl border border-stone-300 focus:border-amber-400 focus:ring-2 focus:ring-amber-200 focus:outline-none transition"
                           required maxlength="255">
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition shadow-sm whitespace-nowrap">
                        Tambah
                    </button>
                </div>
                <p class="text-xs text-stone-400 mt-1.5">Nama ini akan muncul langsung di undangan mereka.</p>
            </form>
        </div>
        @endif

        {{-- Daftar tamu --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            @if($guests->isEmpty())
            <div class="p-10 text-center text-stone-400">
                <div class="text-4xl mb-3">👥</div>
                <p class="text-sm">Belum ada tamu. Tambahkan nama tamu di atas.</p>
            </div>
            @else
            <div class="px-5 py-3 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 tracking-wider">NAMA TAMU</span>
                <span class="text-xs font-bold text-stone-500 tracking-wider">LINK UNDANGAN</span>
            </div>
            @foreach($guests as $g)
            <div class="flex items-center gap-3 px-5 py-3 border-b border-stone-100 last:border-0 hover:bg-stone-50 transition">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-stone-800 text-sm truncate">{{ $g->guest_name }}</div>
                    <div class="text-xs text-stone-400 mt-0.5 font-mono truncate">
                        {{ url('/' . $wedding->slug) }}?to={{ \Illuminate\Support\Str::slug($g->guest_name) }}
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button onclick="copyLink('{{ url('/' . $wedding->slug) }}?to={{ \Illuminate\Support\Str::slug($g->guest_name) }}', this)"
                            class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-amber-100 text-stone-500 hover:text-amber-700 text-xs font-medium transition">
                        📋
                    </button>
                    @if($canManage)
                    <form action="{{ route('trial.guests.destroy', [$wedding->slug, $g->id]) }}" method="post"
                          data-name="{{ e($g->guest_name) }}"
                          onsubmit="return confirm('Hapus tamu \'' + this.dataset.name + '\'?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-red-100 text-stone-400 hover:text-red-600 text-xs font-medium transition">
                            🗑
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Link balik ke undangan --}}
        <div class="mt-6 text-center space-y-2">
            <a href="{{ url('/' . $wedding->slug) }}" target="_blank"
               class="inline-block text-sm font-medium text-amber-700 hover:text-amber-800 underline">
                👁 Lihat Undangan
            </a>
            <p class="text-xs text-stone-400">Simpan link halaman ini agar bisa kembali kapan saja.</p>
        </div>
    </div>

    <script>
    function copyLink(url, btn) {
        const original = btn.textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url);
        } else {
            const el = document.createElement('textarea');
            el.value = url; document.body.appendChild(el); el.select();
            document.execCommand('copy'); document.body.removeChild(el);
        }
        btn.textContent = '✅';
        setTimeout(() => { btn.textContent = original; }, 1800);
    }
    </script>
</body>
</html>
