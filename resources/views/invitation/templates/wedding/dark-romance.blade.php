@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=playfair-display:400,400i,700,700i|raleway:300,400,500,600&display=swap" rel="stylesheet" />
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dark-romance.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- Canvas ember particles (background) --}}
<canvas id="dr-canvas" aria-hidden="true"></canvas>

{{-- ══════════════════════════════════════
     PETAL PARTICLES
══════════════════════════════════════ --}}
<div id="dr-petals" aria-hidden="true"></div>

{{-- ══════════════════════════════════════
     CURTAIN / ENVELOPE INTRO
══════════════════════════════════════ --}}
<div class="dr-curtain" id="dr-curtain">
    <div class="dr-curtain-inner">
        <div id="cover-photo-bg"></div>

        {{-- Rotating sigil --}}
        <div class="dr-curtain-sigil">✦</div>

        {{-- Bismillah --}}
        <p class="dr-curtain-bismillah">Bismillahirrahmanirrahim</p>

        {{-- Guest greeting --}}
        @if($guest)
        <div style="margin-bottom:20px;">
            <span class="dr-curtain-kepada">Kepada Yang Terhormat</span>
            <span class="dr-curtain-guest">{{ $guest->guest_name }}</span>
        </div>
        @endif

        {{-- Divider --}}
        <div class="dr-curtain-divider"><span>◆</span></div>

        {{-- Label --}}
        <span class="dr-curtain-label">Undangan Pernikahan</span>

        {{-- Names --}}
        <div class="dr-curtain-names">
            <h1>{{ $wedding->bride_name }}</h1>
            <span class="dr-curtain-amp">&amp;</span>
            <h1>{{ $wedding->groom_name }}</h1>
        </div>

        {{-- Date --}}
        @if($wedding->event_date)
        <div class="dr-curtain-date">
            {{ $wedding->event_date->locale('id')->translatedFormat('d F Y') }}
        </div>
        @endif

        <button class="dr-open-btn" id="dr-open-btn">
            Buka Undangan
        </button>
    </div>
</div>


{{-- ══════════════════════════════════════
     MAIN CONTENT (hidden until curtain opens)
══════════════════════════════════════ --}}
<div id="dr-main" style="opacity:0; transition: opacity .7s ease;">

    {{-- Sticky nav --}}
    <nav class="dr-nav" aria-label="Navigasi undangan">
        <a href="#mempelai">Mempelai</a>
        <a href="#acara">Acara</a>
        @if($wedding->has_gallery || !empty($isPreview))
        <a href="#galeri">Galeri</a>
        @endif
        <a href="#rsvp">RSVP</a>
    </nav>


    {{-- ══════════════════════════════════════
         HERO
    ══════════════════════════════════════ --}}
    <section class="dr-hero" id="top">

        <p class="dr-hero-bismillah">Bismillahirrahmanirrahim</p>

        {{-- Guest greeting --}}
        @if($guest)
        <div class="dr-hero-kepada">
            <span class="dr-hero-kepada-label">Kepada Yang Terhormat</span>
            <span class="dr-hero-kepada-name">{{ $guest->guest_name }}</span>
        </div>
        @endif

        {{-- Eyebrow --}}
        <div class="dr-hero-eyebrow">
            <span class="dr-hero-tag">Undangan Pernikahan</span>
        </div>

        {{-- Names --}}
        <div class="dr-hero-names">
            <h1>{{ $wedding->bride_name }}</h1>
            <span class="dr-hero-amp">&amp;</span>
            <h1>{{ $wedding->groom_name }}</h1>
        </div>

        {{-- Date + Location badge --}}
        @if($wedding->event_date)
        <div class="dr-hero-date-wrap">
            <div class="dr-hero-date">
                <span class="dr-hero-date-dot"></span>
                {{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}
                <span class="dr-hero-date-dot"></span>
            </div>
            @if($wedding->location)
            <p class="dr-hero-location">{{ $wedding->location }}</p>
            @endif
        </div>
        @endif

        {{-- CTA scroll --}}
        <a href="#mempelai" class="dr-hero-cta">
            <span class="dr-hero-cta-text">Gulir ke bawah</span>
            <span class="dr-hero-arrow" aria-hidden="true"></span>
        </a>
    </section>


    {{-- ══════════════════════════════════════
         MEMPELAI
    ══════════════════════════════════════ --}}
    <section class="dr-mempelai-section" id="mempelai" style="max-width:100%; padding:80px 24px;">
        <div style="max-width:680px; margin:0 auto; text-align:center;">
            <div class="dr-divider reveal">
                <div class="dr-divider-line"></div>
                <span class="dr-divider-icon">✦</span>
                <div class="dr-divider-line"></div>
            </div>
            <span class="dr-tag reveal">Yang Berbahagia</span>
            <h2 class="dr-title reveal">Dua Hati Bersatu</h2>
            <p class="dr-sub reveal">
                Dengan penuh syukur dan kebahagiaan, kami mengumumkan pernikahan kami
                kepada seluruh sanak saudara dan sahabat tercinta.
            </p>

            {{-- Couple grid --}}
            <div class="dr-couple-grid" style="margin-top:52px;">
                {{-- Mempelai Wanita --}}
                <div class="reveal">
                    <div class="dr-photo-frame">
                        <div class="dr-photo-inner">
                            @if($wedding->bride_photo ?? false)
                            <img src="{{ asset('storage/'.$wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
                            @else
                            🌹
                            @endif
                        </div>
                    </div>
                    <div class="dr-person-name">{{ $wedding->bride_name }}</div>
                    @if($wedding->bride_fullname ?? false)
                    <div class="dr-person-fullname">{{ $wedding->bride_fullname }}</div>
                    @endif
                    @if(($wedding->bride_parent ?? false))
                    <p class="dr-person-parents">{{ $wedding->bride_parent }}</p>
                    @elseif(($wedding->bride_father ?? false) || ($wedding->bride_mother ?? false))
                    <p class="dr-person-parents">
                        Putri dari<br>
                        <span style="color:var(--dr-blush);">
                            {{ $wedding->bride_father ?? '' }}{{ ($wedding->bride_father && $wedding->bride_mother) ? ' &amp; ' : '' }}{{ $wedding->bride_mother ?? '' }}
                        </span>
                    </p>
                    @endif
                </div>

                {{-- Separator --}}
                <div class="dr-couple-sep reveal">&amp;</div>

                {{-- Mempelai Pria --}}
                <div class="reveal">
                    <div class="dr-photo-frame">
                        <div class="dr-photo-inner">
                            @if($wedding->groom_photo ?? false)
                            <img src="{{ asset('storage/'.$wedding->groom_photo) }}" alt="{{ $wedding->groom_name }}">
                            @else
                            🕯️
                            @endif
                        </div>
                    </div>
                    <div class="dr-person-name">{{ $wedding->groom_name }}</div>
                    @if($wedding->groom_fullname ?? false)
                    <div class="dr-person-fullname">{{ $wedding->groom_fullname }}</div>
                    @endif
                    @if(($wedding->groom_parent ?? false))
                    <p class="dr-person-parents">{{ $wedding->groom_parent }}</p>
                    @elseif(($wedding->groom_father ?? false) || ($wedding->groom_mother ?? false))
                    <p class="dr-person-parents">
                        Putra dari<br>
                        <span style="color:var(--dr-blush);">
                            {{ $wedding->groom_father ?? '' }}{{ ($wedding->groom_father && $wedding->groom_mother) ? ' &amp; ' : '' }}{{ $wedding->groom_mother ?? '' }}
                        </span>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════════
         FOTO COUPLE
    ══════════════════════════════════════ --}}
    @if($wedding->couple_photo ?? false)
    <div class="dr-couple-photo-section">
        <span class="dr-tag reveal">Momen Berdua</span>
        <h2 class="dr-title reveal" style="margin-bottom:24px;">A Moment Together</h2>
        <div class="dr-couple-photo-wrap reveal">
            <img src="{{ asset('storage/'.$wedding->couple_photo) }}"
                 alt="{{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}">
        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════
         QUOTE / AYAT
    ══════════════════════════════════════ --}}
    <div class="dr-quote-section" style="text-align:center;">
        <div style="max-width:620px; margin:0 auto;">
            <span class="dr-quote-mark reveal">"</span>
            <p class="dr-quote-text reveal">
                {{ $wedding->customText('quote_text', 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya, dan dijadikan-Nya di antaramu rasa kasih dan sayang.') }}
            </p>
            <span class="dr-quote-source reveal">{{ $wedding->customText('quote_source', 'QS. Ar-Rum: 21') }}</span>
        </div>
    </div>


    {{-- ══════════════════════════════════════
         ACARA
    ══════════════════════════════════════ --}}
    <section id="acara">
        <div class="dr-divider reveal">
            <div class="dr-divider-line"></div>
            <span class="dr-divider-icon">✦</span>
            <div class="dr-divider-line"></div>
        </div>
        <span class="dr-tag reveal">Rangkaian Acara</span>
        <h2 class="dr-title reveal">Agenda Hari Istimewa</h2>

        <div class="dr-acara-grid reveal">
            {{-- Akad Nikah --}}
            @if(($wedding->akad_date ?? $wedding->event_date) ?? false)
            <div class="dr-acara-card">
                <span class="dr-acara-icon">☽</span>
                <div class="dr-acara-type">Akad Nikah</div>
                <div class="dr-acara-name">Ijab Qabul</div>
                <div class="dr-acara-info">
                    <strong>{{ ($wedding->akad_date ?? $wedding->event_date)->locale('id')->translatedFormat('l, d F Y') }}</strong><br>
                    {{ $wedding->akad_time ?? '08.00 – 10.00' }} WIB<br><br>
                    {{ $wedding->akad_location ?? ($wedding->location ?? 'Lokasi Akad Nikah') }}
                </div>
            </div>
            @endif

            {{-- Resepsi --}}
            <div class="dr-acara-card">
                <span class="dr-acara-icon" style="font-size:1.3rem; letter-spacing:.1em;">✦</span>
                <div class="dr-acara-type">Resepsi</div>
                <div class="dr-acara-name">{{ $wedding->customText('event_name', 'Walimatul Ursy') }}</div>
                <div class="dr-acara-info">
                    <strong>{{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}</strong><br>
                    {{ $wedding->reception_time ?? '11.00 – 14.00' }} WIB<br><br>
                    {{ $wedding->reception_location ?? ($wedding->location ?? 'Lokasi Resepsi') }}
                </div>
            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════════
         COUNTDOWN
    ══════════════════════════════════════ --}}
    <section class="dr-countdown-section">
        <span class="dr-tag reveal">Menuju Hari Bahagia</span>
        <h2 class="dr-title reveal">Hitung Mundur</h2>

        <div class="dr-countdown-grid reveal" id="dr-countdown-grid">
            <div class="dr-cd-item">
                <span class="dr-cd-num" id="dr-cd-hari">00</span>
                <span class="dr-cd-lbl">Hari</span>
            </div>
            <div class="dr-cd-item">
                <span class="dr-cd-num" id="dr-cd-jam">00</span>
                <span class="dr-cd-lbl">Jam</span>
            </div>
            <div class="dr-cd-item">
                <span class="dr-cd-num" id="dr-cd-menit">00</span>
                <span class="dr-cd-lbl">Menit</span>
            </div>
            <div class="dr-cd-item">
                <span class="dr-cd-num" id="dr-cd-detik">00</span>
                <span class="dr-cd-lbl">Detik</span>
            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════════
         GALLERY
    ══════════════════════════════════════ --}}
    @php
        $gallPhotos = [];
        if (!empty($isPreview) && !empty($demoPhotos)) {
            $gallPhotos = $demoPhotos;
        } elseif (($wedding->galleries ?? collect())->count() > 0) {
            $gallPhotos = $wedding->galleries->map(fn($g) => asset('storage/'.$g->path))->toArray();
        }
    @endphp
    @if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)
    <section id="galeri">
        <div class="dr-divider reveal">
            <div class="dr-divider-line"></div>
            <span class="dr-divider-icon">✦</span>
            <div class="dr-divider-line"></div>
        </div>
        <span class="dr-tag reveal">Momen Berharga</span>
        <h2 class="dr-title reveal">Galeri Foto</h2>
        <p class="dr-sub reveal" style="margin-bottom:0;">Setiap bingkai menyimpan cerita cinta yang tak terlupakan.</p>
        @include('invitation.partials.gallery-innovations', [
            'galleryPhotos'  => $gallPhotos,
            'galleryMode'    => 'polaroid',
            'galleryBgColor' => '#0c0508',
            'galleryAccent'  => 'rgba(139,20,40,0.3)',
        ])
    </section>
    @endif


    {{-- ══════════════════════════════════════
         LOKASI
    ══════════════════════════════════════ --}}
    <section id="lokasi">
        <div class="dr-divider reveal">
            <div class="dr-divider-line"></div>
            <span class="dr-divider-icon">✦</span>
            <div class="dr-divider-line"></div>
        </div>
        <span class="dr-tag reveal">Temukan Kami</span>
        <h2 class="dr-title reveal">Lokasi Acara</h2>
        @if($wedding->location)
        <p class="dr-sub reveal">{{ $wedding->location }}</p>
        @endif

        <div class="dr-map-frame reveal">
            @if($wedding->map_embed ?? false)
                {!! $wedding->map_embed !!}
            @else
            <div class="dr-map-placeholder">
                <span class="icon">📍</span>
                <span class="text">{{ $wedding->location ?? 'Peta Lokasi' }}</span>
            </div>
            @endif
        </div>

        @if($wedding->map_link ?? false)
        <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="dr-btn-outline reveal">
            Buka di Google Maps
        </a>
        @endif
    </section>


    {{-- ══════════════════════════════════════
         AMPLOP DIGITAL
    ══════════════════════════════════════ --}}
    @if(($wedding->bank_accounts ?? collect())->count() > 0 || ($wedding->bride_bank ?? false) || ($wedding->groom_bank ?? false))
    <section id="amplop">
        <div class="dr-divider reveal">
            <div class="dr-divider-line"></div>
            <span class="dr-divider-icon">✦</span>
            <div class="dr-divider-line"></div>
        </div>
        <span class="dr-tag reveal">Hadiah &amp; Doa</span>
        <h2 class="dr-title reveal">Amplop Digital</h2>
        <p class="dr-sub reveal">
            Kehadiran dan doa restu Anda adalah hadiah terindah bagi kami.
            Namun jika berkenan, silakan melalui:
        </p>

        <div class="dr-gift-grid reveal">
            @if($wedding->bank_accounts ?? false)
                @foreach($wedding->bank_accounts as $acc)
                <div class="dr-gift-card">
                    <span class="dr-gift-icon">💳</span>
                    <div class="dr-gift-bank">{{ $acc->bank_name }}</div>
                    <div class="dr-gift-norek">{{ $acc->account_number }}</div>
                    <div class="dr-gift-atas">a/n {{ $acc->account_name }}</div>
                    <button class="dr-copy-btn" onclick="drCopyText('{{ $acc->account_number }}', this)">Salin Nomor</button>
                </div>
                @endforeach
            @else
                @if($wedding->bride_bank ?? false)
                <div class="dr-gift-card">
                    <span class="dr-gift-icon">💳</span>
                    <div class="dr-gift-bank">{{ $wedding->bride_bank }}</div>
                    <div class="dr-gift-norek">{{ $wedding->bride_norek }}</div>
                    <div class="dr-gift-atas">a/n {{ $wedding->bride_name }}</div>
                    <button class="dr-copy-btn" onclick="drCopyText('{{ $wedding->bride_norek }}', this)">Salin Nomor</button>
                </div>
                @endif
                @if($wedding->groom_bank ?? false)
                <div class="dr-gift-card">
                    <span class="dr-gift-icon">💳</span>
                    <div class="dr-gift-bank">{{ $wedding->groom_bank }}</div>
                    <div class="dr-gift-norek">{{ $wedding->groom_norek }}</div>
                    <div class="dr-gift-atas">a/n {{ $wedding->groom_name }}</div>
                    <button class="dr-copy-btn" onclick="drCopyText('{{ $wedding->groom_norek }}', this)">Salin Nomor</button>
                </div>
                @endif
            @endif
        </div>
    </section>
    @endif


    @include('invitation.partials.guestbook')

    {{-- ══════════════════════════════════════
         RSVP + UCAPAN
    ══════════════════════════════════════ --}}
    <section class="dr-rsvp-section" id="rsvp">
        <div class="dr-divider reveal">
            <div class="dr-divider-line"></div>
            <span class="dr-divider-icon">✦</span>
            <div class="dr-divider-line"></div>
        </div>
        <span class="dr-tag reveal">Konfirmasi &amp; Ucapan</span>
        <h2 class="dr-title reveal">Kirimkan Doa Anda</h2>
        <p class="dr-sub reveal">Mohon konfirmasi kehadiran Anda dan titipkan ucapan untuk kedua mempelai.</p>

        {{-- Success state --}}
        <div class="dr-rsvp-success" id="dr-rsvp-success">
            <span class="dr-rsvp-success-icon">✦</span>
            <p style="font-family:'Playfair Display',serif; font-size:1.8rem; font-style:italic; color:var(--dr-blush);">Terima Kasih</p>
            <p style="color:rgba(245,224,227,.5); font-size:.85rem; margin-top:10px; font-family:'Raleway',sans-serif;">
                Konfirmasi dan ucapan Anda telah kami terima. 🌹
            </p>
        </div>

        <form id="dr-rsvp-form" class="dr-rsvp-form reveal" action="{{ route('rsvp.store', $wedding->slug) }}" method="POST">
            @csrf

            <div class="dr-form-row">
                <label for="guest_name">Nama Lengkap</label>
                <input type="text" id="guest_name" name="guest_name"
                    value="{{ $guest->guest_name ?? '' }}"
                    placeholder="Nama Anda" required>
            </div>

            <div class="dr-form-row">
                <label for="phone">Nomor WhatsApp <span style="opacity:.4;">(opsional)</span></label>
                <input type="tel" id="phone" name="phone" placeholder="+62 8xx xxxx xxxx">
            </div>

            <div class="dr-form-row">
                <label>Konfirmasi Kehadiran</label>
                <div class="dr-hadir-toggle">
                    <input type="radio" name="attendance" id="dr-att-hadir" value="hadir" checked>
                    <label for="dr-att-hadir">Hadir</label>

                    <input type="radio" name="attendance" id="dr-att-mungkin" value="mungkin">
                    <label for="dr-att-mungkin">Mungkin</label>

                    <input type="radio" name="attendance" id="dr-att-tidak" value="tidak_hadir">
                    <label for="dr-att-tidak">Tidak Hadir</label>
                </div>
            </div>

            <div class="dr-form-row" id="dr-row-jumlah">
                <label for="guests_count">Jumlah Tamu</label>
                <input type="number" id="guests_count" name="guests_count" min="1" max="10" value="1">
            </div>

            <div class="dr-form-row">
                <label for="dr-message">Ucapan &amp; Doa</label>
                <textarea id="dr-message" name="message"
                    placeholder="Tuliskan ucapan, doa, dan harapan untuk kedua mempelai…"></textarea>
            </div>

            <button type="submit" class="dr-submit-btn">Kirim Sekarang</button>
        </form>

        {{-- Ucapan wall --}}
        @if(($rsvps ?? collect())->count() > 0)
        <div class="reveal" style="margin-top:64px;">
            <div class="dr-divider">
                <div class="dr-divider-line"></div>
                <span class="dr-divider-icon">✦</span>
                <div class="dr-divider-line"></div>
            </div>
            <span class="dr-tag">Ucapan &amp; Doa</span>

            <div class="dr-ucapan-list" id="ucapan-list">
                @foreach($rsvps as $rsvp)
                <div class="dr-ucapan-item">
                    <div>
                        <span class="dr-ucapan-nama">{{ $rsvp->guest_name }}</span>
                        <span class="dr-ucapan-hadir">
                            · {{ $rsvp->is_attending ? '✓ Hadir' : '✗ Tidak Hadir' }}
                        </span>
                    </div>
                    @php
                        $msg = $rsvp->notes ?? '';
                        if (str_contains($msg, '[RSVP]')) $msg = trim(str_replace('[RSVP]', '', $msg));
                    @endphp
                    <p class="dr-ucapan-pesan">{{ $msg }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>


    {{-- ══════════════════════════════════════
         FOOTER
    ══════════════════════════════════════ --}}
    <footer class="dr-footer">
        <div class="dr-divider" style="margin-bottom:28px;">
            <div class="dr-divider-line"></div>
            <span class="dr-divider-icon">✦</span>
            <div class="dr-divider-line"></div>
        </div>

        <div class="dr-footer-names">
            {{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}
        </div>

        <div class="dr-footer-info">
            @if($wedding->event_date)
            {{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}<br>
            @endif
            @if($wedding->location)
            {{ $wedding->location }}<br>
            @endif
            <em class="dr-footer-em">With all our love &amp; gratitude 🌹</em>
        </div>

        <div class="dr-divider" style="margin:36px auto 24px; max-width:180px;">
            <div class="dr-divider-line"></div>
            <span class="dr-divider-icon" style="font-size:.6rem; opacity:.5;">◆</span>
            <div class="dr-divider-line"></div>
        </div>

        <p class="dr-footer-credit">Dibuat dengan ❤ · TretanInvite</p>
    </footer>

</div>{{-- /#dr-main --}}

{{-- ═══ MUSIC PLAYER ═══ --}}
@php
    $_rawMus = null;
    if (!empty($wedding->music_file))  $_rawMus = asset('storage/' . $wedding->music_file);
    elseif (!empty($wedding->music_url)) $_rawMus = $wedding->music_url;
    $_musUrl = null; $_ytId = null;
    if ($_rawMus) {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $_rawMus, $_ym)) {
            $_ytId = $_ym[1];
        } else { $_musUrl = $_rawMus; }
    }
@endphp
@if(!empty($_musUrl) || !empty($_ytId))
<button id="wp-music-btn" onclick="toggleMusic()" title="Putar Musik" style="position:fixed;bottom:20px;left:16px;z-index:9999;width:44px;height:44px;border-radius:50%;border:2px solid rgba(255,255,255,.25);background:rgba(0,0,0,.55);color:#fff;font-size:20px;cursor:pointer;display:none;align-items:center;justify-content:center;backdrop-filter:blur(8px);transition:all .2s;box-shadow:0 2px 12px rgba(0,0,0,.4);">♪</button>
@endif
@if(!empty($_ytId))
<div style="position:fixed;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;top:-9999px;left:-9999px;" aria-hidden="true"><div id="wp-yt-player"></div></div>
@endif

@endsection

@push('scripts')
<script>
    window.weddingData = {
        eventDate: '{{ $wedding->event_date->format("Y-m-d") }}',
        csrfToken: '{{ csrf_token() }}',
        isPreview: {{ !empty($isPreview) ? 'true' : 'false' }}
    };
</script>
<script src="{{ asset('js/dark-romance.js') }}"></script>
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
<script>document.body.classList.add('dark-template');</script>
<script src="{{ asset('js/photo-bg-slideshow.js') }}"></script>
@if(!empty($_musUrl) || !empty($_ytId))
@if(!empty($_ytId))<script src="https://www.youtube.com/iframe_api" async defer></script>@endif
<script>window.musicData={url:'{{ $_musUrl ?? "" }}',ytId:'{{ $_ytId ?? "" }}',btnId:'wp-music-btn'};</script>
<script src="{{ asset('js/music-player.js') }}"></script>
@endif
@endpush
