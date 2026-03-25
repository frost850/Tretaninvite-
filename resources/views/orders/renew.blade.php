<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpanjang Masa Aktif Undangan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/css/tretaninvite.css">
    <script src="/js/tretaninvite.js"></script>
    <style>
        .form-box {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
            border-radius: 24px;
        }
        .day-btn {
            cursor: pointer; border-radius: 16px; border: 2px solid rgba(255,255,255,.08);
            padding: 16px; transition: all .2s;
            background: rgba(255,255,255,.03);
            display: flex; flex-direction: column; gap: 4px;
            text-align: center; position: relative; overflow: hidden;
        }
        .day-btn:hover { border-color: rgba(255,255,255,.2); background: rgba(255,255,255,.06); }
        .day-btn.selected { border-color: rgba(245,158,11,.7); background: rgba(245,158,11,.1); box-shadow: 0 0 24px rgba(245,158,11,.18); }
        .day-btn .badge-disc {
            position: absolute; top: 7px; right: 8px;
            font-size: .6rem; font-weight: 800; letter-spacing: .04em;
            padding: 2px 7px; border-radius: 99px;
            background: rgba(245,158,11,.25); color: #fbbf24; border: 1px solid rgba(245,158,11,.35);
        }
    </style>
</head>

@php
    use App\Models\Order;

    /* Preset hari beserta harga dan label diskon */
    $presets = [
        ['days' => 7,  'price' => 7000,   'disc' => null],
        ['days' => 10, 'price' => 9000,   'disc' => 'HEMAT 1k'],
        ['days' => 14, 'price' => 14000,  'disc' => null],
        ['days' => 30, 'price' => 27000,  'disc' => 'HEMAT 3k'],
    ];

    /* Nama undangan */
    $weddingName = $prefill['groom_name']
        ? ($prefill['bride_name'] . ' & ' . $prefill['groom_name'])
        : $prefill['bride_name'];

    /* Expiry undangan saat ini */
    $currentExpiry = $renewWedding ? $renewWedding->expires_at : null;

    /* Alpine.js config: pricing map & default */
    $pricingJson = collect($presets)->pluck('price', 'days')->toJson();
@endphp

<body class="text-white antialiased"
      x-data="{
          days: 10,
          custom: '',
          pricing: {{ $pricingJson }},
          rate: 1000,
          get price() {
              const d = this.activedays;
              return this.pricing[d] ?? d * this.rate;
          },
          get activedays() {
              return parseInt(this.custom) > 0 ? parseInt(this.custom) : this.days;
          },
          get priceFormatted() {
              return 'Rp\u00a0' + this.price.toLocaleString('id-ID');
          },
          selectPreset(d) { this.days = d; this.custom = ''; },
      }"
      x-init="TI.initPage({ starsId:'ti-stars', tiltSel:false })">

    <div id="ti-stars" style="position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;"></div>

    {{-- ════ NAVBAR ════ --}}
    <nav class="navbar px-6 py-4">
        <div class="max-w-xl mx-auto flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2 select-none">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-lg font-black shadow-lg">T</div>
                <span class="font-bold text-lg tracking-tight"><span class="text-amber-400">Tretan</span>Invite</span>
            </a>
        </div>
    </nav>

    {{-- ════ CONTENT ════ --}}
    <div class="page-bg pt-24 pb-20 px-4 sm:px-6">
        <div class="inner max-w-xl mx-auto">

            {{-- ── Header ── --}}
            <div class="mb-8 reveal text-center">
                <div class="text-5xl mb-4">🔄</div>
                <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-2">
                    Perpanjang Masa Aktif
                </h1>
                <p class="text-sm" style="color:rgba(255,255,255,.5)">
                    Pilih durasi perpanjangan untuk undangan
                    <span class="text-amber-300 font-semibold">{{ $weddingName }}</span>
                </p>
            </div>

            {{-- ── Flash errors ── --}}
            @if($errors->any())
            <div class="ti-alert-error mb-6 reveal">
                <ul class="space-y-1">
                    @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- ── Undangan sekarang ── --}}
            <div class="form-box p-5 mb-5 reveal">
                <p class="ti-label text-xs mb-4">📋 Undangan Anda</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs mb-0.5" style="color:rgba(255,255,255,.35)">Nama</div>
                        <div class="font-semibold text-white">{{ $weddingName }}</div>
                    </div>
                    <div>
                        <div class="text-xs mb-0.5" style="color:rgba(255,255,255,.35)">Template</div>
                        <div class="font-semibold text-white">{{ $templateInfo['icon'] ?? '' }} {{ $templateInfo['label'] }}</div>
                    </div>
                    @if($currentExpiry)
                    <div class="col-span-2">
                        <div class="text-xs mb-0.5" style="color:rgba(255,255,255,.35)">Masa aktif saat ini</div>
                        @if($currentExpiry->isPast())
                        <div class="font-semibold text-rose-400">Habis {{ $currentExpiry->translatedFormat('d M Y') }}</div>
                        @else
                        <div class="font-semibold text-green-400">Aktif s/d {{ $currentExpiry->translatedFormat('d M Y') }}</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── Form ── --}}
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="template"    value="{{ $template }}">
                <input type="hidden" name="renew_token" value="{{ $renewToken }}">
                {{-- renewal_days dikirim via Alpine x-model --}}

                {{-- ── Pilih durasi ── --}}
                <div class="form-box p-6 reveal">
                    <p class="ti-label text-xs mb-4">⏳ Pilih Durasi Perpanjangan</p>

                    {{-- Preset buttons --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                        @foreach($presets as $p)
                        <div class="day-btn"
                             :class="days === {{ $p['days'] }} && !custom ? 'selected' : ''"
                             @click="selectPreset({{ $p['days'] }})">
                            @if($p['disc'])
                            <span class="badge-disc">{{ $p['disc'] }}</span>
                            @endif
                            <div class="text-2xl font-black text-white">{{ $p['days'] }}</div>
                            <div class="text-xs font-semibold text-amber-300">hari</div>
                            <div class="text-xs font-bold text-white mt-1">
                                Rp {{ number_format($p['price'], 0, ',', '.') }}
                            </div>
                            @if($p['disc'])
                            <div class="text-xs line-through" style="color:rgba(255,255,255,.25)">
                                Rp {{ number_format($p['days'] * 1000, 0, ',', '.') }}
                            </div>
                            @else
                            <div class="text-xs" style="color:rgba(255,255,255,.25)">
                                {{ $p['days'] }} × Rp 1.000
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Custom input --}}
                    <div>
                        <label class="ti-label text-xs mb-2 block">Atau masukkan jumlah hari custom:</label>
                        <div class="flex items-center gap-3">
                            <input type="number" x-model="custom" min="1" max="365"
                                   placeholder="cth: 21"
                                   class="ti-input w-32"
                                   @input="if(custom) days=0">
                            <span class="text-sm" style="color:rgba(255,255,255,.45)">hari × Rp 1.000/hari</span>
                        </div>
                    </div>

                    {{-- Sembunyikan hidden field renewal_days agar Alpine bisa kontrol --}}
                    <input type="hidden" name="renewal_days" :value="activedays">
                </div>

                {{-- ── Ringkasan harga ── --}}
                <div class="reveal">
                    <div class="rounded-2xl px-5 py-4 flex items-center justify-between gap-4"
                         style="background:linear-gradient(135deg,rgba(245,158,11,.12),rgba(239,68,68,.08));border:1px solid rgba(245,158,11,.3);">
                        <div>
                            <div class="text-xs font-semibold text-amber-300 mb-0.5">Total Pembayaran</div>
                            <div class="text-3xl font-black text-white" x-text="priceFormatted"></div>
                            <div class="text-xs mt-1" style="color:rgba(255,255,255,.35)">
                                <span x-text="activedays"></span> hari × Rp 1.000/hari
                                <span x-show="pricing[activedays] && pricing[activedays] < activedays * 1000"
                                      class="text-amber-400 font-semibold ml-1">(sudah diskon)</span>
                            </div>
                        </div>
                        <div class="text-4xl">🎁</div>
                    </div>
                </div>

                {{-- ── Kontak pemesan ── --}}
                <div class="form-box p-6 reveal">
                    <p class="ti-label text-xs mb-4">📞 Kontak untuk Konfirmasi</p>
                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="ti-label">No WhatsApp <span class="text-rose-400">*</span></label>
                            <input type="tel" name="customer_phone"
                                   value="{{ old('customer_phone', $prefill['customer_phone'] ?? '') }}"
                                   placeholder="cth: 08123456789" class="ti-input" required>
                        </div>
                        <div>
                            <label class="ti-label">Email <span style="color:rgba(255,255,255,.3);font-weight:400">(opsional)</span></label>
                            <input type="email" name="customer_email"
                                   value="{{ old('customer_email', $prefill['customer_email'] ?? '') }}"
                                   placeholder="cth: nama@gmail.com" class="ti-input">
                        </div>
                    </div>
                    <div>
                        <label class="ti-label">Catatan <span style="color:rgba(255,255,255,.3);font-weight:400">(opsional)</span></label>
                        <textarea name="notes" rows="2" class="ti-input"
                                  placeholder="Permintaan tambahan...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- ── Submit ── --}}
                <div class="reveal">
                    <button type="submit"
                            class="btn-glow w-full py-4 rounded-2xl font-bold text-lg text-center">
                        Bayar Perpanjangan →
                    </button>
                    <p class="text-center text-xs mt-3" style="color:rgba(255,255,255,.3)">
                        Setelah membayar, admin akan memproses perpanjangan dan menghubungi Anda via WhatsApp.
                    </p>
                </div>
            </form>

        </div>
    </div>

    {{-- ════ FOOTER ════ --}}
    <footer style="background:#070514;border-top:1px solid rgba(255,255,255,.05);" class="py-8 px-6 text-center relative z-10">
        <div class="flex items-center justify-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-sm font-black">T</div>
            <span class="font-bold text-white/70 text-sm"><span class="text-amber-400">Tretan</span>Invite</span>
        </div>
        <p class="text-xs" style="color:rgba(255,255,255,.2)">© {{ date('Y') }} TretanInvite</p>
    </footer>
</body>
</html>
