@extends('invitation.layout')

@push('fonts')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=Jost:wght@200;300;400;500&family=Great+Vibes&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/birthday-patisserie.css') }}">
@endpush

@section('content')

<div id="paper-bg"></div>
<div id="dot-pattern"></div>
<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>
<div id="cur"></div>
<div id="cur-petal"></div>

{{-- ════════════ COVER ════════════ --}}
<div id="cover">
  <div id="cover-photo-bg"></div>
  {{-- floating petals bg --}}
  <div class="cover-float" style="top:10%;left:8%;animation-duration:4s">🌸</div>
  <div class="cover-float" style="top:20%;right:10%;animation-duration:5s;animation-delay:.5s;font-size:1.1rem">🌺</div>
  <div class="cover-float" style="top:65%;left:6%;animation-duration:3.5s;animation-delay:1s;font-size:1rem">🌷</div>
  <div class="cover-float" style="top:70%;right:8%;animation-duration:4.5s;animation-delay:.3s">🌸</div>
  <div class="cover-float" style="top:40%;left:4%;animation-duration:6s;animation-delay:1.5s;font-size:1.1rem">🍀</div>
  <div class="cover-float" style="top:85%;left:30%;animation-duration:5s;animation-delay:.8s;font-size:.9rem">✨</div>
  <div class="cover-float" style="top:15%;left:45%;animation-duration:4s;animation-delay:.2s;font-size:.85rem">🌟</div>

  <div class="cover-box" id="cc">
    <div class="cover-ribbon"></div>
    <div class="wax-seal">🎂</div>
    <div class="cover-pretitle">✦ You're Invited ✦</div>

    <div class="cover-deco"><span>🌸</span><span>✦</span><span>🌸</span></div>

    @if($guest)
    <div class="cover-kepada">
        <div class="cover-label">Kepada Yang Terhormat</div>
        <div class="cover-guest-name">{{ $guest->guest_name }}</div>
    </div>
    <div class="cover-deco"><span>🌸</span><span>✦</span><span>🌸</span></div>
    @endif

    <span class="cover-name">{{ $wedding->bride_name }}</span>
    @if($wedding->bride_age)
    <span class="cover-age">turns {{ $wedding->bride_age }}</span>
    @else
    <span class="cover-age">Birthday Party</span>
    @endif

    <div class="cover-deco" style="margin-top:4px"><span>🍰</span><span>✦</span><span>🍰</span></div>

    @if($wedding->event_date)
    <div class="cover-date">{{ $wedding->event_date->isoFormat('dddd · D MMMM Y') }}</div>
    @endif

    <button class="open-btn" onclick="openInvitation()">
      Open Invitation ✦
    </button>

    <div class="cover-cakes">
      <span>🌸</span><span>🍰</span><span>✨</span><span>🎂</span><span>🌸</span>
    </div>
  </div>
</div>

{{-- ════════════ MAIN ════════════ --}}
<div id="main">

  <nav id="nav">
    <a href="#hero">Home</a>
    <a href="#about">{{ $wedding->bride_gender === 'male' ? 'The King' : 'The Queen' }}</a>
    <a href="#event">Acara</a>
    @if(($wedding->hasGalleryAccess() && $wedding->has_gallery) || !empty($isPreview))
    <a href="#gallery">Galeri</a>
    @endif
    <a href="#rsvp">RSVP</a>
  </nav>

  {{-- HERO --}}
  <section id="hero" data-ph="0">
    <div class="floral-corner fc-tl">🌸</div>
    <div class="floral-corner fc-tr">🌸</div>
    <div class="floral-corner fc-bl">🌷</div>
    <div class="floral-corner fc-br">🌷</div>

    @if($wedding->event_date)
    <div class="e-pill rev">🌸 Save the Date · {{ $wedding->event_date->isoFormat('D MMMM Y') }}</div>
    @endif

    {{-- Cake Scene --}}
    <div class="cake-scene rev" id="cakeScene">
      <div class="cake-3d" id="cake3d">
        <div class="cake-ring cr1"></div>
        <div class="cake-ring cr2"></div>
        <div class="cake-ring cr3"></div>
        <div class="mac-orb" style="--a:0deg;--r:calc(min(128px,31vw));animation-duration:9s">🍰</div>
        <div class="mac-orb" style="--a:51deg;--r:calc(min(128px,31vw));animation-duration:9s">🌸</div>
        <div class="mac-orb" style="--a:102deg;--r:calc(min(128px,31vw));animation-duration:9s">🍫</div>
        <div class="mac-orb" style="--a:153deg;--r:calc(min(128px,31vw));animation-duration:9s">✨</div>
        <div class="mac-orb" style="--a:204deg;--r:calc(min(128px,31vw));animation-duration:9s">🌺</div>
        <div class="mac-orb" style="--a:255deg;--r:calc(min(128px,31vw));animation-duration:9s">💐</div>
        <div class="mac-orb" style="--a:306deg;--r:calc(min(128px,31vw));animation-duration:9s">🎂</div>
        <div class="cake-core">
          @if(($wedding->hasMusicAccess() || !empty($isPreview)) && $wedding->bride_photo)
          {{-- Prioritas: Foto profil yang diupload admin --}}
          <img src="{{ asset('storage/' . $wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
          @elseif(!empty($isPreview) && !empty($demoPhotos) && isset($demoPhotos[0]))
          {{-- Preview mode: Gunakan demo photo --}}
          <img src="{{ $demoPhotos[0] }}" alt="{{ $wedding->bride_name }}" loading="lazy">
          @elseif(($wedding->hasMusicAccess() || !empty($isPreview)) && $wedding->gallery && $wedding->gallery->count() > 0)
          {{-- Fallback: Foto pertama dari galeri --}}
          <img src="{{ asset('storage/' . $wedding->gallery->first()->path) }}" alt="{{ $wedding->bride_name }}">
          @else
          {{-- Fallback terakhir: Emoji --}}
          <span style="position:absolute;font-size:5rem">🎂</span>
          @endif
        </div>
      </div>
    </div>

    <div class="hero-name-wrap">
      <span class="hero-greeting">Merayakan ulang tahun</span>
      <span class="hero-name">{{ $wedding->bride_name }}</span>
      @if($wedding->bride_age)
      <span class="hero-age">yang ke-{{ $wedding->bride_age }} ✦</span>
      @else
      <span class="hero-age">Birthday Celebration ✦</span>
      @endif
      @if(!empty($demo) && isset($demo['invitation_text']))
      <span class="hero-sub">✦ {{ $demo['invitation_text'] }} ✦</span>
      @else
      <span class="hero-sub">✦ Sebuah perayaan yang manis & tak terlupakan ✦</span>
      @endif
    </div>

    @if($wedding->event_date)
    @php
    $eventDate = $wedding->event_date;
    $dayName = $eventDate->isoFormat('dddd');
    $monthName = $eventDate->isoFormat('MMMM');
    @endphp
    <div class="pastel-dates">
      <div class="pd" style="--c:var(--rose)"><span class="pn">{{ $eventDate->format('d') }}</span><span class="pl">Tanggal</span></div>
      <div class="pd" style="--c:var(--lavender)"><span class="pn" style="font-size:1.2rem">{{ $monthName }}</span><span class="pl">Bulan</span></div>
      <div class="pd" style="--c:var(--mint)"><span class="pn">{{ $eventDate->format('Y') }}</span><span class="pl">Tahun</span></div>
      <div class="pd" style="--c:var(--gold)"><span class="pn" style="font-size:1rem">{{ $dayName }}</span><span class="pl">Hari</span></div>
    </div>
    @endif

    <div class="scroll-hint">
      <span>🌸</span>
      <p>Scroll ke bawah</p>
    </div>
  </section>

@php
$isMale     = ($wedding->bride_gender ?? 'female') === 'male';
$titleLabel = $isMale ? 'The Birthday King' : 'The Birthday Queen';
$titleRatu  = $isMale ? 'Sang Raja' : 'Sang Ratu';
$emojiCrown = $isMale ? '🤴' : '👸';
@endphp
  {{-- ABOUT --}}
  <section id="about" data-ph="1">
    <div class="e-pill rev">🎂 {{ $titleLabel }}</div>
    <h2 class="sec-title rev">Mengenal <em>{{ $titleRatu }}</em></h2>
    <p class="sec-sub rev">Setiap momen bersamanya adalah hadiah terindah</p>

    <div class="about-layout">
      {{-- Portrait --}}
      <div class="portrait-frame rev">
        <div class="portrait-mat">
          @if(($wedding->hasMusicAccess() || !empty($isPreview)) && $wedding->bride_photo)
          {{-- Prioritas: Foto profil yang diupload admin --}}
          <img src="{{ asset('storage/' . $wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
          @elseif(!empty($isPreview) && !empty($demoPhotos) && isset($demoPhotos[1]))
          {{-- Preview mode: Demo photo kedua --}}
          <img src="{{ $demoPhotos[1] }}" alt="{{ $wedding->bride_name }}" loading="lazy">
          @elseif(($wedding->hasMusicAccess() || !empty($isPreview)) && $wedding->gallery && $wedding->gallery->count() > 1)
          {{-- Fallback: Foto kedua dari galeri --}}
          <img src="{{ asset('storage/' . $wedding->gallery->skip(1)->first()->path) }}" alt="{{ $wedding->bride_name }}">
          @else
          {{-- Fallback terakhir: Emoji --}}
          <span style="position:absolute;font-size:6rem">{{ $emojiCrown }}</span>
          @endif
        </div>
        <div class="portrait-tag">{{ $wedding->bride_name }}</div>
      </div>

      {{-- Info stack --}}
      <div class="info-stack">
        <div class="info-card rev" style="--c:var(--rose)">
          <div class="ic-label">Nama Lengkap</div>
          <div class="ic-val">{{ $wedding->bride_name }}</div>
          <div class="ic-sub">{{ $isMale ? 'The birthday king 🤴' : 'The birthday queen 👸' }}</div>
        </div>
        
        @if($wedding->bride_age)
        <div class="info-card rev" style="--c:var(--lavender);transition-delay:.08s">
          <div class="ic-label">Ulang Tahun Ke</div>
          <div class="ic-val">Yang ke-{{ $wedding->bride_age }} ✨</div>
          <div class="ic-sub">Milestone celebration!</div>
        </div>
        @endif
        
        @if($wedding->event_date)
        <div class="info-card rev" style="--c:var(--mint);transition-delay:.16s">
          <div class="ic-label">Tanggal Lahir</div>
          <div class="ic-val">{{ $wedding->event_date->isoFormat('D MMMM Y') }}</div>
          <div class="ic-sub">Sweet & lovely celebration</div>
        </div>
        @endif
        
        @if($wedding->bride_parent)
        <div class="info-card rev" style="--c:var(--gold);transition-delay:.24s">
          <div class="ic-label">Orang Tua</div>
          <div class="ic-val">{{ $wedding->bride_parent }}</div>
          <div class="ic-sub">With love from the family 💕</div>
        </div>
        @endif
      </div>
    </div>
  </section>

  {{-- EVENT --}}
  <section id="event" data-ph="2">
    <div class="e-pill rev">🍰 Menu Acara</div>
    <h2 class="sec-title rev">Afternoon <em>Tea Party</em></h2>
    <p class="sec-sub rev">Sebuah sore yang manis bersama orang-orang terkasih</p>

    <div class="menu-layout">
      <div class="menu-card rev" style="--c:var(--rose);--wm:'🌸'">
        <span class="mc-icon">🎀</span>
        <div class="mc-type">✦ Main Event ✦</div>
        <div class="mc-title">Birthday Tea Party</div>
        <div class="mc-divider"><div class="d"></div></div>
        <div class="mc-info">
          @if($wedding->event_date)
          <strong>{{ $wedding->event_date->isoFormat('dddd, D MMMM Y') }}</strong>
          🕐 {{ $wedding->reception_time ?? '14:00' }} WIB<br>
          @endif
          @if($wedding->location)
          📍 {{ $wedding->location }}<br>
          @endif
          @if($wedding->dresscode)
          🌸 Dresscode: {{ $wedding->dresscode }}
          @else
          🌸 Dresscode: Pastel & Floral
          @endif
        </div>
      </div>

      <div class="menu-card rev" style="--c:var(--lavender);--wm:'🎂';transition-delay:.12s">
        <span class="mc-icon">🍰</span>
        <div class="mc-type">✦ Celebration ✦</div>
        <div class="mc-title">Tiup Lilin & Kue</div>
        <div class="mc-divider"><div class="d"></div></div>
        <div class="mc-info">
          @if($wedding->reception_time)
          <strong>Pukul {{ $wedding->reception_time }} WIB</strong>
          @endif
          🎂 Special birthday cake<br>
          🍫 Sweet dessert table<br>
          🌸 Floral decoration<br>
          📸 Photo booth & memories
        </div>
      </div>

      @if(!empty($demo) && isset($demo['party_theme']))
      <div class="menu-card rev" style="--c:var(--mint);--wm:'✨';transition-delay:.24s">
        <span class="mc-icon">✨</span>
        <div class="mc-type">✦ Theme ✦</div>
        <div class="mc-title">{{ $demo['party_theme'] }}</div>
        <div class="mc-divider"><div class="d"></div></div>
        <div class="mc-info">
          <strong>Special Activities</strong>
          🍰 Fun games & prizes<br>
          🎁 Goodie bags for all<br>
          👩‍🍳 Sweet memories<br>
          🌺 Celebration together
        </div>
      </div>
      @endif
    </div>
  </section>

  {{-- DRESSCODE --}}
  @if($wedding->dresscode)
  <section id="dresscode">
    <div class="e-pill rev">👗 Dress Code</div>
    <h2 class="sec-title rev">Palet <em>Warna</em> Hari Ini</h2>
    <p class="sec-sub rev">Kenakan warna-warna lembut yang menawan</p>

    <div class="palette-row">
      <div class="palette-swatch rev" style="transition-delay:0s">
        <div class="sw-color" style="background:linear-gradient(135deg,#f4a7b9,#f8c8d4)"></div>
        <span class="sw-icon">🌸</span>
        <div class="sw-name">Blush Rose</div>
        <div class="sw-hint">Pink pastel lembut</div>
      </div>
      <div class="palette-swatch rev" style="transition-delay:.08s">
        <div class="sw-color" style="background:linear-gradient(135deg,#d1c4e9,#e8dff8)"></div>
        <span class="sw-icon">💜</span>
        <div class="sw-name">Soft Lavender</div>
        <div class="sw-hint">Ungu muda elegan</div>
      </div>
      <div class="palette-swatch rev" style="transition-delay:.16s">
        <div class="sw-color" style="background:linear-gradient(135deg,#b2dfdb,#d0f0ee)"></div>
        <span class="sw-icon">🌿</span>
        <div class="sw-name">Mint Cream</div>
        <div class="sw-hint">Hijau mint segar</div>
      </div>
      <div class="palette-swatch rev" style="transition-delay:.24s">
        <div class="sw-color" style="background:linear-gradient(135deg,#ffccbc,#ffe0d0)"></div>
        <span class="sw-icon">🍑</span>
        <div class="sw-name">Peach Sorbet</div>
        <div class="sw-hint">Peach hangat manis</div>
      </div>
      <div class="palette-swatch rev" style="transition-delay:.32s">
        <div class="sw-color" style="background:linear-gradient(135deg,#fdf8f0,#f8f0e0)"></div>
        <span class="sw-icon">🤍</span>
        <div class="sw-name">Ivory & Cream</div>
        <div class="sw-hint">Putih krem anggun</div>
      </div>
    </div>
  </section>
  @endif

  {{-- COUNTDOWN --}}
  @if($wedding->event_date)
  <section id="countdown">
    <div class="cd-title rev">Menghitung Hari ✦</div>
    <div class="cd-sub rev">Menuju perayaan yang paling manis</div>
    <div class="cd-tiles rev">
      <div class="cd-tile"><span class="cd-n" id="cd-d">00</span><span class="cd-l">Hari</span></div>
      <div class="cd-sep">·</div>
      <div class="cd-tile"><span class="cd-n" id="cd-h">00</span><span class="cd-l">Jam</span></div>
      <div class="cd-sep">·</div>
      <div class="cd-tile"><span class="cd-n" id="cd-m">00</span><span class="cd-l">Menit</span></div>
      <div class="cd-sep">·</div>
      <div class="cd-tile"><span class="cd-n" id="cd-s">00</span><span class="cd-l">Detik</span></div>
    </div>
  </section>
  @endif

  {{-- GALLERY --}}
  @if(!empty($isPreview) || ($wedding->hasGalleryAccess() && $wedding->has_gallery))
  <section id="gallery" data-ph="4">
    <div class="e-pill rev">📸 Galeri</div>
    <h2 class="sec-title rev">Kenangan <em>Manis</em></h2>

    <div class="gallery-film rev">
      @if(!empty($isPreview) && !empty($demoPhotos))
        @foreach($demoPhotos as $i => $url)
          @if($i < 6)
          <div class="gf @if($i === 0) gf-tall @elseif($i === 3) gf-wide @endif">
            <div class="gf-em">{{ ['🎂', '🌸', '🍰', '✨', '🌺', '🎀'][$i] }}</div>
            <img src="{{ $url }}" alt="Foto {{ $i+1 }}" loading="lazy" onerror="this.previousElementSibling.style.display='flex';this.remove()">
          </div>
          @endif
        @endforeach
      @else
        @foreach($wedding->gallery->take(6) as $i => $photo)
        @php
          $pathParts   = explode('/', $photo->path);
          $encodedPath = implode('/', array_map('rawurlencode', $pathParts));
        @endphp
        <div class="gf @if($i === 0) gf-tall @elseif($i === 3) gf-wide @endif">
          <div class="gf-em">{{ ['🎂', '🌸', '🍰', '✨', '🌺', '🎀'][$i] }}</div>
          <img src="{{ asset('storage/' . $encodedPath) }}" alt="Foto {{ $i+1 }}" onerror="this.previousElementSibling.style.display='flex';this.remove()">
        </div>
        @endforeach
      @endif
    </div>
  </section>
  @endif

  {{-- RSVP --}}
  <section id="rsvp" data-ph="3">
    <div class="e-pill rev">✉️ RSVP</div>
    <h2 class="sec-title rev">Hadirlah di <em>Pesta Kami</em></h2>
    @if($wedding->event_date)
    <p class="sec-sub rev">Konfirmasi kehadiranmu paling lambat {{ $wedding->event_date->subDays(7)->isoFormat('D MMMM Y') }}</p>
    @endif

    <div class="rsvp-envelope rev">
      <div class="env-flap"></div>
      <div class="rsvp-card">
        @if($guest && $guest->replied_at)
        {{-- ── Sudah RSVP: status + form ucapan tambahan saja ── --}}
        <div style="text-align:center;padding:14px 16px;margin-bottom:20px;background:rgba(212,163,115,.1);border-radius:14px;border:1px solid rgba(212,163,115,.25);">
          <div style="font-size:1.8rem;margin-bottom:6px;">{{ $guest->is_attending ? '🌸' : '🍂' }}</div>
          <p style="font-family:var(--serif);color:var(--rose);font-size:.9rem;margin:0 0 4px;">
            Hei, <strong>{{ $guest->guest_name }}</strong>!<br>
            Kamu sudah konfirmasi <strong>{{ $guest->is_attending ? 'hadir' : 'tidak hadir' }}</strong>.
          </p>
          <p style="color:var(--muted);font-size:.75rem;margin:0;">Masih ingin menambah ucapan? Silakan! 🌸</p>
        </div>
        <form id="rsvpForm" onsubmit="return handleRsvp(event)">
          <input type="hidden" name="name" value="{{ $guest->guest_name }}">
          <input type="hidden" name="attendance" value="{{ $guest->is_attending ? 'hadir' : 'tidak_hadir' }}">
          <div class="rf">
            <label>✦ Ucapan untuk {{ $wedding->bride_name }}</label>
            <textarea name="message" placeholder="Tuliskan ucapan tambahanmu..." required></textarea>
          </div>
          <button type="submit" class="send-btn">Kirim Ucapan ✦</button>
        </form>
        @else
        {{-- ── Belum RSVP: form lengkap ── --}}
        <form id="rsvpForm" onsubmit="return handleRsvp(event)">
          <div class="rf">
            <label>✦ Nama Lengkap</label>
            <input type="text" name="name" placeholder="Nama indahmu..." value="{{ $guest->guest_name ?? '' }}" required>
          </div>
          <div class="rf">
            <label>✦ WhatsApp</label>
            <input type="tel" name="phone" placeholder="+62 xxx xxxx xxxx" value="{{ $guest->phone ?? '' }}">
          </div>
          <div class="rf">
            <label>✦ Kehadiran</label>
            <div class="att-row">
              <div class="att on" data-value="hadir" onclick="pickA(this)">
                <span class="ae">🌸</span>Hadir
              </div>
              <div class="att" data-value="tidak_hadir" onclick="pickA(this)">
                <span class="ae">🍂</span>Tidak
              </div>
              <div class="att" data-value="mungkin" onclick="pickA(this)">
                <span class="ae">🌿</span>Belum Pasti
              </div>
            </div>
            <input type="hidden" name="attendance" value="hadir">
          </div>
          <div class="rf">
            <label>✦ Jumlah Tamu</label>
            <select name="guests">
              <option value="1">Hanya aku 🌸</option>
              <option value="2">Berdua 💑</option>
              <option value="3">Bertiga 🌺</option>
              <option value="4+">Berempat+ 🎀</option>
            </select>
          </div>
          <div class="rf">
            <label>✦ Ucapan untuk {{ $wedding->bride_name }}</label>
            <textarea name="message" placeholder="Tuliskan ucapan terindahmu..."></textarea>
          </div>
          <button type="submit" class="send-btn">Kirim Ucapan ✦</button>
        </form>
        @endif

        {{-- Sukses state (shared) --}}
        <div id="rsvp-ok" style="display:none;text-align:center;padding:48px 24px;">
          <div style="font-size:3.5rem;margin-bottom:12px;">🌸</div>
          <h3 style="font-family:var(--serif);color:var(--rose);font-size:1.4rem;margin-bottom:8px;">Terima Kasih!</h3>
          <p style="color:var(--muted);font-size:.85rem;line-height:1.6;">
            @if($guest && $guest->replied_at)
              Ucapanmu sudah kami terima. Terima kasih banyak! 🌸
            @else
              Ucapan & konfirmasimu sudah kami terima.<br>Sampai jumpa di pesta! 🎂
            @endif
          </p>
        </div>
      </div>
    </div>

    {{-- Wish list — hanya tampil untuk Premium/VIP --}}
    @if($wedding->hasMusicAccess())
    <div class="wish-list" id="wl">
      @forelse($rsvps->take(10) as $rsvp)
      <div class="wish" style="animation-delay:{{ $loop->index * 0.1 }}s">
        <div class="w-name">✦ {{ $rsvp->guest_name }}</div>
        @if($rsvp->notes)
        <div class="w-txt">"{{ $rsvp->notes }}"</div>
        @endif
      </div>
      @empty
      <div class="wish" style="animation-delay:.1s">
        <div class="w-name">✦ Jadilah yang pertama</div>
        <div class="w-txt">"Kirimkan ucapan indahmu dan jadilah yang pertama memberikan doa terbaik 🌸"</div>
      </div>
      @endforelse
    </div>
    @endif
  </section>

  {{-- LOCATION --}}
  @if($wedding->map_embed || $wedding->map_link)
  <section id="location" style="min-height:auto;padding:80px 24px;background:var(--linen)">
    <div class="e-pill rev">📍 Lokasi</div>
    <h2 class="sec-title rev">Peta <em>Lokasi</em></h2>
    
    @if($wedding->map_embed)
    <div class="rev" style="max-width:700px;width:100%;margin-top:36px;overflow:hidden;border-radius:8px;box-shadow:0 8px 30px rgba(61,43,31,.1)">
      {!! $wedding->map_embed !!}
    </div>
    @endif
    
    @if($wedding->map_link)
    <div class="rev" style="margin-top:24px">
      <a href="{{ $wedding->map_link }}" target="_blank" class="open-btn" style="display:inline-block;text-decoration:none">
        Buka di Google Maps ✦
      </a>
    </div>
    @endif
  </section>
  @endif

  <footer>
    <div class="footer-floral">
      <span>🌸</span><span>🌺</span><span>🌸</span><span>🌷</span><span>🌸</span>
    </div>
    <div class="footer-seal">🎂</div>
    <div class="footer-name">{{ $wedding->bride_name }}</div>
    <div class="gold-divider" style="margin:14px auto">
      <div class="gd-gem"></div><div class="gd-gem"></div><div class="gd-gem"></div>
    </div>
    <p>Terima kasih telah meluangkan waktu untuk hadir</p>
    <p>merayakan hari paling manis ini bersama kami 🌸</p>
    <br>
    @if($wedding->event_date)
    <p style="opacity:.35;font-size:.55rem;letter-spacing:.4em">✦ {{ $wedding->event_date->format('d · m · Y') }} ✦</p>
    @endif
  </footer>

</div>

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
{{-- ═══ THEME TOGGLE ═══ --}}
<button id="bp-theme-btn" onclick="bpToggleTheme()" title="Toggle Dark Mode">🌙</button>

@if(($wedding->hasMusicAccess() || !empty($isPreview)) && (!empty($_musUrl) || !empty($_ytId)))
<button id="wp-music-btn" onclick="toggleMusic()" title="Putar Musik" style="position:fixed;bottom:20px;left:16px;z-index:9999;width:44px;height:44px;border-radius:50%;border:2px solid rgba(255,255,255,.25);background:rgba(0,0,0,.55);color:#fff;font-size:20px;cursor:pointer;display:none;align-items:center;justify-content:center;backdrop-filter:blur(8px);transition:all .2s;box-shadow:0 2px 12px rgba(0,0,0,.4);">&#9834;</button>
@endif
@if(($wedding->hasMusicAccess() || !empty($isPreview)) && !empty($_ytId))
<div style="position:fixed;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;top:-9999px;left:-9999px;" aria-hidden="true"><div id="wp-yt-player"></div></div>
@endif

@endsection

@push('scripts')
<script>
@if($wedding->event_date)
window.eventTimestamp = {{ $wedding->event_date->timestamp * 1000 }};
window.eventDate = '{{ $wedding->event_date->toIso8601String() }}';
@endif
@if($guest)
window.guestToken = '{{ $guest->token }}';
@endif
window.weddingSlug    = '{{ $wedding->slug }}';
window.weddingPackage = '{{ $wedding->package ?? "basic" }}';
@if($wedding->hasGalleryAccess() && isset($wedding->gallery) && $wedding->gallery->count() > 0)
window.bgPhotos = [@foreach($wedding->gallery as $photo)
  '{{ asset("storage/" . implode("/", array_map("rawurlencode", explode("/", $photo->path)))) }}',
@endforeach];
@elseif(!empty($isPreview) && !empty($demoPhotos))
window.bgPhotos = {!! json_encode(array_values($demoPhotos)) !!};
@else
window.bgPhotos = [];
@endif
</script>
<script src="{{ asset('js/birthday-patisserie.js') }}"></script>
<script>
(function(){
  var btn = document.getElementById('bp-theme-btn');
  var isDark = localStorage.getItem('bp-theme') === 'dark';
  if(isDark){ document.body.classList.add('dark-mode'); if(btn) btn.textContent = '☀️'; }
  window.bpToggleTheme = function(){
    isDark = !isDark;
    document.body.classList.toggle('dark-mode', isDark);
    if(btn) btn.textContent = isDark ? '☀️' : '🌙';
    localStorage.setItem('bp-theme', isDark ? 'dark' : 'light');
  };
})();
</script>
@if(($wedding->hasMusicAccess() || !empty($isPreview)) && (!empty($_musUrl) || !empty($_ytId)))
@if(!empty($_ytId))<script src="https://www.youtube.com/iframe_api" async defer></script>@endif
<script>window.musicData={url:'{{ $_musUrl ?? "" }}',ytId:'{{ $_ytId ?? "" }}',btnId:'wp-music-btn'};</script>
<script src="{{ asset('js/music-player.js') }}"></script>
@endif
@endpush
