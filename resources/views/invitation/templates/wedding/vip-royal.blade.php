@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=cormorant-garamond:300,300i,400,400i,600,700|jost:200,300,400,500|cinzel:400,700&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vip-royal.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
<style>
/* ──────────────────────────────────────────
   GUESTBOOK REAL-TIME — VIP Royal
────────────────────────────────────────── */
.vr-gb-counter {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 0 auto 32px;
}
.vr-gb-counter-num {
    font-family: 'Cinzel', serif;
    font-size: 2rem;
    color: var(--gold);
    line-height: 1;
}
.vr-gb-counter-lbl {
    font-size: .78rem;
    letter-spacing: .12em;
    color: var(--txt-dim);
    text-transform: uppercase;
}

/* Form */
.vr-gb-name-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
}
.vr-gb-name-row input {
    flex: 1;
}
.vr-gb-av-preview {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: var(--gold);
    color: #080818;
    font-family: 'Cinzel', serif;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .3s;
}
.vr-gb-msg-wrap { position: relative; margin-bottom: 6px; }
.vr-gb-char {
    text-align: right;
    font-size: .7rem;
    color: var(--txt-dim);
    margin-bottom: 14px;
    opacity: .6;
}

/* Live header */
.vr-gb-live-hdr {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .7rem;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: var(--txt-dim);
    margin: 36px auto 20px;
    max-width: 560px;
}
.vr-gb-live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #4ade80;
    animation: vr-pulse-gb 2s ease-in-out infinite;
    flex-shrink: 0;
}
@keyframes vr-pulse-gb {
    0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(74,222,128,.5); }
    50%       { opacity: .7; box-shadow: 0 0 0 5px rgba(74,222,128,0); }
}
#vr-gb-new-badge {
    background: var(--gold);
    color: #080818;
    font-size: .65rem;
    font-family: 'Cinzel', serif;
    padding: 2px 8px;
    border-radius: 20px;
    letter-spacing: .05em;
    cursor: pointer;
}

/* List */
.vr-gb-list {
    max-width: 560px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.vr-gb-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(201,168,76,.12);
    border-radius: 14px;
    padding: 16px 18px;
    opacity: 0;
    transform: translateY(16px);
    transition: opacity .45s ease, transform .45s ease;
}
.vr-gb-item.vr-gb-in {
    opacity: 1;
    transform: translateY(0);
}
.vr-gb-item.vr-gb-new {
    border-color: rgba(201,168,76,.32);
    background: rgba(201,168,76,.05);
}
.vr-gb-av {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gold);
    color: #080818;
    font-family: 'Cinzel', serif;
    font-size: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.vr-gb-body { flex: 1; min-width: 0; }
.vr-gb-item-name {
    font-family: 'Cinzel', serif;
    font-size: .82rem;
    color: var(--gold);
    letter-spacing: .06em;
    margin-bottom: 5px;
}
.vr-gb-item-msg {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1rem;
    color: var(--cream);
    line-height: 1.6;
    word-break: break-word;
}
.vr-gb-item-time {
    font-size: .65rem;
    color: var(--txt-dim);
    letter-spacing: .08em;
    margin-top: 8px;
    opacity: .55;
}
.vr-gb-empty {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-size: 1rem;
    color: var(--txt-dim);
    text-align: center;
    padding: 32px 0;
    opacity: .5;
}

/* ────────────────────────────────────────────
   TIKET DIGITAL
──────────────────────────────────────────── */
.vr-ticket {
    max-width: 340px;
    margin: 0 auto;
    background: linear-gradient(160deg, #0d1040 0%, #080522 60%, #100828 100%);
    border: 1px solid rgba(201,168,76,.3);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 0 50px rgba(201,168,76,.08), 0 0 0 1px rgba(201,168,76,.05);
}
.vr-ticket-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 22px 20px 18px;
    border-bottom: 1px dashed rgba(201,168,76,.2);
}
.vr-ticket-crown {
    font-size: 1.1rem;
    color: var(--gold);
}
.vr-ticket-label {
    font-family: 'Cinzel', serif;
    font-size: .65rem;
    letter-spacing: .4em;
    color: var(--gold);
    text-transform: uppercase;
    opacity: .8;
}
.vr-ticket-names {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--cream);
    text-align: center;
    padding: 18px 20px 4px;
    line-height: 1.4;
}
.vr-ticket-date {
    font-size: .72rem;
    letter-spacing: .18em;
    color: var(--gold);
    text-transform: uppercase;
    text-align: center;
    padding-bottom: 16px;
    opacity: .7;
}
.vr-ticket-tear {
    height: 0;
    border-top: 2px dashed rgba(201,168,76,.25);
    margin: 0 -1px;
    position: relative;
}
.vr-ticket-tear::before,
.vr-ticket-tear::after {
    content: '';
    position: absolute;
    top: -10px;
    width: 18px;
    height: 18px;
    background: #050818;
    border-radius: 50%;
    border: 1px solid rgba(201,168,76,.2);
}
.vr-ticket-tear::before { left: -10px; }
.vr-ticket-tear::after  { right: -10px; }

.vr-ticket-qr-wrap {
    display: flex;
    justify-content: center;
    padding: 24px 20px 12px;
}
.vr-ticket-qr-img {
    width: 180px;
    height: 180px;
    border-radius: 12px;
    background: #fff;
    padding: 6px;
    display: block;
}
.vr-ticket-guest {
    font-family: 'Cinzel', serif;
    font-size: .9rem;
    letter-spacing: .1em;
    color: var(--cream);
    text-align: center;
    padding: 0 20px 2px;
}
.vr-ticket-pax {
    font-size: .7rem;
    letter-spacing: .12em;
    color: var(--gold);
    text-align: center;
    opacity: .65;
    padding-bottom: 20px;
    text-transform: uppercase;
}
.vr-ticket-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 0 20px 14px;
}
.vr-ticket-btn {
    flex: 1;
    max-width: 140px;
    padding: 9px 14px;
    border-radius: 30px;
    border: 1px solid var(--gold);
    background: transparent;
    color: var(--gold);
    font-family: 'Cinzel', serif;
    font-size: .65rem;
    letter-spacing: .1em;
    cursor: pointer;
    transition: background .25s, color .25s;
}
.vr-ticket-btn:hover {
    background: var(--gold);
    color: #080818;
}
.vr-ticket-btn-sec {
    border-color: rgba(201,168,76,.4);
    color: var(--txt-dim);
}
.vr-ticket-btn-sec:hover {
    background: rgba(201,168,76,.15);
    color: var(--gold);
}
.vr-ticket-hint {
    font-size: .65rem;
    color: var(--txt-dim);
    text-align: center;
    padding: 4px 20px 20px;
    opacity: .45;
    letter-spacing: .05em;
}

/* Social links di kartu mempelai */
.vr-couple-social {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 6px;
    margin-right: 6px;
    font-family: 'Jost', sans-serif;
    font-size: .72rem;
    letter-spacing: .08em;
    color: var(--gold);
    border: 1px solid rgba(201,168,76,.3);
    border-radius: 20px;
    padding: 3px 10px;
    text-decoration: none;
    transition: background .2s, color .2s;
}
.vr-couple-social:hover {
    background: rgba(201,168,76,.15);
    color: var(--cream);
}
.vr-couple-wa {
    color: #4ade80;
    border-color: rgba(74,222,128,.3);
}
.vr-couple-wa:hover {
    background: rgba(74,222,128,.1);
    color: var(--cream);
}
.vr-couple-social-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 4px;
    margin-top: 10px;
}
    margin: 14px auto 10px;
    text-align: center;
    font-family: 'Cormorant Garamond', serif;
}
.vr-rsvp-summary-badge {
    font-family: 'Cinzel', serif;
    font-size: .82rem;
    letter-spacing: .1em;
    font-weight: 700;
}
.vr-rsvp-summary-msg {
    font-style: italic;
    font-size: 1rem;
    color: var(--cream);
    margin-top: 10px;
    opacity: .8;
    line-height: 1.6;
}
</style>
@endpush

@php
    /* ── Demo data for preview mode ── */
    $isDemo = !empty($isPreview);
    $demoPhotos = $demoPhotos ?? [];

    $brideName  = $wedding->bride_name  ?? 'Annisa Putri';
    $groomName  = $wedding->groom_name  ?? 'Rizky Pratama';
    $eventDate  = $wedding->event_date  ?? now()->addDays(60);
    $location   = $wedding->location    ?? 'Grand Ballroom, Hotel Mulia Jakarta';

    /* Cover photo: VIP field atau fallback couple photo */
    $coverPhoto = null;
    if (!empty($wedding->cover_photo)) {
        $coverPhoto = asset('storage/' . $wedding->cover_photo);
    } elseif (!empty($wedding->bride_photo)) {
        $coverPhoto = asset('storage/' . $wedding->bride_photo);
    }

    /* Section background photos (VIP Royal) */
    $bgMempelai = !empty($wedding->bg_mempelai_photo) ? asset('storage/' . $wedding->bg_mempelai_photo) : null;
    $bgAcara    = !empty($wedding->bg_acara_photo)    ? asset('storage/' . $wedding->bg_acara_photo)    : null;
    $bgLokasi   = !empty($wedding->bg_lokasi_photo)   ? asset('storage/' . $wedding->bg_lokasi_photo)   : null;

    /* Video: VIP field — convert YouTube/Vimeo URL to embed */
    $videoEmbed = null;
    if (!empty($wedding->video_url)) {
        $v = $wedding->video_url;
        if (preg_match('/youtu\.be\/([^?]+)/', $v, $m) || preg_match('/youtube\.com\/watch\?v=([^&]+)/', $v, $m)) {
            $videoEmbed = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $v, $m)) {
            $videoEmbed = 'https://player.vimeo.com/video/' . $m[1];
        }
    }

    /* Extra events: VIP JSON field */
    $extraEvents = (is_array($wedding->extra_events ?? null) ? $wedding->extra_events : []);
    if ($isDemo) {
        $extraEvents = [
            ['label' => 'Siraman', 'date' => $eventDate->clone()->subDay(1)->locale('id')->translatedFormat('l, d F Y'), 'time' => '09.00 WIB', 'location' => 'Kediaman Keluarga Mempelai Wanita'],
        ];
    }

    /* Gallery photos */
    $gallPhotos = [];
    if ($isDemo && !empty($demoPhotos)) {
        $gallPhotos = $demoPhotos;
    } elseif (($wedding->galleries ?? collect())->count() > 0) {
        $gallPhotos = $wedding->galleries->map(fn($g) => asset('storage/' . $g->path))->toArray();
    }

    /* Music — detect YouTube vs direct audio */
    $musicUrl   = null;
    $ytVideoId  = null;
    $rawMusicUrl = null;
    if (!empty($wedding->music_file))  $rawMusicUrl = asset('storage/' . $wedding->music_file);
    elseif (!empty($wedding->music_url)) $rawMusicUrl = $wedding->music_url;
    if ($rawMusicUrl) {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $rawMusicUrl, $ytm)) {
            $ytVideoId = $ytm[1]; // YouTube — audio-only via hidden iframe
        } else {
            $musicUrl = $rawMusicUrl; // Direct audio file
        }
    }
    /* Demo: fallback agar tombol musik muncul saat preview */
    $demoMusicOnly = $isDemo && !$musicUrl && !$ytVideoId;

    /* Guestbook */
    $showGuestbook = $wedding->isVip() && $wedding->guestbook_enabled;
    $gbEntries = $guestbookEntries ?? collect();
@endphp

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- ═══ PARTICLE CANVAS ═══ --}}
<canvas id="vr-particles"></canvas>

{{-- ═══════════════════════════════════════════
     COVER SPLASH
═══════════════════════════════════════════ --}}
<div id="vr-cover" onclick="vrOpenInvitation()">

    {{-- Background: cover photo atau gradient --}}
    <div class="vr-cover-bg"
         @if($coverPhoto)
             style="background-image: url('{{ $coverPhoto }}');"
         @else
             style="background: linear-gradient(160deg, #050818, #0d1040, #0d060d);"
         @endif></div>
    <div class="vr-cover-noise"></div>
    <div class="vr-cover-vignette"></div>
    <div class="vr-cover-halo"></div>

    <div class="vr-cover-inner">
        <span class="vr-cover-crown">♛</span>

        @if(!empty($guest))
        <div style="margin-bottom: 24px;">
            <div class="vr-cover-kepada">Kepada Yang Terhormat</div>
            <div class="vr-cover-guest">{{ $guest->guest_name }}</div>
        </div>
        @endif

        <div class="vr-cover-label">Undangan Pernikahan</div>
        <div class="vr-cover-names">
            {{ $brideName }}
            <span class="vr-cover-amp">&amp;</span>
            {{ $groomName }}
        </div>

        @if($eventDate)
        <div class="vr-cover-date">
            {{ $eventDate->locale('id')->translatedFormat('d F Y') }}
        </div>
        @endif

        <button class="vr-cover-btn">
            <span class="vr-pulse"></span>
            Buka Undangan
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MAIN CONTENT (tersembunyi sampai cover dibuka)
═══════════════════════════════════════════ --}}
<div id="vr-main">

    {{-- NAV --}}
    <nav class="vr-nav" id="vr-nav">
        <a href="#mempelai">Mempelai</a>
        <a href="#acara">Acara</a>
        @if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)<a href="#galeri">Galeri</a>@endif
        <a href="#lokasi">Lokasi</a>
        @if(!empty($wedding->bride_bank) || !empty($wedding->groom_bank))<a href="#amplop">Amplop</a>@endif
        @if($showGuestbook || !$isDemo)<a href="#ucapan">Ucapan</a>@endif
        <a href="#rsvp">RSVP</a>
        @if($guest && !$isDemo)<a href="#tiket">Tiket</a>@endif
    </nav>

    {{-- ═══ HERO — Interactive Split-Screen Couple Photo ═══ --}}
    @php
        $bridePhotoUrl = !empty($wedding->bride_photo) ? asset('storage/'.$wedding->bride_photo) : null;
        $groomPhotoUrl = !empty($wedding->groom_photo) ? asset('storage/'.$wedding->groom_photo) : null;
        // Fallback: use cover photo for both sides if individual photos missing
        $heroFallback  = $coverPhoto ?? null;
    @endphp
    <section id="vr-hero">
        {{-- Split background panels --}}
        <div class="vr-split" id="vr-split">
            <div class="vr-split-l" id="vr-split-l"
                 @if($bridePhotoUrl) style="background-image:url('{{ $bridePhotoUrl }}');" @endif></div>
            <div class="vr-split-r" id="vr-split-r"
                 @if($groomPhotoUrl) style="background-image:url('{{ $groomPhotoUrl }}');" @elseif($bridePhotoUrl) style="background-image:url('{{ $bridePhotoUrl }}');" @endif></div>
            <div class="vr-split-overlay"></div>
            <div class="vr-split-line" id="vr-split-line"></div>
            <div class="vr-split-hint vr-split-hint-l">{{ $brideName }}</div>
            <div class="vr-split-hint vr-split-hint-r">{{ $groomName }}</div>
        </div>

        {{-- Centered overlay content --}}
        <div class="vr-hero-content">
            <div class="vr-rv" style="margin-bottom: 6px;">
                <span class="vr-label">Undangan Pernikahan</span>
            </div>
            <div class="vr-hero-names vr-rv">
                {{ $brideName }}
                <span class="vr-hero-amp">&amp;</span>
                {{ $groomName }}
            </div>
            @if($eventDate)
            <div class="vr-hero-date-wrap vr-rv">
                <div class="vr-hero-date-line"></div>
                <div class="vr-hero-date">{{ $eventDate->locale('id')->translatedFormat('l, d F Y') }}</div>
                <div class="vr-hero-date-line right"></div>
            </div>
            @endif
            <div class="vr-hero-scroll vr-rv">↓ &nbsp; Gulir ke bawah</div>
        </div>
    </section>

    {{-- ═══ OPENING TEXT ═══ --}}
    @if(!empty($wedding->opening_text))
    <section class="vr-sec vr-sec-alt" style="padding: 64px 24px;">
        <div class="vr-rv" style="max-width:660px; margin:0 auto; text-align:center;">
            <span class="vr-cover-crown" style="font-size:1.6rem; display:block; margin-bottom:18px; opacity:.7;">♛</span>
            <p style="font-family:'Cormorant Garamond',serif; font-size:clamp(1rem,2.4vw,1.2rem); font-style:italic; font-weight:300; color:var(--cream); line-height:1.9; white-space:pre-line; opacity:.92;">{{ $wedding->opening_text }}</p>
        </div>
    </section>
    @endif

    {{-- ═══ SEPARATOR ═══ --}}
    <div class="vr-sep" aria-hidden="true"><span class="vr-sep-icon">✦</span></div>

    {{-- ═══ MEMPELAI ═══ --}}
    <section class="vr-sec vr-sec-ornamented" id="mempelai" data-orna="Mempelai">
        @if(!empty($bgMempelai))
        <div style="position:absolute;inset:0;background-image:url('{{ $bgMempelai }}');background-size:cover;background-position:center;z-index:-1;"></div>
        <div style="position:absolute;inset:0;background:rgba(4,4,20,0.82);z-index:-1;"></div>
        @endif
        <span class="vr-label vr-rv">Yang Berbahagia</span>
        <h2 class="vr-title vr-rv">Mempelai</h2>

        <div class="vr-couple-grid">
            {{-- Mempelai Wanita --}}
            <div class="vr-couple-card vr-rv">
                <div class="vr-couple-photo">
                    @if(!empty($wedding->bride_photo))
                        <img src="{{ asset('storage/'.$wedding->bride_photo) }}" alt="{{ $brideName }}">
                    @else 🌸 @endif
                </div>
                <div class="vr-couple-role">Mempelai Wanita</div>
                <div class="vr-couple-name">{{ $brideName }}</div>
                @if(!empty($wedding->bride_fullname))
                    <div class="vr-couple-full">{{ $wedding->bride_fullname }}</div>
                @endif
@php
                    $_brideParentLine = '';
                    if (!empty($wedding->bride_father) || !empty($wedding->bride_mother)) {
                        $_brideParentLine = ($wedding->bride_father ?? '') . ((!empty($wedding->bride_father) && !empty($wedding->bride_mother)) ? ' & ' : '') . ($wedding->bride_mother ?? '');
                    } elseif (!empty($wedding->bride_parent)) {
                        $_brideParentLine = $wedding->bride_parent;
                    }
                @endphp
                @if(!empty($_brideParentLine))
                    <div class="vr-couple-par">
                        Putri dari<br>
                        <strong style="color:var(--cream);">{{ $_brideParentLine }}</strong>
                    </div>
                @endif
                @if(!empty($wedding->bride_wa))
                <div class="vr-couple-social-row vr-couple-social">
                    <a href="https://wa.me/62{{ ltrim($wedding->bride_wa, '0') }}" target="_blank" rel="noopener" class="vr-couple-wa">💬 WhatsApp</a>
                </div>
                @endif
            </div>

            <div class="vr-couple-amp vr-rv">&amp;</div>

            {{-- Mempelai Pria --}}
            <div class="vr-couple-card vr-rv">
                <div class="vr-couple-photo">
                    @if(!empty($wedding->groom_photo))
                        <img src="{{ asset('storage/'.$wedding->groom_photo) }}" alt="{{ $groomName }}">
                    @else 🤵 @endif
                </div>
                <div class="vr-couple-role">Mempelai Pria</div>
                <div class="vr-couple-name">{{ $groomName }}</div>
                @if(!empty($wedding->groom_fullname))
                    <div class="vr-couple-full">{{ $wedding->groom_fullname }}</div>
                @endif
@php
                    $_groomParentLine = '';
                    if (!empty($wedding->groom_father) || !empty($wedding->groom_mother)) {
                        $_groomParentLine = ($wedding->groom_father ?? '') . ((!empty($wedding->groom_father) && !empty($wedding->groom_mother)) ? ' & ' : '') . ($wedding->groom_mother ?? '');
                    } elseif (!empty($wedding->groom_parent)) {
                        $_groomParentLine = $wedding->groom_parent;
                    }
                @endphp
                @if(!empty($_groomParentLine))
                    <div class="vr-couple-par">
                        Putra dari<br>
                        <strong style="color:var(--cream);">{{ $_groomParentLine }}</strong>
                    </div>
                @endif
                @if(!empty($wedding->groom_wa))
                <div class="vr-couple-social-row vr-couple-social">
                    <a href="https://wa.me/62{{ ltrim($wedding->groom_wa, '0') }}" target="_blank" rel="noopener" class="vr-couple-wa">💬 WhatsApp</a>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══ FOTO COUPLE ═══ --}}
    @if(!empty($wedding->couple_photo))
    <section style="position:relative; padding:0; overflow:hidden;">
        <img src="{{ asset('storage/'.$wedding->couple_photo) }}" alt="Foto Bersama {{ $brideName }} &amp; {{ $groomName }}" style="width:100%; max-height:75vh; object-fit:cover; display:block; filter:brightness(.82);">
        <div style="position:absolute;inset:0;background:linear-gradient(to bottom, transparent 50%, rgba(4,4,20,.7));pointer-events:none;"></div>
        <div style="position:absolute;bottom:28px;left:0;right:0;text-align:center;">
            <div style="font-family:'Cinzel',serif;font-size:.62rem;letter-spacing:.4em;text-transform:uppercase;color:rgba(201,168,76,.8);">{{ $brideName }} &amp; {{ $groomName }}</div>
        </div>
    </section>
    @endif

    {{-- ═══ VIDEO EMBED (VIP) ═══ --}}
    @if($videoEmbed || $isDemo)
    <section class="vr-sec vr-sec-alt" style="padding: 70px 24px;">
        <span class="vr-label vr-rv">Momen Spesial</span>
        <h2 class="vr-title vr-rv">Video Kami</h2>
        <p class="vr-sub vr-rv">Saksikan perjalanan cinta kami dalam video istimewa ini.</p>

        <div class="vr-video-wrap vr-rv">
            @if($videoEmbed)
                <iframe src="{{ $videoEmbed }}" allowfullscreen allow="autoplay; encrypted-media" title="Video Prewedding"></iframe>
            @else
                {{-- Demo placeholder --}}
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;background:rgba(201,168,76,.04);">
                    <span style="font-size:3rem; color:var(--gold); opacity:.4;">▶</span>
                    <span style="font-family:'Cinzel',serif; font-size:.6rem; letter-spacing:.4em; color:var(--gold); opacity:.5; text-transform:uppercase;">Video Prewedding</span>
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ═══ SEPARATOR ═══ --}}
    <div class="vr-sep" aria-hidden="true"><span class="vr-sep-icon">◈</span></div>

    {{-- ═══ COUNTDOWN ═══ --}}
    @if($eventDate)
    <section class="vr-sec vr-countdown-sec" style="padding: 80px 24px;">
        <span class="vr-label vr-rv">Menuju Hari Bahagia</span>
        <h2 class="vr-title vr-rv">Hitung Mundur</h2>
        <div class="vr-cd-wrap vr-rv">
            <div class="vr-cd-item">
                <span class="vr-cd-num" id="vr-hari">00</span>
                <span class="vr-cd-lbl">Hari</span>
            </div>
            <div class="vr-cd-sep">:</div>
            <div class="vr-cd-item">
                <span class="vr-cd-num" id="vr-jam">00</span>
                <span class="vr-cd-lbl">Jam</span>
            </div>
            <div class="vr-cd-sep">:</div>
            <div class="vr-cd-item">
                <span class="vr-cd-num" id="vr-menit">00</span>
                <span class="vr-cd-lbl">Menit</span>
            </div>
            <div class="vr-cd-sep">:</div>
            <div class="vr-cd-item">
                <span class="vr-cd-num" id="vr-detik">00</span>
                <span class="vr-cd-lbl">Detik</span>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══ ACARA + EXTRA EVENTS (VIP) ═══ --}}
    <section class="vr-sec vr-sec-ornamented" id="acara" data-orna="Acara">
        @if(!empty($bgAcara))
        <div style="position:absolute;inset:0;background-image:url('{{ $bgAcara }}');background-size:cover;background-position:center;z-index:-1;"></div>
        <div style="position:absolute;inset:0;background:rgba(4,4,20,0.82);z-index:-1;"></div>
        @endif
        <span class="vr-label vr-rv">Rangkaian Acara</span>
        <h2 class="vr-title vr-rv">Hari Istimewa</h2>
        <p class="vr-sub vr-rv">Dengan penuh kebahagiaan kami mengundang Anda untuk bersama kami dalam momen bersejarah ini.</p>

        @php
            /* ── Kumpulkan semua acara, sort by date ascending ── */
            $allEvents = [];

            /* Akad */
            $akadDate = $wedding->akad_date ?? $eventDate;
            if ($akadDate) {
                $allEvents[] = [
                    'type'     => 'akad',
                    'sort_ts'  => \Carbon\Carbon::parse($akadDate)->timestamp,
                    'icon'     => '☽',
                    'type_lbl' => 'Akad Nikah',
                    'name'     => 'Ijab Qabul',
                    'date_obj' => \Carbon\Carbon::parse($akadDate),
                    'time'     => $wedding->akad_time ?? '08.00 – 10.00',
                    'location' => $wedding->akad_location ?? $location,
                    'dot_color'=> '',
                ];
            }

            /* Resepsi */
            $resepsiDate = $wedding->reception_date ?? $eventDate;
            if ($resepsiDate) {
                $allEvents[] = [
                    'type'     => 'resepsi',
                    'sort_ts'  => \Carbon\Carbon::parse($resepsiDate)->timestamp,
                    'icon'     => '◇',
                    'type_lbl' => 'Resepsi',
                    'name'     => $wedding->customText('event_name', 'Walimatul Ursy'),
                    'date_obj' => \Carbon\Carbon::parse($resepsiDate),
                    'time'     => $wedding->reception_time ?? '11.00 – 14.00',
                    'location' => $wedding->reception_location ?? $location,
                    'dot_color'=> '',
                ];
            }

            /* Extra Events */
            foreach ($extraEvents as $extra) {
                if (empty($extra['label'])) continue;
                $extraTs = null;
                if (!empty($extra['date'])) {
                    try { $extraTs = \Carbon\Carbon::parse($extra['date'])->timestamp; } catch (\Exception $e) {}
                }
                $allEvents[] = [
                    'type'      => 'extra',
                    'sort_ts'   => $extraTs ?? PHP_INT_MAX,
                    'icon'      => '✦',
                    'type_lbl'  => 'Rangkaian Acara',
                    'name'      => $extra['label'],
                    'date_obj'  => !empty($extra['date']) ? (function() use ($extra) { try { return \Carbon\Carbon::parse($extra['date']); } catch (\Exception $e) { return null; } })() : null,
                    'date_raw'  => $extra['date'] ?? null,
                    'time'      => $extra['time'] ?? null,
                    'location'  => $extra['location'] ?? null,
                    'dot_color' => 'color:var(--violet-l);',
                ];
            }

            /* Sort berdasarkan timestamp (terkecil = terlama = paling kiri) */
            usort($allEvents, fn($a, $b) => $a['sort_ts'] <=> $b['sort_ts']);
        @endphp

        <div class="vr-event-grid">
            @foreach($allEvents as $ev)
            <div class="vr-event-card {{ $ev['type'] === 'extra' ? 'vr-extra' : '' }} vr-rv">
                <span class="vr-event-icon">{{ $ev['icon'] }}</span>
                <div class="vr-event-type">{{ $ev['type_lbl'] }}</div>
                <div class="vr-event-name">{{ $ev['name'] }}</div>

                {{-- Baris tanggal & waktu --}}
                @if($ev['date_obj'] || !empty($ev['time']))
                <div class="vr-event-row">
                    <span class="vr-event-row-dot" style="{{ $ev['dot_color'] }}">◈</span>
                    <div>
                        @if($ev['date_obj'])
                            <strong style="color:var(--cream); display:block;">
                                {{ $ev['date_obj']->locale('id')->translatedFormat('l, d F Y') }}
                            </strong>
                        @elseif(!empty($ev['date_raw']))
                            <strong style="color:var(--cream); display:block;">{{ $ev['date_raw'] }}</strong>
                        @endif
                        @if(!empty($ev['time']))
                            {{ trim(preg_replace('/\s*wib\s*$/i', '', $ev['time'])) }} WIB
                        @endif
                    </div>
                </div>
                @endif

                {{-- Baris lokasi --}}
                @if(!empty($ev['location']))
                <div class="vr-event-row">
                    <span class="vr-event-row-dot" style="{{ $ev['dot_color'] }}">◈</span>
                    <div>{{ $ev['location'] }}</div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Dresscode --}}
        @if(!empty($wedding->dresscode))
        <div class="vr-rv" style="margin-top:32px;display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;">
            <span style="font-family:'Cinzel',serif;font-size:.62rem;letter-spacing:.28em;text-transform:uppercase;color:var(--txt-dim);">Dresscode</span>
            <span style="width:1px;height:1em;background:var(--gold-dim);opacity:.4;"></span>
            <span style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;font-style:italic;color:var(--gold);">{{ $wedding->dresscode }}</span>
        </div>
        @endif

        {{-- Tambah ke Kalender --}}
        @if($eventDate)
        @php
            $_calTitle = 'Pernikahan ' . $brideName . ' & ' . $groomName;
            $_calStart = $eventDate->format('Ymd') . 'T080000';
            $_calEnd   = $eventDate->format('Ymd') . 'T150000';
            $_calUrl   = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                       . '&text=' . urlencode($_calTitle)
                       . '&dates=' . $_calStart . '/' . $_calEnd
                       . '&location=' . urlencode($location)
                       . '&sf=true&output=xml';
        @endphp
        <div class="vr-rv" style="margin-top:28px;text-align:center;">
            <a href="{{ $_calUrl }}" target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:8px;font-family:'Cinzel',serif;font-size:.65rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);border:1px solid rgba(201,168,76,.3);border-radius:30px;padding:10px 22px;text-decoration:none;transition:background .2s;"
               onmouseover="this.style.background='rgba(201,168,76,.1)'" onmouseout="this.style.background='transparent'">
                📅 &nbsp; Tambah ke Google Calendar
            </a>
        </div>
        @endif
    </section>
    @if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)
    <div class="vr-sep" aria-hidden="true"><span class="vr-sep-icon">✦</span></div>
    <section class="vr-gallery-sec" id="galeri">
        <div class="vr-gallery-header text-center">
            <span class="vr-label vr-rv">Momen Berharga</span>
            <h2 class="vr-title vr-rv">Galeri Foto</h2>
            <p class="vr-sub vr-rv" style="margin-top:6px;font-size:.85rem;opacity:.5;">Klik foto untuk memperbesar</p>
        </div>
        @include('invitation.partials.gallery-vip-royal', [
            'gallPhotos'    => $gallPhotos,
            'bridePhotoUrl' => $bridePhotoUrl ?? null,
            'groomPhotoUrl' => $groomPhotoUrl ?? null,
        ])
    </section>
    @endif

    {{-- ═══ SEPARATOR ═══ --}}
    <div class="vr-sep" aria-hidden="true"><span class="vr-sep-icon">◈</span></div>

    {{-- ═══ LOKASI ═══ --}}
    <section class="vr-sec vr-sec-alt" id="lokasi">
        @if(!empty($bgLokasi))
        <div style="position:absolute;inset:0;background-image:url('{{ $bgLokasi }}');background-size:cover;background-position:center;z-index:-1;"></div>
        <div style="position:absolute;inset:0;background:rgba(4,4,20,0.80);z-index:-1;"></div>
        @endif
        <span class="vr-label vr-rv">Temukan Kami</span>
        <h2 class="vr-title vr-rv">Lokasi Acara</h2>

        @if(!empty($wedding->location))
        <p class="vr-sub vr-rv" style="margin-top: 8px;">{{ $wedding->location }}</p>
        @endif

        <div class="vr-map-wrap vr-rv">
            @if(!empty($wedding->map_embed))
                {!! $wedding->map_embed !!}
            @else
                <div class="vr-map-placeholder">
                    <span style="font-size:2rem; color:var(--gold); opacity:.35;">◈</span>
                    <span style="font-family:'Cinzel',serif; font-size:.62rem; letter-spacing:.35em; color:var(--gold); opacity:.4; text-transform:uppercase;">{{ $location }}</span>
                </div>
            @endif
        </div>

        @if(!empty($wedding->map_link))
        <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="vr-map-btn vr-rv">
            ◈ &nbsp; Buka di Google Maps
        </a>
        @endif
    </section>

    {{-- ═══ AMPLOP DIGITAL ═══ --}}
    @php
        $hasBankInfo = (!empty($wedding->bride_bank) || !empty($wedding->groom_bank) || ($wedding->bank_accounts ?? collect())->count() > 0);
    @endphp
    @if($hasBankInfo)
    <section class="vr-sec vr-sec-ornamented" id="amplop" data-orna="Amplop">
        <span class="vr-label vr-rv">Hadiah &amp; Doa</span>
        <h2 class="vr-title vr-rv">Amplop Digital</h2>
        <p class="vr-sub vr-rv">Kehadiran dan doa restu Anda adalah hadiah terindah bagi kami. Namun jika ingin memberi lebih, kami siapkan rekening di bawah.</p>

        <div class="vr-gift-grid vr-rv">
            @if(($wedding->bank_accounts ?? collect())->count() > 0)
                @foreach($wedding->bank_accounts as $acc)
                <div class="vr-gift-card">
                    <span class="vr-gift-icon">◈</span>
                    <div class="vr-gift-bank">{{ $acc->bank_name }}</div>
                    <div class="vr-gift-norek">{{ $acc->account_number }}</div>
                    <div class="vr-gift-atas">a/n {{ $acc->account_name }}</div>
                    <button class="vr-copy-btn" onclick="vrCopy('{{ $acc->account_number }}', this)">Salin Nomor</button>
                </div>
                @endforeach
            @else
                @if(!empty($wedding->bride_bank))
                <div class="vr-gift-card">
                    <span class="vr-gift-icon">🌸</span>
                    <div class="vr-gift-bank">{{ $wedding->bride_bank }}</div>
                    <div class="vr-gift-norek">{{ $wedding->bride_norek ?? '' }}</div>
                    <div class="vr-gift-atas">a/n {{ $brideName }}</div>
                    <button class="vr-copy-btn" onclick="vrCopy('{{ $wedding->bride_norek ?? '' }}', this)">Salin Nomor</button>
                </div>
                @endif
                @if(!empty($wedding->groom_bank))
                <div class="vr-gift-card">
                    <span class="vr-gift-icon">🌿</span>
                    <div class="vr-gift-bank">{{ $wedding->groom_bank }}</div>
                    <div class="vr-gift-norek">{{ $wedding->groom_norek ?? '' }}</div>
                    <div class="vr-gift-atas">a/n {{ $groomName }}</div>
                    <button class="vr-copy-btn" onclick="vrCopy('{{ $wedding->groom_norek ?? '' }}', this)">Salin Nomor</button>
                </div>
                @endif
            @endif
        </div>
    </section>
    @endif

    {{-- ═══ GUESTBOOK REAL-TIME (VIP) ═══ --}}
    {{-- ═══ SEPARATOR ═══ --}}
    <div class="vr-sep" aria-hidden="true"><span class="vr-sep-icon">◈</span></div>

    @if($showGuestbook)
    <section class="vr-sec vr-sec-alt" id="ucapan">
        <span class="vr-label vr-rv">Titip Ucapan</span>
        <h2 class="vr-title vr-rv">Ucapan &amp; Doa</h2>
        <p class="vr-sub vr-rv">Tulis ucapan terbaik Anda — semua tamu dapat melihat siapa saja yang telah memberikan doa.</p>

        {{-- Counter --}}
        <div class="vr-gb-counter vr-rv">
            <span class="vr-gb-counter-num" id="vr-gb-count">{{ $gbEntries->count() }}</span>
            <span class="vr-gb-counter-lbl">ucapan telah dikirim</span>
        </div>

        {{-- Form --}}
        <form id="vr-gb-form" class="vr-gb-form vr-rv"
              data-action="{{ url('/' . $wedding->slug . '/guestbook') }}">
            @csrf
            <div class="vr-gb-name-row">
                <div class="vr-gb-av-preview" id="vr-gb-av-preview">{{ strtoupper(mb_substr($guest->guest_name ?? 'T', 0, 1)) }}</div>
                <input type="text" name="name" id="vr-gb-name" placeholder="Nama Anda" maxlength="100" required
                       value="{{ $guest->guest_name ?? '' }}"{{ !empty($guest->guest_name) ? ' readonly' : '' }}>
            </div>
            <div class="vr-gb-msg-wrap">
                <textarea name="message" id="vr-gb-msg" rows="3" placeholder="Tulis ucapan &amp; doa terbaik Anda…" maxlength="500" required></textarea>
                <div class="vr-gb-char"><span id="vr-gb-charcount">0</span>/500</div>
            </div>
            <button type="submit" id="vr-gb-btn" class="vr-gb-submit">Kirim Ucapan ✦</button>
        </form>

        <div id="vr-gb-done" class="vr-gb-done">
            ✦ Terima kasih, {{ $guest->guest_name ?? 'Anda' }}! Ucapan telah kami terima.
        </div>

        {{-- Live header + new badge --}}
        <div class="vr-gb-live-hdr vr-rv">
            <span class="vr-gb-live-dot"></span>
            Live &middot; Ucapan Para Tamu
            <span id="vr-gb-new-badge" style="display:none;"></span>
        </div>

        {{-- Entry list --}}
        <div class="vr-gb-list" id="vr-gb-list">
            @forelse($gbEntries->take(50) as $entry)
            @php $gbInitial = strtoupper(mb_substr($entry->name, 0, 1)); @endphp
            <div class="vr-gb-item vr-gb-in" data-id="{{ $entry->id }}">
                <div class="vr-gb-av">{{ $gbInitial }}</div>
                <div class="vr-gb-body">
                    <div class="vr-gb-item-name">{{ $entry->name }}</div>
                    <div class="vr-gb-item-msg">{{ $entry->message }}</div>
                    <div class="vr-gb-item-time">{{ $entry->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div class="vr-gb-empty" id="vr-gb-empty">Jadilah yang pertama mengucapkan selamat ✦</div>
            @endforelse
        </div>
    </section>
    @endif

    {{-- ═══ SEPARATOR ═══ --}}
    <div class="vr-sep" aria-hidden="true"><span class="vr-sep-icon">✦</span></div>

    {{-- ═══ RSVP ═══ --}}
    <section class="vr-sec" id="rsvp">
        <div class="vr-rsvp-inner">
            <span class="vr-label vr-rv">Konfirmasi Kehadiran</span>
            <h2 class="vr-title vr-rv">RSVP</h2>
            <div class="vr-divider vr-rv"><span>✦</span></div>
            <p class="vr-sub vr-rv">Mohon konfirmasi kehadiran Anda agar kami dapat mempersiapkan segalanya dengan sepenuh hati.</p>

            @if($guest)
            @php
                $alreadyReplied = !empty($guest->replied_at);
                $hadirLabel = match(true) {
                    $guest->is_attending === true  => '✓ Hadir',
                    $guest->is_attending === false => '✕ Tidak Hadir',
                    default                        => '? Belum Pasti',
                };
                $hadirColor = match(true) {
                    $guest->is_attending === true  => '#4ade80',
                    $guest->is_attending === false => '#f87171',
                    default                        => 'var(--txt-dim)',
                };
                $prevMsg = trim(str_replace('[RSVP]', '', $guest->notes ?? ''));
            @endphp

            {{-- Form: hanya tampil jika BELUM pernah submit --}}
            <form class="vr-rsvp-form vr-rv" id="vr-rsvp-form"
                  data-action="{{ route('rsvp.store', $wedding->slug) }}"
                  @if($alreadyReplied) style="display:none;" @endif>
                @csrf
                <div>
                    <label>Nama</label>
                    <input type="text" name="name" value="{{ $guest->guest_name }}" required>
                </div>
                <div>
                    <label>Konfirmasi Kehadiran</label>
                    <div class="vr-hadir-group">
                        <input type="radio" name="vr_hadir" id="vr-h-ya"      value="hadir">
                        <label for="vr-h-ya">✓ &nbsp; Hadir</label>
                        <input type="radio" name="vr_hadir" id="vr-h-tidak"   value="tidak_hadir">
                        <label for="vr-h-tidak">✕ &nbsp; Tidak Hadir</label>
                        <input type="radio" name="vr_hadir" id="vr-h-mungkin" value="mungkin" checked>
                        <label for="vr-h-mungkin">? &nbsp; Belum Pasti</label>
                    </div>
                </div>
                <div id="vr-row-jml" style="display:none;">
                    <label>Jumlah Tamu</label>
                    <input type="number" name="jumlah" value="1" min="1" max="10">
                </div>
                <div>
                    <label>Ucapan &amp; Doa</label>
                    <textarea name="pesan" rows="3" placeholder="Tulis ucapan dan doa terbaik Anda…"></textarea>
                </div>
                <button type="submit" class="vr-rsvp-btn">Kirim Konfirmasi ✦</button>
            </form>

            {{-- Terima kasih: tampil otomatis jika sudah reply, atau setelah JS submit --}}
            <div class="vr-rsvp-ok" id="vr-rsvp-ok" @if(!$alreadyReplied) style="display:none;" @endif>
                <span class="vr-ok-icon">◈</span>
                <p>Terima Kasih, {{ $guest->guest_name }}!</p>
                @if($alreadyReplied)
                    <div class="vr-rsvp-summary">
                        <span class="vr-rsvp-summary-badge" style="color:{{ $hadirColor }};">{{ $hadirLabel }}</span>
                        @if($guest->pax && $guest->is_attending)
                            &nbsp;·&nbsp; {{ $guest->pax }} orang
                        @endif
                        @if($prevMsg)
                            <div class="vr-rsvp-summary-msg">"{{ $prevMsg }}"</div>
                        @endif
                    </div>
                    <small>Konfirmasi Anda sudah kami terima. Sampai jumpa di hari istimewa kami!</small>
                @else
                    <small>Ucapan dan doa Anda telah kami terima. Kami tunggu kehadiran Anda.</small>
                @endif
            </div>
            @else
            <div class="vr-rv" style="margin-top: 36px; font-family:'Cormorant Garamond',serif; font-style:italic; font-size:1.1rem; color:var(--txt-dim); line-height:1.9;">
                Buka undangan ini melalui link personal yang Anda terima untuk konfirmasi kehadiran.
            </div>
            @endif

            {{-- Ucapan dari tamu --}}
            @if(isset($rsvps) && $rsvps->count() > 0)
            <div class="vr-ucapan-list vr-rv">
                @foreach($rsvps->take(20) as $r)
                @php $msg = trim(str_replace('[RSVP]', '', $r->notes ?? '')); @endphp
                @if($msg)
                <div class="vr-ucapan-item">
                    <div class="vr-ucapan-nama">{{ $r->guest_name ?? 'Tamu' }}</div>
                    <span class="vr-ucapan-stat">{{ $r->is_attending ? '✓ Hadir' : '✕ Tidak Hadir' }}</span>
                    <div class="vr-ucapan-msg">{{ $msg }}</div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ═══ TIKET DIGITAL (hanya untuk tamu dengan link personal) ═══ --}}
    @if($guest && !$isDemo)
    @php
        $ticketGuestUrl = url('/' . $wedding->slug . '?to=' . \Illuminate\Support\Str::slug($guest->guest_name));
        $ticketQrSmall  = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&ecc=H&margin=4&data=' . urlencode($ticketGuestUrl);
        $ticketQrLarge  = 'https://api.qrserver.com/v1/create-qr-code/?size=600x600&ecc=H&margin=8&data=' . urlencode($ticketGuestUrl);
    @endphp
    <section class="vr-sec" id="tiket" style="padding:70px 24px;">
        <span class="vr-label vr-rv">Akses Venue</span>
        <h2 class="vr-title vr-rv">Tiket Digital</h2>
        <p class="vr-sub vr-rv">Tunjukkan atau biarkan panitia scan QR ini saat Anda tiba di venue.</p>

        <div class="vr-ticket vr-rv">
            {{-- Garis dekorasi atas --}}
            <div class="vr-ticket-top">
                <span class="vr-ticket-crown">♛</span>
                <span class="vr-ticket-label">VIP Entry Pass</span>
            </div>

            {{-- Info tamu --}}
            <div class="vr-ticket-names">{{ $brideName }} &amp; {{ $groomName }}</div>
            @if($eventDate)
            <div class="vr-ticket-date">{{ $eventDate->locale('id')->translatedFormat('d F Y') }}</div>
            @endif

            {{-- Garis zigzag pemisah --}}
            <div class="vr-ticket-tear"></div>

            {{-- QR Code --}}
            <div class="vr-ticket-qr-wrap">
                <img id="vr-ticket-qr"
                     src="{{ $ticketQrSmall }}"
                     data-dl="{{ $ticketQrLarge }}"
                     data-name="tiket-{{ \Illuminate\Support\Str::slug($guest->guest_name) }}"
                     alt="QR Tiket {{ $guest->guest_name }}"
                     class="vr-ticket-qr-img">
            </div>

            {{-- Nama tamu --}}
            <div class="vr-ticket-guest">{{ $guest->guest_name }}</div>
            @if($guest->pax)
            <div class="vr-ticket-pax">{{ $guest->pax }} orang</div>
            @endif

            {{-- Tombol --}}
            <div class="vr-ticket-actions">
                <button class="vr-ticket-btn" onclick="vrDownloadQr(this)" data-dl="{{ $ticketQrLarge }}" data-name="tiket-{{ \Illuminate\Support\Str::slug($guest->guest_name) }}">
                    ↓ &nbsp; Unduh QR
                </button>
                <button class="vr-ticket-btn vr-ticket-btn-sec" onclick="vrShareTicket()" id="vr-share-btn" style="display:none;">
                    ↗ &nbsp; Bagikan
                </button>
            </div>
            <div class="vr-ticket-hint">Simpan screenshot halaman ini sebagai tiket Anda</div>
        </div>
    </section>
    @endif

    {{-- ═══ FOOTER ═══ --}}
    <footer class="vr-footer">
        <div class="vr-divider" style="margin-bottom: 28px;"><span>♛</span></div>
        <div class="vr-footer-names">
            {{ $brideName }} &amp; {{ $groomName }}
        </div>
        @if($eventDate)
        <div class="vr-footer-note">
            {{ $eventDate->locale('id')->translatedFormat('l, d F Y') }}<br>
            Dengan penuh cinta dan kebahagiaan
        </div>
        @endif
        <div class="vr-divider" style="margin-top: 28px;"><span>✦</span></div>
        <div class="vr-footer-credit">Wedding Invitation &mdash; TretanInvite {{ date('Y') }}</div>
    </footer>

</div>{{-- /#vr-main --}}

{{-- LIGHT/DARK TOGGLE --}}
<button id="vr-theme-btn" onclick="vrToggleTheme()" title="Toggle Light/Dark Mode">☀️</button>

{{-- MUSIC BUTTON --}}
@if($musicUrl || $ytVideoId || $demoMusicOnly)
<button class="vr-music-btn" id="vr-music-btn" onclick="vrToggleMusic()" title="Putar / Pause Musik"
    style="{{ $demoMusicOnly ? 'display:flex;opacity:.45;cursor:default;' : (($musicUrl || $ytVideoId) ? 'display:flex;' : 'display:none;') }}">♪</button>
@endif

{{-- Hidden YouTube player (audio-only, 144p) --}}
@if($ytVideoId)
<div id="vr-yt-player-wrap" style="position:fixed;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;top:-9999px;left:-9999px;" aria-hidden="true">
    <div id="vr-yt-player"></div>
</div>
@endif

@endsection

@push('scripts')
<script>
window.vrEventDate = '{{ $eventDate ? $eventDate->format("Y-m-d") : "" }}';
window.vrMusicUrl  = '{{ $musicUrl ?? "" }}';
window.vrYtVideoId = '{{ $ytVideoId ?? "" }}';
</script>
<script src="{{ asset('js/vip-royal.js') }}" defer></script>
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
<script>
(function(){
    var btn = document.getElementById('vr-theme-btn');
    // Default is dark — light mode is the alternative
    var isLight = localStorage.getItem('vr-theme') === 'light';
    function apply(light) {
        document.body.classList.toggle('vr-light', light);
        if (btn) btn.textContent = light ? '\uD83C\uDF19' : '\u2600\uFE0F';
    }
    apply(isLight);
    window.vrToggleTheme = function() {
        isLight = !isLight;
        apply(isLight);
        localStorage.setItem('vr-theme', isLight ? 'light' : 'dark');
    };
})();
</script>
@if($showGuestbook)
<script>
(function () {
    /* ── Avatar colours (deterministic from name) ── */
    var GB_COLORS = ['#c9a84c','#9b6de3','#a07cde','#d4a843','#8056cc','#c4983c','#7c5cc4','#b8972a','#6e4fbf'];
    function gbColor(name) {
        var h = 0;
        for (var i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) & 0xFFFF;
        return GB_COLORS[h % GB_COLORS.length];
    }

    /* ── Initial state from server ── */
    var gbLastId    = {{ $gbEntries->count() > 0 ? $gbEntries->max('id') : 0 }};
    var gbTotal     = {{ $gbEntries->count() }};
    var gbSubmitted = false;
    var gbPollUrl   = '{{ url("/" . $wedding->slug . "/guestbook") }}';
    var gbCsrf      = document.querySelector('meta[name="csrf-token"]');

    /* ── Colour existing items ── */
    document.querySelectorAll('.vr-gb-item[data-id]').forEach(function (item) {
        var av  = item.querySelector('.vr-gb-av');
        var nm  = item.querySelector('.vr-gb-item-name');
        if (av && nm) av.style.background = gbColor(nm.textContent.trim());
    });

    /* ── Avatar preview on name input ── */
    var nameEl  = document.getElementById('vr-gb-name');
    var avPrev  = document.getElementById('vr-gb-av-preview');
    if (nameEl && avPrev) {
        avPrev.style.background = gbColor(nameEl.value || 'T');
        nameEl.addEventListener('input', function () {
            var v = nameEl.value || 'T';
            avPrev.textContent  = v.charAt(0).toUpperCase();
            avPrev.style.background = gbColor(v);
        });
    }

    /* ── Char counter ── */
    var msgEl  = document.getElementById('vr-gb-msg');
    var cntEl  = document.getElementById('vr-gb-charcount');
    if (msgEl && cntEl) {
        msgEl.addEventListener('input', function () { cntEl.textContent = msgEl.value.length; });
    }

    /* ── Escape HTML ── */
    function esc(s) {
        return (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    /* ── Relative time (Bahasa Indonesia) ── */
    function relTime(iso) {
        var sec = Math.floor((Date.now() - new Date(iso).getTime()) / 1000);
        if (sec < 10)    return 'baru saja';
        if (sec < 60)    return sec + ' detik lalu';
        if (sec < 3600)  return Math.floor(sec / 60) + ' menit lalu';
        if (sec < 86400) return Math.floor(sec / 3600) + ' jam lalu';
        return Math.floor(sec / 86400) + ' hari lalu';
    }

    /* ── Update counter display ── */
    function setCount(n) {
        var el = document.getElementById('vr-gb-count');
        if (el) el.textContent = n;
    }

    /* ── Render a single entry card ── */
    function renderEntry(e, prepend) {
        var initial = (e.name || '?').charAt(0).toUpperCase();
        var color   = gbColor(e.name || '?');
        var div     = document.createElement('div');
        div.className    = 'vr-gb-item vr-gb-new';
        div.dataset.id   = e.id;
        div.innerHTML    =
            '<div class="vr-gb-av" style="background:' + color + '">' + initial + '</div>' +
            '<div class="vr-gb-body">' +
              '<div class="vr-gb-item-name">' + esc(e.name) + '</div>' +
              '<div class="vr-gb-item-msg">'  + esc(e.message) + '</div>' +
              '<div class="vr-gb-item-time">' + relTime(e.created_at) + '</div>' +
            '</div>';

        var list   = document.getElementById('vr-gb-list');
        var emptyEl = document.getElementById('vr-gb-empty');
        if (emptyEl) emptyEl.remove();
        if (prepend && list.firstChild) list.insertBefore(div, list.firstChild);
        else list.appendChild(div);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () { div.classList.add('vr-gb-in'); });
        });
    }

    /* ── AJAX form submit ── */
    var form = document.getElementById('vr-gb-form');
    var btn  = document.getElementById('vr-gb-btn');
    var done = document.getElementById('vr-gb-done');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (gbSubmitted) return;  // hard guard — prevents any double-fire
            gbSubmitted  = true;      // set IMMEDIATELY, before async
            btn.disabled    = true;
            btn.textContent = 'Mengirim…';

            var fd = new FormData(form);
            fetch(form.dataset.action, {
                method:  'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': fd.get('_token') },
                body:    fd,
            })
            .then(function (res) {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(function (data) {
                form.style.display = 'none';
                done.style.display = 'block';
                if (data.entry) {
                    renderEntry(data.entry, true);
                    gbLastId = Math.max(gbLastId, data.entry.id);
                    gbTotal++;
                    setCount(gbTotal);
                }
            })
            .catch(function () {
                // Even on error, keep guard up — tell user to refresh
                form.style.display = 'none';
                done.style.display = 'block';
            });
        });
    }

    /* ── Polling for new entries ── */
    var gbNewCount = 0;
    function gbPoll() {
        fetch(gbPollUrl + '?after=' + gbLastId, { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.ok ? r.json() : []; })
        .then(function (entries) {
            if (!Array.isArray(entries) || entries.length === 0) return;
            entries.sort(function (a, b) { return a.id - b.id; });
            entries.forEach(function (entry) {
                renderEntry(entry, true);
                gbLastId = Math.max(gbLastId, entry.id);
                gbTotal++;
            });
            setCount(gbTotal);
            if (!gbSubmitted) {
                gbNewCount += entries.length;
                var badge = document.getElementById('vr-gb-new-badge');
                if (badge) {
                    badge.textContent     = '+' + gbNewCount + ' ucapan baru';
                    badge.style.display   = 'inline-block';
                    badge.onclick         = function () {
                        badge.style.display = 'none';
                        gbNewCount = 0;
                        document.getElementById('vr-gb-list').scrollIntoView({ behavior: 'smooth' });
                    };
                }
            }
        })
        .catch(function () { /* silent */ });
    }

    /* Start polling after 10s, then every 8s */
    setTimeout(function () { gbPoll(); setInterval(gbPoll, 8000); }, 10000);
})();
</script>
@endif

{{-- Tiket Digital: download + share --}}
@if($guest && !$isDemo)
<script>
/* ── Download QR (fetch → blob → anchor) ── */
function vrDownloadQr(btn) {
    var dlUrl  = btn.dataset.dl;
    var name   = (btn.dataset.name || 'tiket') + '.png';
    btn.textContent = 'Mengunduh…';
    btn.disabled    = true;
    fetch(dlUrl)
        .then(function (r) { return r.blob(); })
        .then(function (blob) {
            var a   = document.createElement('a');
            a.href  = URL.createObjectURL(blob);
            a.download = name;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(function () { URL.revokeObjectURL(a.href); }, 5000);
            btn.textContent = '✓ Tersimpan';
            setTimeout(function () { btn.textContent = '↓  Unduh QR'; btn.disabled = false; }, 2500);
        })
        .catch(function () {
            /* Fallback: buka gambar di tab baru agar user bisa long-press save */
            window.open(dlUrl, '_blank');
            btn.textContent = '↓  Unduh QR';
            btn.disabled    = false;
        });
}

/* ── Web Share API ── */
function vrShareTicket() {
    if (navigator.share) {
        navigator.share({
            title: 'Tiket Undangan — {{ $guest->guest_name }}',
            text:  'Tiket digital saya untuk pernikahan {{ $brideName }} & {{ $groomName }}',
            url:   window.location.href,
        }).catch(function () {});
    }
}

/* Tampilkan tombol share jika browser support */
document.addEventListener('DOMContentLoaded', function () {
    if (navigator.share) {
        var shareBtn = document.getElementById('vr-share-btn');
        if (shareBtn) shareBtn.style.display = '';
    }
});
</script>
@endif
@endpush
