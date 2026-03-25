@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=Jost:wght@200;300;400;500&family=Great+Vibes&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vip-patisserie.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
<style>
/* ── Guestbook inline ── */
.vp-gb-form { max-width: 520px; margin: 32px auto 0; display: flex; flex-direction: column; gap: 14px; text-align: left; }
.vp-gb-name-row { display: flex; align-items: center; gap: 12px; }
.vp-gb-name-row input { flex: 1; }
.vp-gb-submit { align-self: center; padding:12px 32px; border:1px solid rgba(244,167,185,.4); border-radius:999px; background:linear-gradient(135deg,#f4a7b9,#e07a94); color:#fff; font-family:'Jost',sans-serif; font-size:.6rem; letter-spacing:.32em; text-transform:uppercase; cursor:pointer; transition:all .3s; box-shadow:0 4px 20px rgba(244,167,185,.3); }
.vp-gb-submit:hover { transform:translateY(-1px); box-shadow:0 8px 32px rgba(244,167,185,.45); }
.vp-gb-done { text-align:center; font-style:italic; font-family:'Cormorant Garamond',serif; font-size:1.1rem; color:#e07a94; padding:24px; display:none; }
.vp-gb-live-hdr { display:flex; align-items:center; gap:8px; margin:44px auto 20px; font-size:.55rem; letter-spacing:.35em; text-transform:uppercase; color:rgba(61,43,31,.4); max-width:580px; }
#vp-gb-new-badge { background:#f4a7b9; color:#fff; font-size:.62rem; padding:2px 10px; border-radius:20px; letter-spacing:.05em; cursor:pointer; }
</style>
@endpush

@php
    /* ── Demo data for preview mode ── */
    $isDemo       = !empty($isPreview);
    $demoPhotos   = $demoPhotos ?? [];

    $brideName = $wedding->bride_name ?? 'Annisa Putri';
    $groomName = $wedding->groom_name ?? 'Rizky Pratama';
    $eventDate = $wedding->event_date ?? now()->addDays(60);
    $location  = $wedding->location   ?? 'Grand Ballroom, Hotel Mulia Jakarta';

    /* Cover photo */
    $coverPhoto = null;
    if (!empty($wedding->cover_photo))      $coverPhoto = asset('storage/' . $wedding->cover_photo);
    elseif (!empty($wedding->bride_photo))  $coverPhoto = asset('storage/' . $wedding->bride_photo);

    /* Section bg photos */
    $bgMempelai = !empty($wedding->bg_mempelai_photo) ? asset('storage/' . $wedding->bg_mempelai_photo) : null;
    $bgAcara    = !empty($wedding->bg_acara_photo)    ? asset('storage/' . $wedding->bg_acara_photo)    : null;
    $bgLokasi   = !empty($wedding->bg_lokasi_photo)   ? asset('storage/' . $wedding->bg_lokasi_photo)   : null;

    /* Video embed */
    $videoEmbed = null;
    if (!empty($wedding->video_url)) {
        $v = $wedding->video_url;
        if (preg_match('/youtu\.be\/([^?]+)/', $v, $m) || preg_match('/youtube\.com\/watch\?v=([^&]+)/', $v, $m)) {
            $videoEmbed = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $v, $m)) {
            $videoEmbed = 'https://player.vimeo.com/video/' . $m[1];
        }
    }

    /* Extra events */
    $extraEvents = is_array($wedding->extra_events ?? null) ? $wedding->extra_events : [];
    if ($isDemo) {
        $extraEvents = [
            ['label' => 'Siraman', 'date' => $eventDate->clone()->subDay(1)->locale('id')->translatedFormat('l, d F Y'), 'time' => '09.00 WIB', 'location' => 'Kediaman Keluarga Mempelai Wanita'],
        ];
    }

    /* Gallery */
    $gallPhotos = [];
    if ($isDemo && !empty($demoPhotos)) {
        $gallPhotos = $demoPhotos;
    } elseif (($wedding->galleries ?? collect())->count() > 0) {
        $gallPhotos = $wedding->galleries->map(fn($g) => asset('storage/' . $g->path))->toArray();
    }

    /* Music */
    $musicUrl = null; $ytVideoId = null; $rawMusicUrl = null;
    if (!empty($wedding->music_file))  $rawMusicUrl = asset('storage/' . $wedding->music_file);
    elseif (!empty($wedding->music_url)) $rawMusicUrl = $wedding->music_url;
    if ($rawMusicUrl) {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $rawMusicUrl, $ytm)) {
            $ytVideoId = $ytm[1];
        } else {
            $musicUrl = $rawMusicUrl;
        }
    }
    $demoMusicOnly = $isDemo && !$musicUrl && !$ytVideoId;

    /* Guestbook */
    $showGuestbook = $wedding->isVip() && $wedding->guestbook_enabled;
    $gbEntries     = $guestbookEntries ?? collect();

    /* Couple photos */
    $bridePhotoUrl = !empty($wedding->bride_photo) ? asset('storage/' . $wedding->bride_photo) : null;
    $groomPhotoUrl = !empty($wedding->groom_photo) ? asset('storage/' . $wedding->groom_photo) : null;
@endphp

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- ═══ PETAL CANVAS ═══ --}}
<canvas id="vp-petals" aria-hidden="true"></canvas>

{{-- ══════════════════════════════════════════════════════
     COVER
══════════════════════════════════════════════════════ --}}
<div id="vp-cover" onclick="vpOpenInvitation()" role="button" aria-label="Buka Undangan">

    {{-- Background --}}
    <div class="vp-cover-bg"
        @if($coverPhoto) style="background-image:url('{{ $coverPhoto }}');"
        @else style="background:linear-gradient(160deg,#fce4ec,#ede8f8,#e0f5f3);" @endif></div>
    <div class="vp-cover-vignette"></div>

    {{-- Floating petals --}}
    @php
        $petalPositions = [
            ['2rem','3rem','2.5s',.1],['90%','6rem','3.2s',.08],
            ['12%','75%','4.0s',.12],['78%','80%','2.8s',.09],
            ['50%','15%','3.6s',.07],['35%','88%','3.0s',.1],
            ['65%','50%','4.4s',.06],['8%','40%','2.6s',.09],
        ];
        $petalEmojis = ['🌸','🌷','🌺','🌼','✿'];
    @endphp
    @foreach($petalPositions as $i => $pp)
    <div class="vp-cover-petal"
         style="left:{{ $pp[0] }};top:{{ $pp[1] }};animation-duration:{{ $pp[2] }};animation-delay:{{ $i * .45 }}s;opacity:{{ $pp[3] }};">
        {{ $petalEmojis[$i % count($petalEmojis)] }}
    </div>
    @endforeach

    <div class="vp-cover-box">
        {{-- Wax seal --}}
        <span class="vp-cover-seal">🌸</span>

        <div class="vp-cover-eyebrow">Undangan Pernikahan</div>

        @if(!empty($guest))
        <div style="margin-bottom:20px;">
            <div class="vp-cover-kepada-lbl">Kepada Yang Terhormat</div>
            <div class="vp-cover-guest-name">{{ $guest->guest_name }}</div>
        </div>
        @endif

        <div class="vp-cover-deco"><span>✿</span><span>✿</span><span>✿</span></div>

        <div class="vp-cover-names">
            {{ $brideName }}
            <span class="vp-cover-amp">&amp;</span>
            {{ $groomName }}
        </div>

        @if($eventDate)
        <div class="vp-cover-date">{{ $eventDate->locale('id')->translatedFormat('d F Y') }}</div>
        @endif

        <button class="vp-cover-btn" type="button">
            🌸 &nbsp; Buka Undangan
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MAIN
══════════════════════════════════════════════════════ --}}
<div id="vp-main">

    {{-- NAV --}}
    <nav class="vp-nav" id="vp-nav" aria-label="Navigasi">
        <a href="#mempelai">Mempelai</a>
        <a href="#acara">Acara</a>
        @if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)<a href="#galeri">Galeri</a>@endif
        <a href="#lokasi">Lokasi</a>
        @php $hasBankInfo = (!empty($wedding->bride_bank) || !empty($wedding->groom_bank) || ($wedding->bank_accounts ?? collect())->count() > 0); @endphp
        @if($hasBankInfo)<a href="#amplop">Amplop</a>@endif
        @if($showGuestbook)<a href="#ucapan">Ucapan</a>@endif
        <a href="#rsvp">RSVP</a>
        @if($guest && !$isDemo)<a href="#tiket">Tiket</a>@endif
    </nav>

    {{-- ══ HERO ══ --}}
    <section id="vp-hero">
        <div class="vp-hero-bg"></div>

        {{-- Floral corner decorations --}}
        <span class="vp-hero-corner tl">🌸</span>
        <span class="vp-hero-corner tr">🌷</span>
        <span class="vp-hero-corner bl">🌺</span>
        <span class="vp-hero-corner br">🌼</span>

        {{-- Overlapping couple frames --}}
        <div class="vp-hero-frames vp-rv">
            <div class="vp-hero-frame bride">
                @if($bridePhotoUrl)
                    <img src="{{ $bridePhotoUrl }}" alt="{{ $brideName }}" loading="eager">
                @else
                    🌸
                @endif
            </div>
            <div class="vp-hero-amp-badge">&amp;</div>
            <div class="vp-hero-frame groom">
                @if($groomPhotoUrl)
                    <img src="{{ $groomPhotoUrl }}" alt="{{ $groomName }}" loading="eager">
                @elseif($bridePhotoUrl)
                    <img src="{{ $bridePhotoUrl }}" alt="{{ $groomName }}" loading="eager">
                @else
                    🤵
                @endif
            </div>
        </div>

        <div class="vp-hero-eyebrow vp-rv">Undangan Pernikahan</div>

        <div class="vp-hero-names vp-rv">
            {{ $brideName }}
            <span class="vp-hero-amp">&amp;</span>
            {{ $groomName }}
        </div>

        @if($eventDate)
        <div class="vp-hero-date-wrap vp-rv">
            <div class="vp-hero-date-line"></div>
            <div class="vp-hero-date">{{ $eventDate->locale('id')->translatedFormat('l, d F Y') }}</div>
            <div class="vp-hero-date-line r"></div>
        </div>
        @endif

        <div class="vp-hero-scroll vp-rv">✿ &nbsp; Gulir ke bawah</div>
    </section>

    {{-- ══ OPENING TEXT ══ --}}
    @if(!empty($wedding->opening_text))
    <section class="vp-sec vp-sec-rose" style="padding:64px 24px;">
        <div class="vp-opening-card vp-rv">
            <p>{{ $wedding->opening_text }}</p>
        </div>
    </section>
    @endif

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">🌸</span></div>

    {{-- ══ MEMPELAI ══ --}}
    <section class="vp-sec vp-sec-rose" id="mempelai" style="max-width:900px;padding-left:0;padding-right:0;position:relative;overflow:hidden;">
        @if(!empty($bgMempelai))
        <div style="position:absolute;inset:0;background-image:url('{{ $bgMempelai }}');background-size:cover;background-position:center;z-index:-1;"></div>
        <div style="position:absolute;inset:0;background:rgba(253,246,239,.88);z-index:-1;"></div>
        @endif

        <div style="text-align:center;padding:0 24px;">
            <div class="vp-rv"><span class="vp-pill">🌸 Yang Berbahagia</span></div>
            <h2 class="vp-title vp-rv">Mempelai</h2>
            <p class="vp-sub vp-rv">Dengan penuh syukur dan kebahagiaan, kami mengumumkan jalinan cinta kami</p>
        </div>

        <div class="vp-couple-grid" style="padding:0 24px;">
            {{-- Mempelai Wanita --}}
            <div class="vp-couple-card vp-rv">
                <div class="vp-couple-photo">
                    @if($bridePhotoUrl)
                        <img src="{{ $bridePhotoUrl }}" alt="{{ $brideName }}">
                    @else 🌸 @endif
                </div>
                <div class="vp-couple-role">Mempelai Wanita</div>
                <div class="vp-couple-name">{{ $brideName }}</div>
                @if(!empty($wedding->bride_fullname))
                    <div class="vp-couple-full">{{ $wedding->bride_fullname }}</div>
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
                    <div class="vp-couple-par">Putri dari<br><strong>{{ $_brideParentLine }}</strong></div>
                @endif
                @if(!empty($wedding->bride_wa))
                <div class="vp-couple-social-row">
                    <a href="https://wa.me/62{{ ltrim($wedding->bride_wa,'0') }}" target="_blank" rel="noopener" class="vp-couple-social vp-couple-wa">💬 WhatsApp</a>
                </div>
                @endif
            </div>

            {{-- Ampersand divider --}}
            <div class="vp-couple-amp vp-rv"><span>&amp;</span></div>

            {{-- Mempelai Pria --}}
            <div class="vp-couple-card vp-rv">
                <div class="vp-couple-photo">
                    @if($groomPhotoUrl)
                        <img src="{{ $groomPhotoUrl }}" alt="{{ $groomName }}">
                    @else 🤵 @endif
                </div>
                <div class="vp-couple-role">Mempelai Pria</div>
                <div class="vp-couple-name">{{ $groomName }}</div>
                @if(!empty($wedding->groom_fullname))
                    <div class="vp-couple-full">{{ $wedding->groom_fullname }}</div>
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
                    <div class="vp-couple-par">Putra dari<br><strong>{{ $_groomParentLine }}</strong></div>
                @endif
                @if(!empty($wedding->groom_wa))
                <div class="vp-couple-social-row">
                    <a href="https://wa.me/62{{ ltrim($wedding->groom_wa,'0') }}" target="_blank" rel="noopener" class="vp-couple-social vp-couple-wa">💬 WhatsApp</a>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ══ COUPLE PHOTO FULLWIDTH ══ --}}
    @if(!empty($wedding->couple_photo))
    <div class="vp-couple-photo-full">
        <img src="{{ asset('storage/'.$wedding->couple_photo) }}" alt="Foto bersama {{ $brideName }} &amp; {{ $groomName }}" loading="lazy">
        <div class="vp-couple-photo-full-overlay"></div>
        <div class="vp-couple-photo-full-caption">{{ $brideName }} &amp; {{ $groomName }}</div>
    </div>
    @endif

    {{-- ══ VIDEO (VIP) ══ --}}
    @if($videoEmbed || $isDemo)
    <section class="vp-sec vp-sec-lav" style="padding:70px 24px;">
        <div class="vp-rv"><span class="vp-pill">🎬 Momen Spesial</span></div>
        <h2 class="vp-title vp-rv">Video Kami</h2>
        <p class="vp-sub vp-rv">Saksikan perjalanan cinta kami dalam video istimewa ini.</p>
        <div class="vp-video-wrap vp-rv">
            @if($videoEmbed)
                <iframe src="{{ $videoEmbed }}" allowfullscreen allow="autoplay; encrypted-media" title="Video Prewedding"></iframe>
            @else
                <div class="vp-video-placeholder">
                    <span style="font-size:3rem;color:var(--rose);opacity:.3;">▶</span>
                    <span style="font-family:var(--sans);font-size:.6rem;letter-spacing:.4em;color:var(--rose-d);opacity:.4;text-transform:uppercase;">Video Prewedding</span>
                </div>
            @endif
        </div>
    </section>
    @endif

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">✿</span></div>

    {{-- ══ COUNTDOWN ══ --}}
    @if($eventDate)
    <section class="vp-sec vp-sec-gold" id="countdown">
        <div class="vp-rv"><span class="vp-pill">⏳ Menghitung Hari</span></div>
        <h2 class="vp-title vp-rv">Hitung Mundur</h2>
        <p class="vp-sub vp-rv">Waktu terus berjalan menuju momen paling bahagia kami</p>
        <div class="vp-cd-wrap vp-rv">
            <div class="vp-cd-item">
                <span class="vp-cd-num" id="vp-hari">--</span>
                <span class="vp-cd-lbl">Hari</span>
            </div>
            <div class="vp-cd-sep">:</div>
            <div class="vp-cd-item">
                <span class="vp-cd-num" id="vp-jam">--</span>
                <span class="vp-cd-lbl">Jam</span>
            </div>
            <div class="vp-cd-sep">:</div>
            <div class="vp-cd-item">
                <span class="vp-cd-num" id="vp-menit">--</span>
                <span class="vp-cd-lbl">Menit</span>
            </div>
            <div class="vp-cd-sep">:</div>
            <div class="vp-cd-item">
                <span class="vp-cd-num" id="vp-detik">--</span>
                <span class="vp-cd-lbl">Detik</span>
            </div>
        </div>
    </section>
    @endif

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">🌸</span></div>

    {{-- ══ ACARA ══ --}}
    <section class="vp-sec vp-sec-rose" id="acara" style="position:relative;overflow:hidden;">
        @if(!empty($bgAcara))
        <div style="position:absolute;inset:0;background-image:url('{{ $bgAcara }}');background-size:cover;background-position:center;z-index:-1;"></div>
        <div style="position:absolute;inset:0;background:rgba(253,246,239,.9);z-index:-1;"></div>
        @endif

        <div class="vp-rv"><span class="vp-pill">📜 Rangkaian Acara</span></div>
        <h2 class="vp-title vp-rv">Hari Istimewa</h2>
        <p class="vp-sub vp-rv">Dengan penuh kebahagiaan kami mengundang Anda hadir bersama kami</p>

        @php
            $allEvents = [];

            $akadDate = $wedding->akad_date ?? $eventDate;
            if ($akadDate) {
                $allEvents[] = [
                    'type'     => 'akad',
                    'sort_ts'  => \Carbon\Carbon::parse($akadDate)->timestamp,
                    'icon'     => '🌙',
                    'type_lbl' => 'Akad Nikah',
                    'name'     => 'Ijab Qabul',
                    'date_obj' => \Carbon\Carbon::parse($akadDate),
                    'time'     => $wedding->akad_time ?? '08.00 – 10.00',
                    'location' => $wedding->akad_location ?? $location,
                    'is_extra' => false,
                ];
            }

            $resepsiDate = $wedding->reception_date ?? $eventDate;
            if ($resepsiDate) {
                $allEvents[] = [
                    'type'     => 'resepsi',
                    'sort_ts'  => \Carbon\Carbon::parse($resepsiDate)->timestamp,
                    'icon'     => '🌸',
                    'type_lbl' => 'Resepsi',
                    'name'     => $wedding->customText('event_name', 'Walimatul Ursy'),
                    'date_obj' => \Carbon\Carbon::parse($resepsiDate),
                    'time'     => $wedding->reception_time ?? '11.00 – 14.00',
                    'location' => $wedding->reception_location ?? $location,
                    'is_extra' => false,
                ];
            }

            foreach ($extraEvents as $extra) {
                if (empty($extra['label'])) continue;
                $extraTs = null;
                if (!empty($extra['date'])) {
                    try { $extraTs = \Carbon\Carbon::parse($extra['date'])->timestamp; } catch (\Exception $e) {}
                }
                $allEvents[] = [
                    'type'     => 'extra',
                    'sort_ts'  => $extraTs ?? PHP_INT_MAX,
                    'icon'     => '🌷',
                    'type_lbl' => 'Rangkaian Acara',
                    'name'     => $extra['label'],
                    'date_obj' => !empty($extra['date']) ? (function() use ($extra) { try { return \Carbon\Carbon::parse($extra['date']); } catch (\Exception $e) { return null; } })() : null,
                    'date_raw' => $extra['date'] ?? null,
                    'time'     => $extra['time'] ?? null,
                    'location' => $extra['location'] ?? null,
                    'is_extra' => true,
                ];
            }

            usort($allEvents, fn($a, $b) => $a['sort_ts'] <=> $b['sort_ts']);
        @endphp

        <div class="vp-event-grid vp-rv">
            @foreach($allEvents as $ev)
            <div class="vp-event-card {{ $ev['is_extra'] ? 'vp-extra' : '' }}">
                <span class="vp-event-icon">{{ $ev['icon'] }}</span>
                <div class="vp-event-type">{{ $ev['type_lbl'] }}</div>
                <div class="vp-event-name">{{ $ev['name'] }}</div>

                @if(isset($ev['date_obj']) || !empty($ev['time']))
                <div class="vp-event-row">
                    <span class="vp-event-row-dot">✿</span>
                    <div>
                        @if(isset($ev['date_obj']) && $ev['date_obj'])
                            <strong>{{ $ev['date_obj']->locale('id')->translatedFormat('l, d F Y') }}</strong>
                        @elseif(!empty($ev['date_raw']))
                            <strong>{{ $ev['date_raw'] }}</strong>
                        @endif
                        @if(!empty($ev['time']))
                            &nbsp; {{ trim(preg_replace('/\s*wib\s*$/i', '', $ev['time'])) }} WIB
                        @endif
                    </div>
                </div>
                @endif

                @if(!empty($ev['location']))
                <div class="vp-event-row">
                    <span class="vp-event-row-dot">📍</span>
                    <div>{{ $ev['location'] }}</div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Dresscode --}}
        @if(!empty($wedding->dresscode))
        <div class="vp-rv" style="margin-top:32px;display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;">
            <span class="vp-dresscode-badge">👗 &nbsp; Dresscode &nbsp;·&nbsp; <em>{{ $wedding->dresscode }}</em></span>
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
        <div style="margin-top:28px;">
            <a href="{{ $_calUrl }}" target="_blank" rel="noopener" class="vp-cal-btn vp-rv">
                📅 &nbsp; Tambah ke Google Calendar
            </a>
        </div>
        @endif
    </section>

    {{-- ══ GALERI ══ --}}
    @if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)
    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">✿</span></div>
    <section class="vp-gallery-sec" id="galeri">
        <div class="vp-gallery-header">
            <div class="vp-rv"><span class="vp-pill">📷 Momen Berharga</span></div>
            <h2 class="vp-title vp-rv">Galeri Foto</h2>
            <p class="vp-sub vp-rv" style="margin-top:6px;">Klik foto untuk memperbesar</p>
        </div>
        @include('invitation.partials.gallery-vip-royal', [
            'gallPhotos'    => $gallPhotos,
            'bridePhotoUrl' => $bridePhotoUrl ?? null,
            'groomPhotoUrl' => $groomPhotoUrl ?? null,
        ])
    </section>
    @endif

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">🌸</span></div>

    {{-- ══ LOKASI ══ --}}
    <section class="vp-sec vp-sec-mint" id="lokasi" style="position:relative;overflow:hidden;">
        @if(!empty($bgLokasi))
        <div style="position:absolute;inset:0;background-image:url('{{ $bgLokasi }}');background-size:cover;background-position:center;z-index:-1;"></div>
        <div style="position:absolute;inset:0;background:rgba(253,246,239,.88);z-index:-1;"></div>
        @endif
        <div class="vp-rv"><span class="vp-pill">📍 Temukan Kami</span></div>
        <h2 class="vp-title vp-rv">Lokasi Acara</h2>
        @if(!empty($wedding->location))
        <p class="vp-sub vp-rv">{{ $wedding->location }}</p>
        @endif

        <div class="vp-map-wrap vp-rv">
            @if(!empty($wedding->map_embed))
                {!! $wedding->map_embed !!}
            @else
                <div class="vp-map-placeholder">
                    <span style="font-size:2rem;opacity:.35;">🗺️</span>
                    <span style="font-family:var(--sans);font-size:.6rem;letter-spacing:.28em;color:var(--rose-d);opacity:.5;text-transform:uppercase;">{{ $location }}</span>
                </div>
            @endif
        </div>

        @if(!empty($wedding->map_link))
        <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="vp-map-btn vp-rv">
            📍 &nbsp; Buka di Google Maps
        </a>
        @endif
    </section>

    {{-- ══ AMPLOP DIGITAL (VIP) ══ --}}
    @if($hasBankInfo)
    <section class="vp-sec vp-sec-lav" id="amplop">
        <div class="vp-rv"><span class="vp-pill">💝 Hadiah &amp; Doa</span></div>
        <h2 class="vp-title vp-rv">Amplop Digital</h2>
        <p class="vp-sub vp-rv">Kehadiran dan doa restu Anda adalah hadiah terindah. Namun jika ingin berbagi lebih, kami siapkan nomor rekening di bawah ini.</p>

        <div class="vp-gift-grid vp-rv">
            @if(($wedding->bank_accounts ?? collect())->count() > 0)
                @foreach($wedding->bank_accounts as $acc)
                <div class="vp-gift-card">
                    <span class="vp-gift-icon">🌸</span>
                    <div class="vp-gift-bank">{{ $acc->bank_name }}</div>
                    <div class="vp-gift-norek">{{ $acc->account_number }}</div>
                    <div class="vp-gift-atas">a/n {{ $acc->account_name }}</div>
                    <button class="vp-copy-btn" data-copy="{{ $acc->account_number }}">Salin Nomor</button>
                </div>
                @endforeach
            @else
                @if(!empty($wedding->bride_bank))
                <div class="vp-gift-card">
                    <span class="vp-gift-icon">🌸</span>
                    <div class="vp-gift-bank">{{ $wedding->bride_bank }}</div>
                    <div class="vp-gift-norek">{{ $wedding->bride_norek ?? '' }}</div>
                    <div class="vp-gift-atas">a/n {{ $brideName }}</div>
                    <button class="vp-copy-btn" data-copy="{{ $wedding->bride_norek ?? '' }}">Salin Nomor</button>
                </div>
                @endif
                @if(!empty($wedding->groom_bank))
                <div class="vp-gift-card">
                    <span class="vp-gift-icon">🌿</span>
                    <div class="vp-gift-bank">{{ $wedding->groom_bank }}</div>
                    <div class="vp-gift-norek">{{ $wedding->groom_norek ?? '' }}</div>
                    <div class="vp-gift-atas">a/n {{ $groomName }}</div>
                    <button class="vp-copy-btn" data-copy="{{ $wedding->groom_norek ?? '' }}">Salin Nomor</button>
                </div>
                @endif
            @endif
        </div>
    </section>
    @endif

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">✿</span></div>

    {{-- ══ GUESTBOOK REALTIME (VIP) ══ --}}
    @if($showGuestbook)
    <section class="vp-sec vp-sec-rose" id="ucapan">
        <div class="vp-rv"><span class="vp-pill">💌 Titip Ucapan</span></div>
        <h2 class="vp-title vp-rv">Ucapan &amp; Doa</h2>
        <p class="vp-sub vp-rv">Tulis ucapan terbaik Anda — semua tamu yang hadir dapat membacanya bersama</p>

        <div class="vp-gb-counter vp-rv">
            <span class="vp-gb-counter-num" id="vp-gb-count">{{ $gbEntries->count() }}</span>
            <span class="vp-gb-counter-lbl">ucapan telah dikirim</span>
        </div>

        <form id="vp-gb-form" class="vp-gb-form vp-rv"
              data-action="{{ url('/' . $wedding->slug . '/guestbook') }}">
            @csrf
            <div class="vp-gb-name-row">
                <div class="vp-gb-av-preview" id="vp-gb-av">{{ strtoupper(mb_substr($guest->guest_name ?? 'T', 0, 1)) }}</div>
                <input type="text" name="name" id="vp-gb-name" placeholder="Nama Anda" maxlength="100" required
                       value="{{ $guest->guest_name ?? '' }}"{{ !empty($guest->guest_name) ? ' readonly' : '' }}>
            </div>
            <div class="vp-gb-msg-wrap">
                <textarea name="message" id="vp-gb-msg" rows="3" placeholder="Tulis ucapan &amp; doa terbaik Anda…" maxlength="220" required></textarea>
                <span class="vp-gb-char" id="vp-gb-char">0/220</span>
            </div>
            <button type="submit" class="vp-gb-submit">Kirim Ucapan 🌸</button>
        </form>

        <div id="vp-gb-done" class="vp-gb-done">
            🌸 Terima kasih, {{ $guest->guest_name ?? 'Anda' }}! Ucapan telah kami terima.
        </div>

        <div class="vp-gb-live-hdr vp-rv">
            <span class="vp-gb-live-dot"></span>
            Live &middot; Ucapan Para Tamu
            <span id="vp-gb-new-badge" style="display:none;"></span>
        </div>

        <div class="vp-gb-list" id="vp-gb-list">
            @forelse($gbEntries->take(50) as $entry)
            @php $gbInit = strtoupper(mb_substr($entry->name, 0, 1)); @endphp
            <div class="vp-gb-item vp-gb-in" data-id="{{ $entry->id }}">
                <div class="vp-gb-av">{{ $gbInit }}</div>
                <div class="vp-gb-body">
                    <div class="vp-gb-item-name">{{ $entry->name }}</div>
                    <div class="vp-gb-item-msg">{{ $entry->message }}</div>
                    <div class="vp-gb-item-time">{{ $entry->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div class="vp-gb-empty" id="vp-gb-empty">Jadilah yang pertama mengucapkan selamat 🌸</div>
            @endforelse
        </div>
    </section>
    @endif

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">🌸</span></div>

    {{-- ══ RSVP ══ --}}
    <section class="vp-sec vp-sec-lav" id="rsvp">
        <div class="vp-rv"><span class="vp-pill">📬 Konfirmasi Kehadiran</span></div>
        <h2 class="vp-title vp-rv">RSVP</h2>
        <p class="vp-sub vp-rv">Mohon konfirmasi kehadiran Anda agar kami dapat mempersiapkan segalanya dengan sepenuh hati.</p>

        @if($guest)
        @php
            $alreadyReplied = !empty($guest->replied_at);
            $hadirLabel = match(true) {
                $guest->is_attending === true  => '✓ Hadir',
                $guest->is_attending === false => '✕ Tidak Hadir',
                default                        => '? Belum Pasti',
            };
            $hadirColor = match(true) {
                $guest->is_attending === true  => '#3a9e5c',
                $guest->is_attending === false => '#e07a94',
                default                        => 'var(--txt-dim)',
            };
            $prevMsg = trim(str_replace('[RSVP]', '', $guest->notes ?? ''));
        @endphp

        <form class="vp-rsvp-form vp-rv" id="vp-rsvp-form"
              data-action="{{ route('rsvp.store', $wedding->slug) }}"
              @if($alreadyReplied) style="display:none;" @endif>
            @csrf
            <div>
                <label>Nama</label>
                <input type="text" name="name" value="{{ $guest->guest_name }}" required>
            </div>
            <div>
                <label>Konfirmasi Kehadiran</label>
                <div class="vp-hadir-group">
                    <input type="radio" name="vr_hadir" id="vp-h-ya"      value="hadir">
                    <label for="vp-h-ya">✓ &nbsp; Hadir</label>
                    <input type="radio" name="vr_hadir" id="vp-h-tidak"   value="tidak_hadir">
                    <label for="vp-h-tidak">✕ &nbsp; Tidak</label>
                    <input type="radio" name="vr_hadir" id="vp-h-mungkin" value="mungkin" checked>
                    <label for="vp-h-mungkin">? &nbsp; Belum Pasti</label>
                </div>
            </div>
            <div id="vp-row-jml" style="display:none;">
                <label>Jumlah Tamu</label>
                <input type="number" name="jumlah" value="1" min="1" max="10">
            </div>
            <div>
                <label>Ucapan &amp; Doa</label>
                <textarea name="pesan" rows="3" placeholder="Tulis ucapan dan doa terbaik Anda…"></textarea>
            </div>
            <button type="submit" class="vp-rsvp-btn">Kirim Konfirmasi 🌸</button>
        </form>

        <div class="vp-rsvp-ok" id="vp-rsvp-ok" @if(!$alreadyReplied) style="display:none;" @endif>
            <span class="vp-ok-icon">🌸</span>
            <p>Terima Kasih, {{ $guest->guest_name }}!</p>
            @if($alreadyReplied)
                <div id="vp-rsvp-summary" class="vp-rsvp-summary" style="font-family:var(--serif);">
                    <span style="color:{{ $hadirColor }};">{{ $hadirLabel }}</span>
                    @if($guest->pax && $guest->is_attending) &nbsp;·&nbsp; {{ $guest->pax }} orang @endif
                    @if($prevMsg)<div style="font-style:italic;margin-top:8px;opacity:.75;">&ldquo;{{ $prevMsg }}&rdquo;</div>@endif
                </div>
                <small>Konfirmasi Anda sudah kami terima. Sampai jumpa di hari istimewa kami!</small>
            @else
                <small>Ucapan dan doa Anda telah kami terima. Kami tunggu kehadiran Anda.</small>
            @endif
        </div>
        @else
        <div class="vp-rv" style="margin-top:36px;font-family:var(--serif);font-style:italic;font-size:1.1rem;color:var(--txt-dim);line-height:1.9;">
            Buka undangan ini melalui link personal yang Anda terima untuk konfirmasi kehadiran.
        </div>
        @endif

        @if(isset($rsvps) && $rsvps->count() > 0)
        <div class="vp-ucapan-list vp-rv">
            @foreach($rsvps->take(20) as $r)
            @php $rMsg = trim(str_replace('[RSVP]','', $r->notes ?? '')); @endphp
            @if($rMsg)
            <div class="vp-ucapan-item">
                <div class="vp-ucapan-nama">{{ $r->guest_name ?? 'Tamu' }}</div>
                <span class="vp-ucapan-stat">{{ $r->is_attending ? '✓ Hadir' : '✕ Tidak Hadir' }}</span>
                <div class="vp-ucapan-msg">{{ $rMsg }}</div>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </section>

    {{-- ══ TIKET DIGITAL ══ --}}
    @if($guest && !$isDemo)
    @php
        $ticketGuestUrl = url('/' . $wedding->slug . '?to=' . \Illuminate\Support\Str::slug($guest->guest_name));
        $ticketQrSmall  = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&ecc=H&margin=4&data=' . urlencode($ticketGuestUrl);
        $ticketQrLarge  = 'https://api.qrserver.com/v1/create-qr-code/?size=600x600&ecc=H&margin=8&data=' . urlencode($ticketGuestUrl);
    @endphp
    <section class="vp-sec vp-sec-mint" id="tiket" style="padding:70px 24px;">
        <div class="vp-rv"><span class="vp-pill">🎫 Akses Venue</span></div>
        <h2 class="vp-title vp-rv">Tiket Digital</h2>
        <p class="vp-sub vp-rv">Tunjukkan atau biarkan panitia scan QR ini saat Anda tiba di venue.</p>

        <div class="vp-ticket vp-rv">
            <div class="vp-ticket-top">
                <span class="vp-ticket-label">🌸 Tiket Masuk Tamu VIP</span>
            </div>

            <div class="vp-ticket-names">{{ $brideName }} &amp; {{ $groomName }}</div>
            @if($eventDate)
            <div class="vp-ticket-date">{{ $eventDate->locale('id')->translatedFormat('d F Y') }}</div>
            @endif

            <div class="vp-ticket-tear"></div>

            <div class="vp-ticket-qr-wrap">
                <img id="vp-ticket-qr"
                     src="{{ $ticketQrSmall }}"
                     data-dl="{{ $ticketQrLarge }}"
                     data-name="tiket-{{ \Illuminate\Support\Str::slug($guest->guest_name) }}"
                     alt="QR Tiket {{ $guest->guest_name }}"
                     class="vp-ticket-qr-img">
            </div>

            <div class="vp-ticket-guest">{{ $guest->guest_name }}</div>
            @if($guest->pax)
            <div class="vp-ticket-pax">{{ $guest->pax }} orang</div>
            @endif

            <div class="vp-ticket-actions">
                <button class="vp-ticket-btn" id="vp-dl-btn"
                        data-dl="{{ $ticketQrLarge }}"
                        data-name="tiket-{{ \Illuminate\Support\Str::slug($guest->guest_name) }}">
                    ↓ &nbsp; Unduh QR
                </button>
                <button class="vp-ticket-btn vp-ticket-btn-sec" id="vp-share-btn" style="display:none;">
                    ↗ &nbsp; Bagikan
                </button>
            </div>
            <div class="vp-ticket-hint">Simpan screenshot halaman ini sebagai tiket masuk Anda</div>
        </div>
    </section>
    @endif

    {{-- ══ FOOTER ══ --}}
    <footer class="vp-footer">
        <div class="vp-footer-floral">🌸 🌷 🌺 🌼 🌸</div>
        <div class="vp-footer-names">{{ $brideName }} &amp; {{ $groomName }}</div>
        @if($eventDate)
        <div class="vp-footer-note">
            {{ $eventDate->locale('id')->translatedFormat('l, d F Y') }}<br>
            Dengan penuh cinta dan kebahagiaan
        </div>
        @endif
        <div class="vp-floral-div">✿</div>
        <div class="vp-footer-credit">Wedding Invitation · TretanInvite {{ date('Y') }}</div>
    </footer>

</div>{{-- /#vp-main --}}

{{-- THEME TOGGLE --}}
<button id="vp-theme-btn" title="Light / Dark Mode" aria-label="Toggle tema">🌙</button>

{{-- MUSIC BUTTON --}}
<button class="vp-music-btn" id="vp-music-btn"
        title="Putar / Pause Musik"
        aria-label="Musik"
        style="{{ $demoMusicOnly ? 'display:flex;opacity:.45;cursor:default;' : 'display:none;' }}">♩</button>

{{-- Hidden YouTube player --}}
@if($ytVideoId)
<div style="position:fixed;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;top:-9999px;" aria-hidden="true">
    <div id="vp-yt-player"></div>
</div>
@endif

@endsection

@push('scripts')
@if($ytVideoId)
<script src="https://www.youtube.com/iframe_api" async defer></script>
@endif
<script>
window.vpEventDate    = '{{ $eventDate ? $eventDate->format("Y-m-d") : "" }}';
window.vpMusicUrl     = '{{ $musicUrl ?? "" }}';
window.vpYtVideoId    = '{{ $ytVideoId ?? "" }}';
window.vpDemoMusicOnly = {{ $demoMusicOnly ? 'true' : 'false' }};
</script>
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
<script src="{{ asset('js/vip-patisserie.js') }}" defer></script>

{{-- RSVP — jumlah input show/hide --}}
<script>
(function() {
    var radios   = document.querySelectorAll('input[name="vr_hadir"]');
    var rowJml   = document.getElementById('vp-row-jml');
    function chk() {
        var v = document.querySelector('input[name="vr_hadir"]:checked');
        if (rowJml) rowJml.style.display = (v && v.value === 'hadir') ? '' : 'none';
    }
    radios.forEach(function(r) { r.addEventListener('change', chk); });
    chk();

    /* RSVP form submit */
    var form = document.getElementById('vp-rsvp-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn   = form.querySelector('.vp-rsvp-btn');
            var okEl  = document.getElementById('vp-rsvp-ok');
            var data  = new FormData(form);
            var token = (form.querySelector('input[name=_token]') || {}).value || '';
            if (btn) { btn.disabled = true; btn.textContent = '...'; }
            fetch(form.dataset.action, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body:    data,
            })
            .then(function(r) { return r.json(); })
            .then(function(json) {
                if (json && (json.success || json.message)) {
                    form.style.display = 'none';
                    if (okEl) {
                        var sumEl = document.getElementById('vp-rsvp-summary');
                        if (sumEl) {
                            var nm   = data.get('name')     || '';
                            var hd   = data.get('vr_hadir') || '';
                            var jml  = data.get('jumlah')   || '1';
                            var lbl  = hd === 'hadir' ? '✓ Hadir' : hd === 'tidak_hadir' ? '✕ Tidak Hadir' : '? Belum Pasti';
                            sumEl.innerHTML = '<span style="color:var(--rose-d)">' + nm + '</span> &mdash; ' + lbl + (hd === 'hadir' ? ' · ' + jml + ' orang' : '');
                        }
                        okEl.style.display = 'block';
                    }
                } else {
                    alert(json && json.message ? json.message : 'Terjadi kesalahan.');
                    if (btn) { btn.disabled = false; btn.textContent = 'Kirim Konfirmasi 🌸'; }
                }
            })
            .catch(function() {
                alert('Gagal terhubung ke server.');
                if (btn) { btn.disabled = false; btn.textContent = 'Kirim Konfirmasi 🌸'; }
            });
        });
    }
})();
</script>

@if($showGuestbook)
<script>
(function(){
    /* ── Colours (deterministic from name) ── */
    var GB_COLORS=['#f4a7b9','#c5b8e8','#98d4cf','#f5c5a3','#e07a94','#9d8ed0','#65b5af','#d4a373','#c29bcc'];
    function gbColor(name){ var h=0; for(var i=0;i<name.length;i++) h=(h*31+name.charCodeAt(i))&0xFFFF; return GB_COLORS[h%GB_COLORS.length]; }

    var gbLastId  = {{ $gbEntries->count() > 0 ? $gbEntries->max('id') : 0 }};
    var gbTotal   = {{ $gbEntries->count() }};
    var gbSent    = false;
    var gbUrl     = '{{ url("/" . $wedding->slug . "/guestbook") }}';

    /* colour existing items */
    document.querySelectorAll('.vp-gb-item[data-id]').forEach(function(item){
        var av=item.querySelector('.vp-gb-av');
        var nm=item.querySelector('.vp-gb-item-name');
        if(av && nm) av.style.background=gbColor(nm.textContent.trim());
    });

    /* avatar preview */
    var nameEl=document.getElementById('vp-gb-name');
    var avEl  =document.getElementById('vp-gb-av');
    if(nameEl && avEl){
        avEl.style.background=gbColor(nameEl.value||'T');
        nameEl.addEventListener('input',function(){
            var v=nameEl.value||'T';
            avEl.textContent=v[0].toUpperCase();
            avEl.style.background=gbColor(v);
        });
    }

    function esc(s){ return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function relTime(iso){
        var sec=Math.floor((Date.now()-new Date(iso))/1000);
        if(sec<10) return'baru saja';
        if(sec<60) return sec+' detik lalu';
        if(sec<3600) return Math.floor(sec/60)+' menit lalu';
        if(sec<86400) return Math.floor(sec/3600)+' jam lalu';
        return Math.floor(sec/86400)+' hari lalu';
    }
    function setCount(n){ var el=document.getElementById('vp-gb-count'); if(el) el.textContent=n; }

    function renderEntry(e,prepend){
        var init=(e.name||'?')[0].toUpperCase();
        var col=gbColor(e.name||'?');
        var div=document.createElement('div');
        div.className='vp-gb-item vp-gb-new';
        div.dataset.id=e.id;
        div.innerHTML=
            '<div class="vp-gb-av" style="background:'+col+'">'+init+'</div>'+
            '<div class="vp-gb-body">'+
              '<div class="vp-gb-item-name">'+esc(e.name)+'</div>'+
              '<div class="vp-gb-item-msg">'+esc(e.message)+'</div>'+
              '<div class="vp-gb-item-time">'+relTime(e.created_at)+'</div>'+
            '</div>';
        var list=document.getElementById('vp-gb-list');
        var emEl=document.getElementById('vp-gb-empty');
        if(emEl) emEl.remove();
        if(prepend && list.firstChild) list.insertBefore(div,list.firstChild); else list.appendChild(div);
        requestAnimationFrame(function(){ requestAnimationFrame(function(){ div.classList.add('vp-gb-in'); }); });
    }

    /* form submit */
    var form=document.getElementById('vp-gb-form');
    var done=document.getElementById('vp-gb-done');
    if(form){
        form.addEventListener('submit',function(e){
            e.preventDefault();
            if(gbSent) return;
            gbSent=true;
            var btn=form.querySelector('.vp-gb-submit');
            if(btn){ btn.disabled=true; btn.textContent='Mengirim…'; }
            var fd=new FormData(form);
            fetch(form.dataset.action,{
                method:'POST',
                headers:{'Accept':'application/json','X-CSRF-TOKEN':fd.get('_token')},
                body:fd,
            })
            .then(function(r){ if(!r.ok) throw new Error(); return r.json(); })
            .then(function(data){
                form.style.display='none';
                if(done) done.style.display='block';
                if(data.entry){
                    renderEntry(data.entry,true);
                    gbLastId=Math.max(gbLastId,data.entry.id);
                    gbTotal++;
                    setCount(gbTotal);
                }
            })
            .catch(function(){
                form.style.display='none';
                if(done) done.style.display='block';
            });
        });
    }

    /* polling */
    var gbNewCount=0;
    function gbPoll(){
        fetch(gbUrl+'?after='+gbLastId,{headers:{'Accept':'application/json'}})
        .then(function(r){ return r.ok?r.json():[]; })
        .then(function(entries){
            if(!Array.isArray(entries)||!entries.length) return;
            entries.sort(function(a,b){ return a.id-b.id; });
            entries.forEach(function(entry){
                renderEntry(entry,true);
                gbLastId=Math.max(gbLastId,entry.id);
                gbTotal++;
            });
            setCount(gbTotal);
            if(!gbSent){
                gbNewCount+=entries.length;
                var badge=document.getElementById('vp-gb-new-badge');
                if(badge){
                    badge.textContent='+'+gbNewCount+' ucapan baru';
                    badge.style.display='inline-block';
                    badge.onclick=function(){
                        badge.style.display='none'; gbNewCount=0;
                        document.getElementById('vp-gb-list').scrollIntoView({behavior:'smooth'});
                    };
                }
            }
        })
        .catch(function(){});
    }
    setTimeout(function(){ gbPoll(); setInterval(gbPoll,8000); },10000);
})();
</script>
@endif

@if($guest && !$isDemo)
<script>
(function(){
    /* Download QR */
    var dlBtn=document.getElementById('vp-dl-btn');
    if(dlBtn){
        dlBtn.addEventListener('click',function(){
            var url=dlBtn.dataset.dl;
            var name=(dlBtn.dataset.name||'tiket')+'.png';
            dlBtn.textContent='Mengunduh…'; dlBtn.disabled=true;
            fetch(url)
                .then(function(r){ return r.blob(); })
                .then(function(blob){
                    var a=document.createElement('a');
                    a.href=URL.createObjectURL(blob); a.download=name;
                    document.body.appendChild(a); a.click(); document.body.removeChild(a);
                    setTimeout(function(){ URL.revokeObjectURL(a.href); },5000);
                    dlBtn.textContent='✓ Tersimpan';
                    setTimeout(function(){ dlBtn.textContent='↓  Unduh QR'; dlBtn.disabled=false; },2500);
                })
                .catch(function(){
                    window.open(url,'_blank');
                    dlBtn.textContent='↓  Unduh QR'; dlBtn.disabled=false;
                });
        });
    }

    /* Share */
    var shareBtn=document.getElementById('vp-share-btn');
    if(shareBtn && navigator.share){
        shareBtn.style.display='';
        shareBtn.addEventListener('click',function(){
            navigator.share({
                title: 'Tiket Undangan — {{ $guest->guest_name }}',
                text:  'Tiket digital saya untuk pernikahan {{ $brideName }} & {{ $groomName }}',
                url:   window.location.href,
            }).catch(function(){});
        });
    }
})();
</script>
@endif
@endpush
