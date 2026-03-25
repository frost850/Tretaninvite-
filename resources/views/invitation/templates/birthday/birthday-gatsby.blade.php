@extends('invitation.layout')

@push('fonts')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Josefin+Sans:wght@100;200;300;400&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/birthday-gatsby.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@php
  $_rawMus = null;
  if (!empty($wedding->music_file))
    $_rawMus = asset('storage/' . $wedding->music_file);
  elseif (!empty($wedding->music_url))
    $_rawMus = $wedding->music_url;
  $_musUrl = null;
  $_ytId   = null;
  if ($_rawMus) {
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $_rawMus, $_ym)) {
      $_ytId = $_ym[1];
    } else {
      $_musUrl = $_rawMus;
    }
  }
  $_showMusic = ($wedding->hasMusicAccess() || !empty($isPreview)) && (!empty($_musUrl) || !empty($_ytId));
@endphp

@section('content')
<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>
@php
  $name      = $wedding->bride_name ?? 'Nadira';
  $age       = $wedding->bride_age  ?? null;
  $parent    = $wedding->bride_parent ?? null;
  $ig        = null;
  $dresscode = $wedding->dresscode ?? null;
  $waPhone = preg_replace('/[^0-9]/', '', $wedding->bride_wa ?? '');
  if (str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);
  $waMsg  = 'Halo, saya ingin memberikan ucapan ulang tahun untuk ' . $name;
  $waUrl  = $waPhone ? 'https://wa.me/' . $waPhone . '?text=' . urlencode($waMsg) : null;
  $photo  = !empty($wedding->bride_photo) ? asset('storage/' . $wedding->bride_photo) : null;
  $romanAge = '';
  if ($age) {
    $n = (int) $age;
    $map = [50=>'L',40=>'XL',10=>'X',9=>'IX',5=>'V',4=>'IV',1=>'I'];
    $r = '';
    foreach ($map as $v => $sym) {
      while ($n >= $v) { $r .= $sym; $n -= $v; }
    }
    $romanAge = $r;
  }
  $dateDay   = $wedding->event_date ? $wedding->event_date->isoFormat('D') : null;
  $dateMonth = $wedding->event_date ? $wedding->event_date->isoFormat('MMMM') : null;
  $dateYear  = $wedding->event_date ? $wedding->event_date->format('Y') : null;
  $dateDow   = $wedding->event_date ? $wedding->event_date->isoFormat('dddd') : null;
  $dateLabel = $wedding->event_date ? $wedding->event_date->isoFormat('dddd, D MMMM Y') : null;
  $eventTime = $wedding->reception_time ?? null;
  $location  = $wedding->location ?? null;
  $galleryPhotos = $wedding->galleries->isNotEmpty()
    ? $wedding->galleries->map(fn($g) => asset('storage/' . $g->path))->toArray()
    : ($demoPhotos ?? []);
  $ageTurns = $age ? 'Turns ' . $age : 'Birthday Party';
@endphp

<div id="grain"></div>
<div id="vignette"></div>
<div id="deco-bg"></div>
<div id="deco-chevron"></div>
<div id="spotlight"></div>
<div id="cur"></div>
<div id="cur-outer"></div>

<div id="cover">
  <div id="cover-photo-bg"></div>
  <div id="film-strips"></div>
  <div class="beam" style="left:20%;height:100%;opacity:1;animation-duration:6s;animation-delay:0s;width:40px;"></div>
  <div class="beam" style="left:50%;height:100%;opacity:.7;animation-duration:8s;animation-delay:1s;width:60px;"></div>
  <div class="beam" style="left:75%;height:100%;opacity:.5;animation-duration:5s;animation-delay:.5s;width:30px;"></div>
  <div class="cover-inner">
    <div class="frame-outer"></div>
    <div class="dc tl"><div class="dc-dot"></div></div>
    <div class="dc tr"><div class="dc-dot"></div></div>
    <div class="dc bl"><div class="dc-dot"></div></div>
    <div class="dc br"><div class="dc-dot"></div></div>
    <div class="cover-card" id="cc">
      <svg class="deco-top" viewBox="0 0 200 60" fill="none">
        <line x1="0" y1="30" x2="200" y2="30" stroke="rgba(201,168,76,0.2)" stroke-width="1"/>
        <line x1="0" y1="25" x2="200" y2="25" stroke="rgba(201,168,76,0.08)" stroke-width="1"/>
        <line x1="0" y1="35" x2="200" y2="35" stroke="rgba(201,168,76,0.08)" stroke-width="1"/>
        <polygon points="100,0 110,30 100,60 90,30" fill="rgba(201,168,76,0.15)" stroke="rgba(201,168,76,0.4)" stroke-width="0.5"/>
        <polygon points="100,5 106,30 100,55 94,30" fill="rgba(201,168,76,0.08)"/>
        <polygon points="60,30 70,20 80,30 70,40" fill="none" stroke="rgba(201,168,76,0.3)" stroke-width="0.8"/>
        <polygon points="140,30 130,20 120,30 130,40" fill="none" stroke="rgba(201,168,76,0.3)" stroke-width="0.8"/>
        <circle cx="100" cy="30" r="4" fill="rgba(201,168,76,0.5)"/>
        <circle cx="70" cy="30" r="2" fill="rgba(201,168,76,0.3)"/>
        <circle cx="130" cy="30" r="2" fill="rgba(201,168,76,0.3)"/>
        <line x1="100" y1="0" x2="40" y2="25" stroke="rgba(201,168,76,0.1)" stroke-width="0.5"/>
        <line x1="100" y1="0" x2="160" y2="25" stroke="rgba(201,168,76,0.1)" stroke-width="0.5"/>
      </svg>
      @if($dateYear)
      <span class="cover-year">Anno Domini · {{ $dateYear }}</span>
      @endif
      <span class="cover-presents">Proudly Presents</span>
      <span class="cover-title-pre">An Evening with</span>
      <span class="cover-name">{{ $name }}</span>
      <div class="deco-rule">
        <div class="deco-rule-mid"><span></span><span></span><span></span></div>
      </div>
      @if($romanAge)
      <span class="cover-roman">{{ $romanAge }}</span>
      @else
      <span class="cover-roman">✦</span>
      @endif
      <div class="deco-rule">
        <div class="deco-rule-mid"><span></span><span></span><span></span></div>
      </div>
      @if($guest)
      <div class="cover-kepada">
        <div class="cover-label">Kepada Yang Terhormat</div>
        <div class="cover-guest-name">{{ $guest->guest_name }}</div>
      </div>
      @endif
      @if($dateLabel || $location)
      <span class="cover-info">
        @if($dateLabel)
        {{ $dateLabel }}
        @endif
        @if($dateLabel && $location)
        <br>
        @endif
        @if($location)
        {{ $location }}
        @endif
      </span>
      @endif
      <button class="enter-btn" onclick="enterSoiree()">
        <span>◆ Enter the Soirée ◆</span>
      </button>
      <div class="film-circles">
        <div class="film-circle">V</div>
        <div class="film-circle">IV</div>
        <div class="film-circle">III</div>
        <div class="film-circle">II</div>
        <div class="film-circle">I</div>
      </div>
    </div>
  </div>
</div>

<div id="main">
  <nav id="nav">
    <div class="nav-top-line"></div>
    <div class="nav-inner">
      <a href="#hero">Overture</a>
      <a href="#about">The Star</a>
      <a href="#event">Programme</a>
      <a href="#countdown">Countdown</a>
      @if(($wedding->hasGalleryAccess() && $wedding->has_gallery) || !empty($isPreview))
      <a href="#gallery">Portfolio</a>
      @endif
      <a href="#rsvp">RSVP</a>
    </div>
    <div class="nav-bottom-line"></div>
  </nav>

  <section id="hero">
    <div class="hero-fan hf1">🎭</div>
    <div class="hero-fan hf2">🎭</div>
    <div class="deco-badge rev"><span class="scan"></span>The Grand Soirée · {{ $dateYear ?? now()->year }}</div>
    <div class="portrait-scene rev" id="portraitScene">
      <div class="portrait-3d" id="portrait3d">
        <div class="p-ring pr1"></div>
        <div class="p-ring pr2"></div>
        <div class="p-ring pr3"></div>
        <div class="p-ring pr4"></div>
        <div class="deco-orb" style="--a:0deg;--r:calc(min(135px,32vw));animation-duration:12s;font-family:'Playfair Display',serif;font-style:italic;font-size:.9rem">✦</div>
        <div class="deco-orb" style="--a:45deg;--r:calc(min(135px,32vw));animation-duration:12s">◆</div>
        <div class="deco-orb" style="--a:90deg;--r:calc(min(135px,32vw));animation-duration:12s;font-family:'Playfair Display',serif;font-style:italic;font-size:.9rem">✦</div>
        <div class="deco-orb" style="--a:135deg;--r:calc(min(135px,32vw));animation-duration:12s">◆</div>
        <div class="deco-orb" style="--a:180deg;--r:calc(min(135px,32vw));animation-duration:12s;font-family:'Playfair Display',serif;font-style:italic;font-size:.9rem">✦</div>
        <div class="deco-orb" style="--a:225deg;--r:calc(min(135px,32vw));animation-duration:12s">◆</div>
        <div class="deco-orb" style="--a:270deg;--r:calc(min(135px,32vw));animation-duration:12s;font-family:'Playfair Display',serif;font-style:italic;font-size:.9rem">✦</div>
        <div class="deco-orb" style="--a:315deg;--r:calc(min(135px,32vw));animation-duration:12s">◆</div>
        <div class="portrait-core">
          @if($photo)
          <img src="{{ $photo }}" alt="{{ $name }}" onerror="this.style.display='none'">
          @endif
          <span style="position:absolute;font-size:5rem;filter:grayscale(1)">{{ $wedding->bride_gender === 'male' ? '🤴' : '👑' }}</span>
        </div>
      </div>
    </div>
    <div class="hero-text-wrap">
      <span class="hero-presents">An Extraordinary Evening Celebrating</span>
      <span class="hero-name">{{ $name }}</span>
      <div class="deco-rule" style="max-width:320px;margin:12px auto">
        <div class="deco-rule-mid"><span></span><span></span><span></span></div>
      </div>
      @if($age)
      <span class="hero-turns">{{ $ageTurns }} ✦</span>
      @else
      <span class="hero-turns">Birthday Soirée ✦</span>
      @endif
      <span class="hero-tagline">◆ A Night of Glamour & Timeless Elegance ◆</span>
    </div>
    @if($dateDay || $dateMonth || $dateYear || $dateDow)
    <div class="hero-dates">
      @if($dateDay)
      <div class="hd"><span class="hn">{{ $dateDay }}</span><span class="hl">Tanggal</span></div>
      @endif
      @if($dateMonth)
      <div class="hd"><span class="hn" style="font-size:.9rem">{{ $dateMonth }}</span><span class="hl">Bulan</span></div>
      @endif
      @if($dateYear)
      <div class="hd"><span class="hn">{{ $dateYear }}</span><span class="hl">Tahun</span></div>
      @endif
      @if($dateDow)
      <div class="hd"><span class="hn" style="font-size:.85rem">{{ $dateDow }}</span><span class="hl">Hari</span></div>
      @endif
    </div>
    @endif
    <div class="scroll-cue">
      <div class="cue-line"></div>
      <div class="cue-diamond"></div>
      <div class="cue-txt">Scroll</div>
    </div>
  </section>

  <section id="about">
    <div class="deco-badge rev"><span class="scan"></span>The Star of the Evening</div>
    <h2 class="sec-title rev">{{ $name }} <em>{{ $age ? '· ' . $romanAge : '' }}</em></h2>
    <p class="sec-sub rev">◆ The {{ $wedding->bride_gender === 'male' ? 'Man' : 'Woman' }} of the Hour ◆</p>
    <div class="about-layout">
      <div class="film-portrait rev">
        <div class="film-mat">
          <div class="sprocket sprocket-left">
            @for($i=0;$i<5;$i++)
            <div class="sprocket-hole"></div>
            @endfor
          </div>
          @if($photo)
          <img src="{{ $photo }}" alt="{{ $name }}" onerror="this.style.display='none'">
          @endif
          <span style="position:absolute;font-size:6rem;filter:grayscale(1)">{{ $wedding->bride_gender === 'male' ? '🤴' : '👸' }}</span>
          <div class="sprocket sprocket-right">
            @for($i=0;$i<5;$i++)
            <div class="sprocket-hole"></div>
            @endfor
          </div>
          <div class="film-caption">
            <span>{{ strtoupper($name) }} · CIRCA {{ $dateYear ?? now()->year }}</span>
          </div>
        </div>
      </div>
      <div class="about-info">
        <div class="info-row rev">
          <span class="ir-label">Nama</span>
          <span class="ir-val">{{ $name }}</span>
          <span class="ir-sub">The star of the evening 🌟</span>
        </div>
        @if($age)
        <div class="info-row rev" style="transition-delay:.08s">
          <span class="ir-label">Milestone</span>
          <span class="ir-val">{{ $romanAge }} — {{ $age }}</span>
          <span class="ir-sub">A year of brilliance</span>
        </div>
        @endif
        @if($dateLabel)
        <div class="info-row rev" style="transition-delay:.16s">
          <span class="ir-label">Tanggal Acara</span>
          <span class="ir-val">{{ $dateLabel }}</span>
          @if($eventTime)
          <span class="ir-sub">Pukul {{ $eventTime }} WIB</span>
          @endif
        </div>
        @endif
        @if($location)
        <div class="info-row rev" style="transition-delay:.24s">
          <span class="ir-label">Venue</span>
          <span class="ir-val">{{ Str::limit($location, 40) }}</span>
          <span class="ir-sub">The evening's grand stage</span>
        </div>
        @endif
        @if($parent)
        <div class="info-row rev" style="transition-delay:.32s">
          <span class="ir-label">Keluarga</span>
          <span class="ir-val">{{ $parent }}</span>
          <span class="ir-sub">With love & blessings</span>
        </div>
        @endif
        @if($ig)
        <div class="info-row rev" style="transition-delay:.4s">
          <span class="ir-label">Instagram</span>
          <span class="ir-val">{{ $ig }}</span>
          <span class="ir-sub">Follow the journey ✨</span>
        </div>
        @endif
      </div>
    </div>
  </section>

  <section id="event">
    <div class="deco-badge rev"><span class="scan"></span>Evening Programme</div>
    <h2 class="sec-title rev">The Grand <em>Soirée</em></h2>
    <p class="sec-sub rev">◆ An Unforgettable Night of Celebration ◆</p>
    <div class="programme rev">
      <div class="prog-head">
        <div class="prog-head-title">Programme of Events</div>
        <div class="prog-head-sub">{{ $dateDow ? '◆ ' . $dateDow . ', ' . $dateDay . ' ' . $dateMonth . ' ' . $dateYear . ' ◆' : '◆ A Special Evening ◆' }}</div>
      </div>
      @if($eventTime)
      <div class="prog-item">
        <div class="prog-time"><span class="prog-t">{{ $eventTime }}</span><span class="prog-tz">WIB</span></div>
        <div class="prog-body">
          <span class="prog-icon">🥂</span>
          <div class="prog-type">◆ Arrival ◆</div>
          <div class="prog-name">Guest Arrival & Reception</div>
          <div class="prog-detail">
            @if($location)
            <strong>{{ $location }}</strong>
            @endif
            Tamu undangan hadir & menikmati sajian awal
          </div>
        </div>
      </div>
      @endif
      <div class="prog-item">
        <div class="prog-time"><span class="prog-t">{{ $eventTime ? date('H:i', strtotime($eventTime) + 5400) : '—' }}</span><span class="prog-tz">WIB</span></div>
        <div class="prog-body">
          <span class="prog-icon">🎂</span>
          <div class="prog-type">◆ Celebration ◆</div>
          <div class="prog-name">Birthday Celebration</div>
          <div class="prog-detail">
            <strong>Acara Inti & Pemotongan Kue</strong>
            Sambutan, doa, dan tiup lilin bersama 🎉
          </div>
        </div>
      </div>
      <div class="prog-item">
        <div class="prog-time"><span class="prog-t">{{ $eventTime ? date('H:i', strtotime($eventTime) + 9000) : '—' }}</span><span class="prog-tz">WIB</span></div>
        <div class="prog-body">
          <span class="prog-icon">🎷</span>
          <div class="prog-type">◆ Entertainment ◆</div>
          <div class="prog-name">Makan & Hiburan</div>
          <div class="prog-detail">
            <strong>Ramah tamah & sesi foto bersama</strong>
            Live music · Photo booth · Open door
            @if($dresscode)
            <br>🎭 Dresscode: {{ $dresscode }}
            @endif
          </div>
        </div>
      </div>
      <div class="prog-foot">
        <span class="prog-foot-txt">◆ RSVP Required · {{ $dateLabel ?? 'Date TBA' }} ◆</span>
      </div>
    </div>
  </section>

  @if($dresscode)
  <section id="dresscode">
    <div class="deco-badge rev"><span class="scan"></span>Dress Code</div>
    <h2 class="sec-title rev">{{ $dresscode }}</h2>
    <p class="sec-sub rev">◆ Dress to the Nines — Elegance is Mandatory ◆</p>
    <div class="dress-layout">
      <div class="dress-card rev">
        <div class="dc-swatch" style="background:linear-gradient(135deg,#0a0a0a,#1a1a1a)"></div>
        <span class="dc-icon">🎩</span>
        <div class="dc-name">Dark Formal</div>
        <div class="dc-hint">Tuxedo & gown</div>
      </div>
      <div class="dress-card rev" style="transition-delay:.08s">
        <div class="dc-swatch" style="background:linear-gradient(135deg,#f5f5f0,#e0e0d8)"></div>
        <span class="dc-icon">🤍</span>
        <div class="dc-name">White</div>
        <div class="dc-hint">Satin & silk</div>
      </div>
      <div class="dress-card rev" style="transition-delay:.16s">
        <div class="dc-swatch" style="background:linear-gradient(135deg,#c9a84c,#e8cc80)"></div>
        <span class="dc-icon">✨</span>
        <div class="dc-name">Champagne Gold</div>
        <div class="dc-hint">Accents welcome</div>
      </div>
      <div class="dress-card rev" style="transition-delay:.24s">
        <div class="dc-swatch" style="background:linear-gradient(135deg,#888,#aaa)"></div>
        <span class="dc-icon">💎</span>
        <div class="dc-name">Silver</div>
        <div class="dc-hint">Sequins & shimmer</div>
      </div>
    </div>
  </section>
  @endif

  @if($wedding->event_date)
  <section id="countdown">
    <div class="deco-badge rev"><span class="scan"></span>Time Remaining</div>
    <h2 class="sec-title rev">The Clock <em>Strikes</em></h2>
    <p class="sec-sub rev">◆ The Evening Approaches ◆</p>
    <div class="cd-row">
      <div class="cd-unit rev">
        <div class="cd-face"><div class="cd-face-bg"></div>
          <div class="cd-inner"><span class="cd-n" id="cd-d">00</span><span class="cd-l">Hari</span></div>
        </div>
      </div>
      <div class="cd-sep rev"><span></span><span></span><span></span></div>
      <div class="cd-unit rev" style="transition-delay:.1s">
        <div class="cd-face"><div class="cd-face-bg"></div>
          <div class="cd-inner"><span class="cd-n" id="cd-h">00</span><span class="cd-l">Jam</span></div>
        </div>
      </div>
      <div class="cd-sep rev" style="transition-delay:.15s"><span></span><span></span><span></span></div>
      <div class="cd-unit rev" style="transition-delay:.2s">
        <div class="cd-face"><div class="cd-face-bg"></div>
          <div class="cd-inner"><span class="cd-n" id="cd-m">00</span><span class="cd-l">Menit</span></div>
        </div>
      </div>
      <div class="cd-sep rev" style="transition-delay:.25s"><span></span><span></span><span></span></div>
      <div class="cd-unit rev" style="transition-delay:.3s">
        <div class="cd-face"><div class="cd-face-bg"></div>
          <div class="cd-inner"><span class="cd-n" id="cd-s">00</span><span class="cd-l">Detik</span></div>
        </div>
      </div>
    </div>
  </section>
  @endif

  @if(($wedding->hasGalleryAccess() && $wedding->has_gallery) || !empty($isPreview))
  <section id="gallery">
    <div class="photo-wall rev">
      @php
        $galleryEmojis = ['🎭','🎷','🥂','✨','🎩','💎','🎂','🌟','🥂','🎶'];
        $photos = $galleryPhotos;
      @endphp
      @forelse($photos as $i => $gallPhoto)
      <div class="pw">
        <div class="pw-em">{{ $galleryEmojis[$i % count($galleryEmojis)] }}</div>
        <img src="{{ $gallPhoto }}" alt="Gallery {{ $i+1 }}" onerror="this.previousElementSibling.style.display='flex';this.remove()">
      </div>
      @empty
      @for($i=0; $i<6; $i++)
      <div class="pw">
        <div class="pw-em">{{ $galleryEmojis[$i % count($galleryEmojis)] }}</div>
      </div>
      @endfor
      @endforelse
    </div>
  </section>
  @endif

  <section id="rsvp">
    <div class="deco-badge rev"><span class="scan"></span>RSVP</div>
    <h2 class="sec-title rev">Your <em>Presence</em> is Requested</h2>
    @if($wedding->event_date)
    <p class="sec-sub rev">◆ Kindly Respond by {{ $wedding->event_date->copy()->subDays(7)->isoFormat('D MMMM Y') }} ◆</p>
    @else
    <p class="sec-sub rev">◆ Kindly Confirm Your Attendance ◆</p>
    @endif
    <div class="rsvp-telegram rev">
      <div class="telegram-top">
        <span class="tg-pre">◆ Telegram of Response</span>
        <span class="tg-num">No. ____</span>
      </div>
      <div class="rsvp-card" id="rsvp-form-wrap">
        <div class="rsvp-perf top"></div>
        @if($guest && $guest->replied_at)
        <div style="text-align:center;padding:32px 0;">
          <div style="font-size:3rem;margin-bottom:12px;">✦</div>
          <p style="font-family:'Playfair Display',serif;font-size:1.2rem;font-style:italic;color:var(--gold);margin-bottom:8px;">Telegram Terima Kasih</p>
          <p style="color:var(--grey);font-size:.8rem;line-height:1.8;">Response Anda telah kami terima.<br>Terima kasih atas konfirmasinya.</p>
        </div>
        @else
        <div id="rsvp-fields">
          <div class="rf">
            <label>◆ Full Name</label>
            <input type="text" id="rsvp-name" placeholder="Your name, if you please..."
              value="{{ $guest?->guest_name ?? '' }}">
          </div>
          <div class="rf">
            <label>◆ WhatsApp</label>
            <input type="tel" id="rsvp-wa" placeholder="+62 xxx xxxx xxxx"
              value="{{ $guest?->phone ?? '' }}">
          </div>
          <div class="rf">
            <label>◆ Your Reply</label>
            <div class="att-row">
              <div class="att on" onclick="pickA(this)" data-val="hadir"><span class="ae">🥂</span>Shall Attend</div>
              <div class="att" onclick="pickA(this)" data-val="tidak_hadir"><span class="ae">🎩</span>Regretfully Not</div>
              <div class="att" onclick="pickA(this)" data-val="mungkin"><span class="ae">💌</span>Uncertain</div>
            </div>
          </div>
          <div class="rf">
            <label>◆ A Message for {{ $name }}</label>
            <textarea id="rsvp-msg" placeholder="Your kind words and wishes..."></textarea>
          </div>
          <button class="send-btn" id="send-btn" onclick="doSend()">
            <span id="send-lbl">◆ Send Telegram ◆</span>
          </button>
        </div>
        <div id="rsvp-ok" style="display:none;text-align:center;padding:32px 0;">
          <div style="font-size:2.5rem;margin-bottom:12px;">✦</div>
          <p style="font-family:'Playfair Display',serif;font-size:1.2rem;font-style:italic;color:var(--gold);margin-bottom:8px;">Telegram Sent</p>
          <p style="color:var(--grey);font-size:.8rem;line-height:1.8;">Ucapan & konfirmasimu sudah kami terima.<br>Sampai jumpa di soirée! 🥂</p>
        </div>
        @endif
        @if($waUrl)
        <a href="{{ $waUrl }}" target="_blank" class="wa-rsvp-btn" style="margin-top:20px;display:flex;">
          <span>💬</span>
          <span>Kirim Ucapan via WhatsApp</span>
        </a>
        @endif
        <div class="rsvp-perf bot"></div>
      </div>
    </div>
    @if($wedding->hasMusicAccess() || !empty($isPreview))
    <div class="wish-scroll" id="wl">
      @forelse($rsvps->take(10) as $rsvp)
      <div class="wish" style="animation-delay:{{ $loop->index * 0.1 }}s">
        <div class="w-name">◆ {{ $rsvp->guest_name }}</div>
        @if($rsvp->notes)
        <div class="w-txt">"{{ $rsvp->notes }}"</div>
        @endif
      </div>
      @empty
      <div class="wish" style="animation-delay:.1s">
        <div class="w-name">◆ Be The First</div>
        <div class="w-txt">"Send your warm wishes and be the first to extend a heartfelt greeting. ✦"</div>
      </div>
      @endforelse
    </div>
    @endif
  </section>

  @if($wedding->map_embed || $wedding->map_link)
  <section id="location">
    <div class="deco-badge rev"><span class="scan"></span>Venue</div>
    <h2 class="sec-title rev">The <em>Venue</em></h2>
    @if($location)
    <p class="sec-sub rev">◆ {{ $location }} ◆</p>
    @endif
    @if($wedding->map_embed)
    <div class="map-embed-wrap rev">
      {!! $wedding->map_embed !!}
    </div>
    @endif
    @if($wedding->map_link)
    <a href="{{ $wedding->map_link }}" target="_blank" class="map-btn rev">
      ◆ Open in Google Maps ◆
    </a>
    @endif
  </section>
  @endif

  <footer>
    <div class="footer-deco-top">
      <div class="fd-mid"><span></span><span></span><span></span></div>
    </div>
    <div class="footer-title">{{ $name }}</div>
    <span class="footer-sub">◆ The Grand Soirée{{ $age ? ' · ' . $romanAge : '' }} ◆</span>
    <div class="deco-rule" style="max-width:300px;margin:14px auto">
      <div class="deco-rule-mid"><span></span><span></span><span></span></div>
    </div>
    <p>Thank you for being part of this extraordinary evening.</p>
    <p>Your presence is the greatest gift of all.</p>
    @if($dateYear)
    <span class="footer-year">{{ strtoupper(gmdate('Y', mktime(0,0,0,1,1,(int)$dateYear))) }}</span>
    @endif
  </footer>
</div>

@if($_showMusic)
<button id="wp-music-btn" onclick="toggleMusic()" title="Putar Musik" style="position:fixed;bottom:20px;left:16px;z-index:9999;width:44px;height:44px;border-radius:50%;border:2px solid rgba(201,168,76,.4);background:rgba(10,10,10,.8);color:var(--gold);font-size:20px;cursor:pointer;display:none;align-items:center;justify-content:center;backdrop-filter:blur(8px);transition:all .2s;box-shadow:0 2px 12px rgba(0,0,0,.4);">&#9834;</button>
@endif

@if($_showMusic && !empty($_ytId))
<div style="position:fixed;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;top:-9999px;left:-9999px;" aria-hidden="true">
  <div id="wp-yt-player"></div>
</div>
@endif

<button id="theme-toggle" onclick="toggleTheme()" title="Toggle Mode">
  <span id="theme-icon">☀</span>
</button>
@endsection

@push('scripts')
<script>
@if($wedding->event_date)
window.eventTimestamp = {{ $wedding->event_date->timestamp * 1000 }};
@endif
@if($guest)
window.guestToken = '{{ $guest->token }}';
@endif
window.weddingSlug    = '{{ $wedding->slug }}';
window.weddingPackage = '{{ $wedding->package ?? "basic" }}';
</script>
<script src="{{ asset('js/birthday-gatsby.js') }}"></script>
<script>
@php
  $_bgList = [];
  if (!empty($isPreview) && !empty($demoPhotos)) {
    $_bgList = array_values($demoPhotos);
  } elseif ($wedding->hasGalleryAccess() && $wedding->gallery->count() > 0) {
    $_bgList = $wedding->gallery->map(fn($g) => asset('storage/' . $g->path))->toArray();
  }
@endphp
window.bgPhotos = {!! json_encode($_bgList) !!};
</script>
<script src="{{ asset('js/photo-bg-slideshow.js') }}"></script>
@if($_showMusic)
@if(!empty($_ytId))
<script src="https://www.youtube.com/iframe_api" async defer></script>
@endif
<script>window.musicData={url:'{{ $_musUrl ?? "" }}',ytId:'{{ $_ytId ?? "" }}',btnId:'wp-music-btn'};</script>
<script src="{{ asset('js/music-player.js') }}"></script>
@endif
@endpush