@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=Jost:wght@200;300;400;500&family=Great+Vibes&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vip-patisserie.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@php
    $isDemo     = !empty($isPreview);
    $demoPhotos = $demoPhotos ?? [];

    $brideName = $wedding->bride_name ?? 'Annisa Putri';
    $groomName = $wedding->groom_name ?? 'Rizky Pratama';
    $eventDate = $wedding->event_date ?? now()->addDays(60);
    $location  = $wedding->location   ?? 'Grand Ballroom, Hotel Mulia Jakarta';

    /* Cover photo */
    $coverPhoto = null;
    if (!empty($wedding->cover_photo))     $coverPhoto = asset('storage/' . $wedding->cover_photo);
    elseif (!empty($wedding->bride_photo)) $coverPhoto = asset('storage/' . $wedding->bride_photo);

    /* Section bg photos */
    $bgMempelai = !empty($wedding->bg_mempelai_photo) ? asset('storage/' . $wedding->bg_mempelai_photo) : null;
    $bgAcara    = !empty($wedding->bg_acara_photo)    ? asset('storage/' . $wedding->bg_acara_photo)    : null;
    $bgLokasi   = !empty($wedding->bg_lokasi_photo)   ? asset('storage/' . $wedding->bg_lokasi_photo)   : null;

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
    if (!empty($wedding->music_file))    $rawMusicUrl = asset('storage/' . $wedding->music_file);
    elseif (!empty($wedding->music_url)) $rawMusicUrl = $wedding->music_url;
    if ($rawMusicUrl) {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $rawMusicUrl, $ytm)) {
            $ytVideoId = $ytm[1];
        } else {
            $musicUrl = $rawMusicUrl;
        }
    }
    $demoMusicOnly = $isDemo && !$musicUrl && !$ytVideoId;

    /* Couple photos */
    $bridePhotoUrl = !empty($wedding->bride_photo) ? asset('storage/' . $wedding->bride_photo) : null;
    $groomPhotoUrl = !empty($wedding->groom_photo) ? asset('storage/' . $wedding->groom_photo) : null;
@endphp

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

<canvas id="vp-petals" aria-hidden="true"></canvas>

{{-- ══ COVER ══ --}}
<div id="vp-cover" onclick="vpOpenInvitation()" role="button" aria-label="Buka Undangan">

    <div class="vp-cover-bg"
        @if($coverPhoto) style="background-image:url('{{ $coverPhoto }}');"
        @else style="background:linear-gradient(160deg,#fce4ec,#ede8f8,#e0f5f3);" @endif></div>
    <div class="vp-cover-vignette"></div>

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

        <button class="vp-cover-btn" type="button">🌸 &nbsp; Buka Undangan</button>
    </div>
</div>

{{-- ══ MAIN ══ --}}
<div id="vp-main">

    <nav class="vp-nav" id="vp-nav" aria-label="Navigasi">
        <a href="#mempelai">Mempelai</a>
        <a href="#acara">Acara</a>
        @if(count($gallPhotos) > 0)<a href="#galeri">Galeri</a>@endif
        <a href="#lokasi">Lokasi</a>
        <a href="#rsvp">RSVP</a>
    </nav>

    {{-- ══ HERO ══ --}}
    <section id="vp-hero">
        <div class="vp-hero-bg"></div>
        <span class="vp-hero-corner tl">🌸</span>
        <span class="vp-hero-corner tr">🌷</span>
        <span class="vp-hero-corner bl">🌺</span>
        <span class="vp-hero-corner br">🌼</span>

        <div class="vp-hero-frames vp-rv">
            <div class="vp-hero-frame bride">
                @if($bridePhotoUrl)
                    <img src="{{ $bridePhotoUrl }}" alt="{{ $brideName }}" loading="eager">
                @else 🌸 @endif
            </div>
            <div class="vp-hero-amp-badge">&amp;</div>
            <div class="vp-hero-frame groom">
                @if($groomPhotoUrl)
                    <img src="{{ $groomPhotoUrl }}" alt="{{ $groomName }}" loading="eager">
                @elseif($bridePhotoUrl)
                    <img src="{{ $bridePhotoUrl }}" alt="{{ $groomName }}" loading="eager">
                @else 🤵 @endif
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
    <section class="vp-sec vp-sec-rose" id="mempelai" style="position:relative;overflow:hidden;">
        @if(!empty($bgMempelai))
        <div style="position:absolute;inset:0;background-image:url('{{ $bgMempelai }}');background-size:cover;background-position:center;z-index:-1;"></div>
        <div style="position:absolute;inset:0;background:rgba(253,246,239,.88);z-index:-1;"></div>
        @endif

        <div class="vp-rv"><span class="vp-pill">🌸 Yang Berbahagia</span></div>
        <h2 class="vp-title vp-rv">Mempelai</h2>
        <p class="vp-sub vp-rv">Dengan penuh syukur dan kebahagiaan, kami mengumumkan jalinan cinta kami</p>

        <div class="vp-couple-grid">
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

            <div class="vp-couple-amp vp-rv"><span>&amp;</span></div>

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

        @if(!empty($wedding->dresscode))
        <div class="vp-rv" style="margin-top:32px;display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;">
            <span class="vp-dresscode-badge">👗 &nbsp; Dresscode &nbsp;·&nbsp; <em>{{ $wedding->dresscode }}</em></span>
        </div>
        @endif

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
    @if(count($gallPhotos) > 0)
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

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">✿</span></div>

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

<button id="vp-theme-btn" title="Light / Dark Mode" aria-label="Toggle tema">🌙</button>

<button class="vp-music-btn" id="vp-music-btn"
        title="Putar / Pause Musik" aria-label="Musik"
        style="{{ $demoMusicOnly ? 'display:flex;opacity:.45;cursor:default;' : 'display:none;' }}">♩</button>

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
window.vpEventDate     = '{{ $eventDate ? $eventDate->format("Y-m-d") : "" }}';
window.vpMusicUrl      = '{{ $musicUrl ?? "" }}';
window.vpYtVideoId     = '{{ $ytVideoId ?? "" }}';
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
<script>
(function() {
    var radios = document.querySelectorAll('input[name="vr_hadir"]');
    var rowJml = document.getElementById('vp-row-jml');
    function chk() {
        var v = document.querySelector('input[name="vr_hadir"]:checked');
        if (rowJml) rowJml.style.display = (v && v.value === 'hadir') ? '' : 'none';
    }
    radios.forEach(function(r) { r.addEventListener('change', chk); });
    chk();

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
                            var nm  = data.get('name')     || '';
                            var hd  = data.get('vr_hadir') || '';
                            var jml = data.get('jumlah')   || '1';
                            var lbl = hd === 'hadir' ? '✓ Hadir' : hd === 'tidak_hadir' ? '✕ Tidak Hadir' : '? Belum Pasti';
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
@endpush
