@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Nunito:wght@300;400;600;700;800;900&family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/greeting-birthday.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- ══ META CSRF untuk AJAX ══ --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ══ DATA JS ══ --}}
<script>
window.GC_SLUG  = '{{ $wedding->slug }}';
window.GC_DEMO  = {{ !empty($isPreview) ? 'true' : 'false' }};
window.GC_NAME  = '{{ addslashes($wedding->bride_name) }}';
</script>

{{-- ══ CANVAS: Three.js hero bg ══ --}}
<canvas id="gc-three-canvas"></canvas>

{{-- ══ CANVAS: Confetti ══ --}}
<canvas id="gc-canvas"></canvas>

{{-- ══ MUSIK LATAR ══ --}}
@if($wedding->music_url)
<audio id="gc-audio" loop preload="none">
    <source src="{{ $wedding->music_url }}" type="audio/mpeg">
</audio>
@endif
<button id="gc-music-btn" onclick="gcToggleMusic()" title="Putar/Jeda Musik" style="{{ $wedding->music_url ? '' : 'display:none' }}">🎵</button>

{{-- ══════════════════════════════════════════════
     COVER KARTU — klik untuk buka
══════════════════════════════════════════════ --}}
<div id="gc-cover" onclick="gcOpenCard()">

    <div id="cover-photo-bg"></div>

    {{-- Partikel sparkle di cover --}}
    <div class="gc-cover-particles">
        @for($i = 0; $i < 12; $i++)
        <div class="gc-cp" style="--cx:{{ rand(5,95) }}%;--cy:{{ rand(5,90) }}%;--cs:{{ rand(8,20) }}px;--cd:{{ $i * 0.18 }}s"></div>
        @endfor
    </div>

    {{-- Card cover --}}
    <div class="gc-cover-card" id="gc-cover-card">
        <div class="gc-cc-ribbon">🎀</div>

        <div class="gc-cc-emoji-ring">
            <span class="gcr-1">🎂</span>
            <span class="gcr-2">🎈</span>
            <span class="gcr-3">⭐</span>
            <span class="gcr-4">🎉</span>
        </div>

        @if($wedding->bride_age)
        <div class="gc-cc-age-badge">
            <span class="gc-age-big">{{ $wedding->bride_age }}</span>
            <span class="gc-age-unit">TAHUN</span>
        </div>
        @endif

        <div class="gc-cc-title">Selamat Ulang Tahun</div>
        <div class="gc-cc-name">{{ $wedding->bride_name }}</div>

        @if($wedding->groom_name)
        <div class="gc-cc-from">dari {{ $wedding->groom_name }} 💌</div>
        @endif

        <button class="gc-open-btn" type="button">
            <span class="gc-btn-envelope">💌</span>
            <span>Buka Kartu Ucapan</span>
            <span class="gc-btn-arrow">→</span>
        </button>

        <div class="gc-cc-hint">Ketuk untuk membuka</div>
    </div>

    {{-- Balon melayang --}}
    <div class="gc-cover-balloons">
        <span class="gc-cb" style="--cl:15%;--cd:0s">🎈</span>
        <span class="gc-cb" style="--cl:30%;--cd:0.6s">🎊</span>
        <span class="gc-cb" style="--cl:60%;--cd:1.1s">🎈</span>
        <span class="gc-cb" style="--cl:78%;--cd:0.3s">🌟</span>
        <span class="gc-cb" style="--cl:50%;--cd:0.9s">🎉</span>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     ISI KARTU UTAMA
══════════════════════════════════════════════ --}}
<div id="gc-main">

    {{-- ─────────────────────────────────────────────
         HERO — Three.js background + parallax layers
    ───────────────────────────────────────────── --}}
    <section id="gc-hero" class="gc-hero">

        {{-- Balon float --}}
        <div id="gc-balloons-hero" class="gc-balloons-container"></div>

        {{-- Layer parallax 1: dekorasi jauh --}}
        <div class="gc-px-layer gc-px-far" id="gc-px-far">
            <span class="gc-px-deco" style="--px:8%;--py:15%">🌟</span>
            <span class="gc-px-deco" style="--px:90%;--py:12%">✨</span>
            <span class="gc-px-deco" style="--px:5%;--py:70%">💫</span>
            <span class="gc-px-deco" style="--px:93%;--py:65%">🌟</span>
            <span class="gc-px-deco" style="--px:45%;--py:5%">✨</span>
        </div>

        {{-- Layer parallax 2: dekorasi tengah --}}
        <div class="gc-px-layer gc-px-mid" id="gc-px-mid">
            <span class="gc-px-deco" style="--px:20%;--py:25%">🎊</span>
            <span class="gc-px-deco" style="--px:75%;--py:30%">🎉</span>
            <span class="gc-px-deco" style="--px:15%;--py:55%">🎈</span>
            <span class="gc-px-deco" style="--px:80%;--py:60%">💖</span>
        </div>

        {{-- Konten utama hero --}}
        <div class="gc-hero-content" id="gc-hero-content">
            <div class="gc-salutation" id="gc-salutation">🎉 Selamat Ulang Tahun 🎉</div>

            <h1 class="gc-hero-name" id="gc-hero-name">{{ $wedding->bride_name }}</h1>

            @if($wedding->bride_age)
            <div class="gc-hero-age-ring" id="gc-hero-age">
                <div class="gc-age-orbit">
                    <div class="gc-age-num-big">{{ $wedding->bride_age }}</div>
                    <div class="gc-age-label-sm">Tahun</div>
                </div>
            </div>
            @endif

            @if($wedding->opening_text)
            <div class="gc-hero-message" id="gc-hero-msg">
                <div class="gc-hm-deco">✦ ✦ ✦</div>
                <p class="gc-hm-text">{{ Str::limit($wedding->opening_text, 150) }}</p>
                <div class="gc-hm-deco">✦ ✦ ✦</div>
            </div>
            @endif

            @if($wedding->groom_name)
            <div class="gc-hero-from">
                <span class="gc-hf-dash">—</span>
                <span class="gc-hf-name">{{ $wedding->groom_name }}</span>
            </div>
            @endif

            {{-- Scroll hint --}}
            <div class="gc-scroll-hint">
                <div class="gc-scroll-mouse"><div class="gc-scroll-dot"></div></div>
                <span>Gulir ke bawah</span>
            </div>
        </div>
    </section>

    {{-- ─────────────────────────────────────────────
         REACTIONS — Emoji reaction AJAX
    ───────────────────────────────────────────── --}}
    <section class="gc-reactions-section gc-reveal" id="gc-reactions">
        <div class="gc-section-inner">
            <div class="gc-react-title">Beri Reaksimu 💝</div>
            <div class="gc-react-row">
                @foreach(['❤️' => 'Cinta', '🎂' => 'Kue', '🎉' => 'Pesta', '🎊' => 'Konfeti', '🥳' => 'Hore'] as $emoji => $label)
                <button class="gc-react-btn" data-emoji="{{ $emoji }}" type="button" title="{{ $label }}">
                    <span class="gc-react-emoji">{{ $emoji }}</span>
                    <span class="gc-react-count">0</span>
                </button>
                @endforeach
            </div>
            <div class="gc-react-note" id="gc-react-note"></div>
        </div>
    </section>

    {{-- ─────────────────────────────────────────────
         MESSAGE — Kartu pesan lengkap
    ───────────────────────────────────────────── --}}
    @if($wedding->opening_text)
    <section class="gc-message-section gc-reveal">
        <div class="gc-section-inner gc-msg-inner">
            <div class="gc-msg-title">✍️ Pesan Ucapan</div>
            <div class="gc-msg-card">
                <div class="gc-msg-quote">"</div>
                <div class="gc-msg-body">{{ $wedding->opening_text }}</div>
                <div class="gc-msg-quote gc-msg-quote-close">"</div>
                @if($wedding->groom_name)
                <div class="gc-msg-signature">
                    <span class="gc-msg-dash">—</span> {{ $wedding->groom_name }}
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ─────────────────────────────────────────────
         VIRTUAL CARD — 3D tilt card
    ───────────────────────────────────────────── --}}
    <section class="gc-vc-section gc-reveal">
        <div class="gc-section-inner">
            <div class="gc-vc-title">💌 Kartu Virtual</div>
            <div class="gc-vc-wrap">
                <div class="gc-virtual-card" id="gc-virtual-card">
                    <div class="gc-vc-glow"></div>
                    <div class="gc-vc-inner">
                        <div class="gc-vc-header-row">
                            <span class="gc-vc-cake">🎂</span>
                            @if($wedding->bride_age)
                            <div class="gc-vc-age-pill">{{ $wedding->bride_age }}</div>
                            @endif
                        </div>
                        <div class="gc-vc-subtitle">HAPPY BIRTHDAY</div>
                        <div class="gc-vc-name-big">{{ $wedding->bride_name }}</div>
                        @if($wedding->opening_text)
                        <blockquote class="gc-vc-quote">
                            {{ Str::limit($wedding->opening_text, 100) }}
                        </blockquote>
                        @endif
                        @if($wedding->groom_name)
                        <div class="gc-vc-from">— {{ $wedding->groom_name }}</div>
                        @endif
                        <div class="gc-vc-deco-row">🎁 🎉 🎊 🎈 🌟</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─────────────────────────────────────────────
         GALLERY — Interactive photo grid + lightbox
         (lazy loaded via AJAX or demo photos if preview)
    ───────────────────────────────────────────── --}}
    <section class="gc-gallery-section" id="gc-gallery-section">
        <div class="gc-section-inner">
            <div class="gc-gal-title gc-reveal">📸 Galeri Foto</div>
            <div class="gc-gallery-grid" id="gc-gallery-grid">
                @if(!empty($isPreview))
                @for($gi = 0; $gi < 6; $gi++)
                <div class="gc-gallery-item gc-reveal">
                    <div class="gc-gal-placeholder">
                        <span>{{ ['🎂','🎉','🎊','🎈','🌟','💖'][$gi] }}</span>
                    </div>
                    <div class="gc-gallery-overlay"><span class="gc-gal-zoom">🔍</span></div>
                </div>
                @endfor
                @else
                <div class="gc-gal-loading" id="gc-gal-loading">
                    <div class="gc-spinner"></div>
                    <span>Memuat foto...</span>
                </div>
                @endif
            </div>
            <div class="gc-gal-empty" id="gc-gal-empty" style="display:none">
                <span>📷</span><p>Belum ada foto galeri</p>
            </div>
        </div>
    </section>

    {{-- ─────────────────────────────────────────────
         FOOTER
    ───────────────────────────────────────────── --}}
    <footer class="gc-footer">
        <div class="gc-footer-inner">
            <div class="gc-ft-emoji">🎂</div>
            <div class="gc-ft-wish">
                Semoga hari-harimu selalu dipenuhi<br>
                senyum, tawa, dan kebahagiaan.
            </div>
            <div class="gc-ft-deco">
                <span>🎉</span><span>🌟</span><span>✨</span><span>💖</span>
                <span>✨</span><span>🌟</span><span>🎉</span>
            </div>
            @if($wedding->groom_name)
            <div class="gc-ft-from">Dengan cinta, {{ $wedding->groom_name }}</div>
            @endif
        </div>
    </footer>

</div>{{-- /#gc-main --}}

{{-- ══════════════════════════════════════════════
     LIGHTBOX MODAL
══════════════════════════════════════════════ --}}
<div id="gc-lightbox" class="gc-lightbox" style="display:none" onclick="gcLbBgClick(event)">
    <button class="gc-lb-close" onclick="gcLbClose()" type="button">✕</button>
    <button class="gc-lb-prev" onclick="gcLbPrev()" type="button">‹</button>
    <button class="gc-lb-next" onclick="gcLbNext()" type="button">›</button>
    <div class="gc-lb-img-wrap">
        <img id="gc-lb-img" src="" alt="Foto">
    </div>
    <div class="gc-lb-counter" id="gc-lb-counter"></div>
</div>


@push('scripts')
<script>
@php
  $_bgList = [];
  if (!empty($isPreview) && !empty($demoPhotos)) {
    $_bgList = array_values($demoPhotos);
  } elseif ($wedding->has_gallery && $wedding->gallery->count() > 0) {
    $_bgList = $wedding->gallery->map(fn($g) => asset('storage/' . $g->path))->toArray();
  }
@endphp
window.bgPhotos = {!! json_encode($_bgList) !!};
</script>
<script src="{{ asset('js/photo-bg-slideshow.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script src="{{ asset('js/greeting/gc-parallax.js') }}"></script>
<script src="{{ asset('js/greeting/gc-threejs.js') }}"></script>
<script src="{{ asset('js/greeting/gc-ajax.js') }}"></script>
<script src="{{ asset('js/greeting/gc-core.js') }}"></script>
@endpush

@endsection
