<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracking Undangan - {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body {
            background: #f8f9fa;
        }
        
        .card-modern {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }
    </style>
</head>
<body class="min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        @php $isBirthday = str_starts_with($wedding->template ?? '', 'birthday'); @endphp
        <div class="mb-8 text-center">
            <div class="inline-block px-4 py-2 rounded-xl text-white mb-4 shadow-md {{ $isBirthday ? 'bg-gradient-to-r from-pink-500 to-rose-500' : 'bg-gradient-to-r from-purple-500 to-indigo-500' }}">
                <span class="text-white text-sm font-bold">{{ $isBirthday ? '🎂 TRACKING ULANG TAHUN' : '📊 TRACKING UNDANGAN' }}</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                @if($isBirthday)
                    🎂 {{ $wedding->bride_name }}
                @elseif($wedding->groom_name)
                    {{ $wedding->bride_name }} & {{ $wedding->groom_name }}
                @else
                    {{ $wedding->bride_name }}
                @endif
            </h1>
            <p class="text-gray-600 text-sm">Pantau tamu yang sudah membuka undangan dan status RSVP</p>

            {{-- Tombol Portal VIP (hanya untuk paket VIP) --}}
            @if(!empty($customerVipToken))
            <div class="mt-4">
                <a href="{{ route('my.vip.dashboard', $customerVipToken) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md
                          bg-gradient-to-r from-yellow-500 to-amber-500 hover:opacity-90 transition">
                    👑 Buka Portal VIP
                </a>
                <p class="text-xs text-gray-400 mt-2">Moderasi guestbook & pantau statistik lengkap</p>
            </div>
            @endif
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="card-modern p-4 text-center border border-gray-200">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="text-xs text-gray-600 mt-1">Total Tamu</div>
            </div>
            
            <div class="card-modern p-4 text-center border border-gray-200">
                <div class="text-3xl font-bold text-green-600">{{ $stats['opened'] }}</div>
                <div class="text-xs text-gray-600 mt-1">Sudah Buka</div>
                @if($stats['total'] > 0)
                <div class="text-xs text-green-600 mt-1 font-medium">{{ round(($stats['opened']/$stats['total'])*100) }}%</div>
                @endif
            </div>
            
            <div class="card-modern p-4 text-center border border-gray-200">
                <div class="text-3xl font-bold text-gray-400">{{ $stats['not_opened'] }}</div>
                <div class="text-xs text-gray-600 mt-1">Belum Buka</div>
            </div>
            
            <div class="card-modern p-4 text-center border border-gray-200">
                <div class="text-3xl font-bold text-emerald-600">{{ $stats['rsvp_hadir'] }}</div>
                <div class="text-xs text-gray-600 mt-1">✅ Hadir</div>
            </div>
            
            <div class="card-modern p-4 text-center border border-gray-200">
                <div class="text-3xl font-bold text-red-500">{{ $stats['rsvp_tidak'] }}</div>
                <div class="text-xs text-gray-600 mt-1">❌ Tidak Hadir</div>
            </div>
            
            <div class="card-modern p-4 text-center border border-gray-200">
                <div class="text-3xl font-bold text-orange-500">{{ $stats['rsvp_belum'] }}</div>
                <div class="text-xs text-gray-600 mt-1">⏳ Belum RSVP</div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2 mb-6 flex-wrap" x-data="{ tab: 'all' }">
            <button @click="tab = 'all'" 
                    :class="tab === 'all' ? 'bg-gradient-to-r from-purple-500 to-indigo-500 text-white border-purple-400' : 'bg-white text-gray-700 border-gray-300 hover:border-purple-400'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border-2 transition shadow-sm">
                Semua ({{ $stats['total'] }})
            </button>
            <button @click="tab = 'opened'" 
                    :class="tab === 'opened' ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white border-green-400' : 'bg-white text-gray-700 border-gray-300 hover:border-green-400'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border-2 transition shadow-sm">
                📬 Sudah Buka ({{ $stats['opened'] }})
            </button>
            <button @click="tab = 'not_opened'" 
                    :class="tab === 'not_opened' ? 'bg-gradient-to-r from-gray-500 to-gray-600 text-white border-gray-400' : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border-2 transition shadow-sm">
                📪 Belum Buka ({{ $stats['not_opened'] }})
            </button>
            <button @click="tab = 'hadir'" 
                    :class="tab === 'hadir' ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white border-emerald-400' : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-400'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border-2 transition shadow-sm">
                ✅ Hadir ({{ $stats['rsvp_hadir'] }})
            </button>
            <button @click="tab = 'tidak'" 
                    :class="tab === 'tidak' ? 'bg-gradient-to-r from-red-500 to-red-600 text-white border-red-400' : 'bg-white text-gray-700 border-gray-300 hover:border-red-400'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border-2 transition shadow-sm">
                ❌ Tidak ({{ $stats['rsvp_tidak'] }})
            </button>

            {{-- Guest List --}}
            <div class="w-full mt-4">
                @if($guests->isEmpty())
                <div class="card-modern p-12 text-center border border-gray-200">
                    <div class="text-6xl mb-4">👥</div>
                    <p class="text-gray-600">Belum ada data tamu</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($guests as $guest)
                    <div class="card-modern p-4 border border-gray-200"
                         x-show="tab === 'all' || 
                                (tab === 'opened' && '{{ $guest->first_opened_at ? '1' : '' }}') || 
                                (tab === 'not_opened' && !'{{ $guest->first_opened_at ? '1' : '' }}') ||
                                (tab === 'hadir' && '{{ $guest->is_attending ? '1' : '0' }}' === '1' && '{{ $guest->replied_at ? '1' : '' }}') ||
                                (tab === 'tidak' && '{{ $guest->is_attending ? '1' : '0' }}' === '0' && '{{ $guest->replied_at ? '1' : '' }}')"
                         x-transition>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-gray-900">{{ $guest->guest_name }}</span>
                                    
                                    @if($guest->first_opened_at)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-300">
                                        📬 Dibuka
                                    </span>
                                    @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-300">
                                        📪 Belum dibuka
                                    </span>
                                    @endif

                                    @if($guest->replied_at)
                                        @if($guest->is_attending)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-300">
                                            ✅ Hadir
                                        </span>
                                        @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-300">
                                            ❌ Tidak Hadir
                                        </span>
                                        @endif
                                    @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-300">
                                        ⏳ Belum RSVP
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="text-xs text-gray-600 mt-1 flex flex-wrap gap-3">
                                    @if($guest->phone)
                                    <span>📱 {{ $guest->phone }}</span>
                                    @endif
                                    @if($guest->first_opened_at)
                                    <span>🕐 Dibuka: {{ $guest->first_opened_at->format('d M Y H:i') }}</span>
                                    @endif
                                    @if($guest->replied_at)
                                    <span>✍️ RSVP: {{ $guest->replied_at->format('d M Y H:i') }}</span>
                                    @endif
                                </div>

                                @if($guest->notes)
                                <div class="text-sm text-gray-700 mt-2 bg-gray-50 rounded-lg p-2 border border-gray-200">
                                    💬 "{{ $guest->notes }}"
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 text-center text-gray-600 text-sm">
            <p>Data diperbarui secara realtime</p>
            <p class="mt-2">
                @if($isBirthday)
                    🎂 {{ $wedding->bride_name }}
                @elseif($wedding->groom_name)
                    ❤️ {{ $wedding->bride_name }} & {{ $wedding->groom_name }}
                @else
                    {{ $wedding->bride_name }}
                @endif
            </p>
        </div>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
