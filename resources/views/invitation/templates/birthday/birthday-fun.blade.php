@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=fredoka:300,400,500,600,700|quicksand:300,400,500,600,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/birthday-fun.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- ══ FLOATING CONFETTI ══ --}}
<div class="bf-confetti" id="bf-confetti"></div>

{{-- ══ BALLOONS ══ --}}
<div class="bf-balloons" id="bf-balloons"></div>

{{-- ══ CUSTOM CURSOR ══ --}}
<div class="bf-cursor" id="bf-cursor"></div>
<div class="bf-cursor-trail" id="bf-cursor-trail"></div>

{{-- ══ NAV ══ --}}
<nav class="bf-nav" id="bf-nav">
    <a href="#birthday-person">🎂 Yang Berulang Tahun</a>
    <a href="#party-details">🎉 Detail Pesta</a>
    @if(($wedding->hasGalleryAccess() && $wedding->has_gallery) || !empty($isPreview))
    <a href="#gallery">📸 Galeri</a>
    @endif
    <a href="#rsvp">💌 RSVP</a>
</nav>

{{-- ══ COVER CARD ══ --}}
<div class="bf-cover" id="bf-cover" onclick="bfOpen()">
    <div class="bf-cover-inner">
        <div id="cover-photo-bg"></div>
        <div class="bf-cover-card" id="bf-cover-card">
            <div class="bf-cover-balloons">
                <span class="bf-balloon" style="--d:0s">🎈</span>
                <span class="bf-balloon" style="--d:0.2s">🎈</span>
                <span class="bf-balloon" style="--d:0.4s">🎈</span>
            </div>

            @if($guest)
            <div class="bf-cover-kepada">
                <div class="bf-cover-label">Kepada Yang Terhormat</div>
                <div class="bf-guest-name">{{ $guest->guest_name }}</div>
            </div>
            @endif

            <div class="bf-cover-divider">🎉 ✨ 🎊</div>

            <div class="bf-cover-title">Undangan Ulang Tahun</div>
            <div class="bf-cover-name">{{ $wedding->bride_name ?? 'Birthday Star' }}</div>
            
            @if($wedding->event_date)
            <div class="bf-cover-age">
                <span class="bf-age-label">Merayakan Usia</span>
                <span class="bf-age-num">{{ $wedding->bride_age ?? '??' }}</span>
                <span class="bf-age-label">Tahun</span>
            </div>
            @endif

            @if($wedding->event_date)
            <div class="bf-cover-date">
                {{ $wedding->event_date->locale('id')->translatedFormat('d F Y') }}
            </div>
            @endif

            <button class="bf-cover-btn" onclick="bfOpen()">
                🎁 Buka Undangan
            </button>
        </div>
    </div>
</div>

{{-- ══ MAIN CONTENT ══ --}}
<div class="bf-main" id="bf-main">

    {{-- ── HERO ──────────────────────────────────────── --}}
    <section class="bf-hero bf-rv" id="top">
        <div class="bf-hero-badge">🎂 Undangan Ulang Tahun</div>

        @if($guest)
        <div class="bf-hero-guest">
            <span class="bf-hero-guest-label">Kepada Yang Terhormat</span>
            <span class="bf-hero-guest-name">{{ $guest->guest_name }}</span>
        </div>
        @endif

        <div class="bf-scene" id="bf-scene">
            <div class="bf-orbit-emoji" style="--od:1; --oa:0deg">🎈</div>
            <div class="bf-orbit-emoji" style="--od:2; --oa:72deg">🎉</div>
            <div class="bf-orbit-emoji" style="--od:3; --oa:144deg">🎁</div>
            <div class="bf-orbit-emoji" style="--od:4; --oa:216deg">🎊</div>
            <div class="bf-orbit-emoji" style="--od:5; --oa:288deg">🎂</div>

            <div class="bf-card-3d" id="bf-3d">
                <div class="bf-ring-outer"></div>
                <div class="bf-ring-mid"></div>
                <div class="bf-photo-circle">
                    @if(($wedding->hasMusicAccess() || !empty($isPreview)) && ($wedding->bride_photo ?? false))
                        <img src="{{ asset('storage/' . $wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
                    @else
                        <span style="font-size:3rem;">🎂</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bf-names bf-rv">
            <span class="bf-hero-subtitle">Selamat Ulang Tahun</span>
            <span class="bf-hero-name">{{ $wedding->bride_name ?? 'Birthday Star' }}</span>
            <span class="bf-hero-age">Merayakan Usia {{ $wedding->bride_age ?? '??' }} Tahun</span>
        </div>

        @if($wedding->event_date)
        <div class="bf-datestrip bf-rv">
            <div class="bf-ds-block">
                <div class="bf-ds-num">{{ $wedding->event_date->format('d') }}</div>
                <span class="bf-ds-lbl">Tanggal</span>
            </div>
            <div class="bf-ds-block">
                <div class="bf-ds-num">{{ $wedding->event_date->locale('id')->translatedFormat('M') }}</div>
                <span class="bf-ds-lbl">Bulan</span>
            </div>
            <div class="bf-ds-block">
                <div class="bf-ds-num">{{ $wedding->event_date->format('Y') }}</div>
                <span class="bf-ds-lbl">Tahun</span>
            </div>
            <div class="bf-ds-block">
                <div class="bf-ds-num">{{ $wedding->event_date->locale('id')->translatedFormat('D') }}</div>
                <span class="bf-ds-lbl">Hari</span>
            </div>
        </div>
        @endif
    </section>

    {{-- ── BIRTHDAY PERSON ───────────────────────────── --}}
    <section class="bf-person-sec" id="birthday-person">
        <div class="bf-section-badge bf-rv">🎂 Yang Berulang Tahun</div>
        <h2 class="bf-sec-title bf-rv">Hari Spesial</h2>

        <div class="bf-person-card bf-rv">
            <div class="bf-card-glow"></div>
            <div class="bf-person-avatar">
                @if(($wedding->hasMusicAccess() || !empty($isPreview)) && ($wedding->bride_photo ?? false))
                    <img src="{{ asset('storage/' . $wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
                @else
                    <span style="font-size:4rem;">🎂</span>
                @endif
            </div>
            <div class="bf-person-age-badge">{{ $wedding->bride_age ?? '??' }}</div>
            <div class="bf-person-name">{{ $wedding->bride_name ?? 'Birthday Star' }}</div>
            @if($wedding->bride_parent ?? false)
            <div class="bf-person-parent">Putra/i dari {{ $wedding->bride_parent }}</div>
            @endif

        </div>
    </section>

    {{-- ── PARTY DETAILS ──────────────────────────────── --}}
    <section class="bf-party-sec" id="party-details">
        <div class="bf-section-badge bf-rv">🎉 Rincian Pesta</div>
        <h2 class="bf-sec-title bf-rv">Ayo Rayakan Bersama!</h2>

        <div class="bf-party-card bf-rv">
            <div class="bf-party-icon">🎊</div>
            <div class="bf-party-title">Pesta Ulang Tahun</div>
            
            <div class="bf-party-details">
                @if($wedding->event_date)
                <div class="bf-detail-row">
                    <div class="bf-detail-icon">📅</div>
                    <div class="bf-detail-info">
                        <strong>{{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}</strong>
                        @if($wedding->reception_time ?? false)<span>{{ $wedding->reception_time }} WIB</span>@endif
                    </div>
                </div>
                @endif

                @if($wedding->location ?? false)
                <div class="bf-detail-row">
                    <div class="bf-detail-icon">📍</div>
                    <div class="bf-detail-info">
                        <strong>{{ $wedding->location }}</strong>
                    </div>
                </div>
                @endif

                @if($wedding->dresscode ?? false)
                <div class="bf-detail-row">
                    <div class="bf-detail-icon">👕</div>
                    <div class="bf-detail-info">
                        <strong>Dresscode: {{ $wedding->dresscode }}</strong>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ── COUNTDOWN ──────────────────────────────────── --}}
    @if($wedding->event_date)
    <section class="bf-cd-sec">
        <div class="bf-cd-label bf-rv">⏳ Menghitung Hari</div>
        <div class="bf-cd-grid bf-rv">
            <div class="bf-cd-block">
                <span class="bf-cd-num" id="bf-hari">00</span>
                <span class="bf-cd-unit">Hari</span>
            </div>
            <div class="bf-cd-sep">:</div>
            <div class="bf-cd-block">
                <span class="bf-cd-num" id="bf-jam">00</span>
                <span class="bf-cd-unit">Jam</span>
            </div>
            <div class="bf-cd-sep">:</div>
            <div class="bf-cd-block">
                <span class="bf-cd-num" id="bf-menit">00</span>
                <span class="bf-cd-unit">Menit</span>
            </div>
            <div class="bf-cd-sep">:</div>
            <div class="bf-cd-block">
                <span class="bf-cd-num" id="bf-detik">00</span>
                <span class="bf-cd-unit">Detik</span>
            </div>
        </div>
    </section>
    @endif

    {{-- ── LOKASI ─────────────────────────────────────── --}}
    @if($wedding->location ?? $wedding->map_embed ?? false)
    <section class="bf-loc-sec" id="lokasi">
        <div class="bf-section-badge bf-rv">📍 Lokasi</div>
        <h2 class="bf-sec-title bf-rv">Tempat Pesta</h2>

        @if($wedding->location ?? false)
        <div class="bf-loc-name bf-rv">{{ $wedding->location }}</div>
        @endif

        <div class="bf-map-card bf-rv">
            @if($wedding->map_embed ?? false)
            {!! $wedding->map_embed !!}
            @else
            <div class="bf-map-placeholder">
                <span style="font-size:3rem;">🗺️</span>
            </div>
            @endif
        </div>

        @if($wedding->map_link ?? false)
        <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="bf-maps-btn bf-rv">
            🗺️ Buka di Google Maps
        </a>
        @endif
    </section>
    @endif

    {{-- ── GALLERY ────────────────────────────────────── --}}
    @if(!empty($isPreview) || ($wedding->hasGalleryAccess() && $wedding->has_gallery))
    <section class="bf-gallery-sec" id="gallery">
        <div class="bf-section-badge bf-rv">📸 Galeri Foto</div>
        <h2 class="bf-sec-title bf-rv">Momen Berharga</h2>

        <div class="bf-gallery-grid bf-rv">
            @if(!empty($isPreview) && !empty($demoPhotos))
                @foreach($demoPhotos as $i => $url)
                <div class="bf-gallery-item">
                    <img src="{{ $url }}" alt="Foto {{ $i+1 }}" loading="lazy">
                </div>
                @endforeach
            @else
                @foreach($wedding->gallery->take(6) as $i => $photo)
                <div class="bf-gallery-item">
                    <img src="{{ asset('storage/' . $photo->path) }}" alt="Foto {{ $i+1 }}">
                </div>
                @endforeach
            @endif
        </div>
    </section>
    @endif

    {{-- ── RSVP ───────────────────────────────────────── --}}
    <section class="bf-rsvp-sec" id="rsvp">
        <div class="bf-section-badge bf-rv">💌 Konfirmasi Kehadiran</div>
        <h2 class="bf-sec-title bf-rv">RSVP</h2>
        <p class="bf-rsvp-note bf-rv">Mohon konfirmasi kehadiran Anda 🎉</p>

        @if($guest)
        <div class="bf-form-card bf-rv">
            @if($guest->replied_at)
            {{-- ── Sudah RSVP: status badge + form ucapan tambahan saja ── --}}
            <div style="text-align:center;padding:14px 16px;margin-bottom:20px;background:rgba(255,255,255,.06);border-radius:14px;border:1px solid rgba(255,255,255,.12);">
                <div style="font-size:1.8rem;margin-bottom:6px;">{{ $guest->is_attending ? '✅' : '❌' }}</div>
                <p style="color:rgba(255,255,255,.9);font-size:.9rem;margin:0 0 4px;font-weight:600;">
                    Hei, {{ $guest->guest_name }}!
                </p>
                <p style="color:rgba(255,255,255,.5);font-size:.8rem;margin:0;">
                    Kamu sudah konfirmasi <strong>{{ $guest->is_attending ? 'hadir' : 'tidak hadir' }}</strong> 🎉<br>
                    Masih ingin menambah ucapan?
                </p>
            </div>
            <form id="bf-rsvp-form" data-action="{{ route('rsvp.store', $wedding->slug) }}" onsubmit="return false;">
                @csrf
                <input type="hidden" name="guest_name" value="{{ $guest->guest_name }}">
                <input type="hidden" name="attendance" value="{{ $guest->is_attending ? 'hadir' : 'tidak_hadir' }}">
                <div class="bf-form-field">
                    <label for="bf-pesan">Ucapan &amp; Doa Tambahan</label>
                    <textarea id="bf-pesan" name="message" rows="3"
                              placeholder="Tulis ucapan tambahanmu…" required></textarea>
                </div>
                <button type="submit" class="bf-submit">Kirim Ucapan 🎉</button>
            </form>
            <div class="bf-rsvp-ok" id="bf-rsvp-ok" style="display:none;">
                <span class="bf-ok-icon">💌</span>
                <p class="bf-ok-text">Ucapan Terkirim!</p>
                <p class="bf-ok-sub">Terima kasih atas doa dan ucapan indahmu! 🌸</p>
            </div>
            @else
            {{-- ── Belum RSVP: form lengkap ── --}}
            <form id="bf-rsvp-form" data-action="{{ route('rsvp.store', $wedding->slug) }}"
                  onsubmit="return false;">
                @csrf

                <div class="bf-form-field">
                    <label for="bf-nama">Nama Lengkap</label>
                    <input type="text" id="bf-nama" name="guest_name" placeholder="Nama Anda"
                           value="{{ $guest->guest_name }}" required>
                </div>

                <div class="bf-form-field">
                    <label for="bf-wa">WhatsApp</label>
                    <input type="tel" id="bf-wa" name="phone" placeholder="+62 xxx xxxx xxxx"
                           value="{{ $guest->phone ?? '' }}">
                </div>

                <div class="bf-form-field">
                    <label>Konfirmasi Kehadiran</label>
                    <input type="hidden" id="bf-attendance" name="attendance" value="hadir">
                    <div class="bf-attend-opts">
                        <div class="bf-attend-opt bf-active" onclick="bfAttend('hadir')" data-val="hadir">
                            <span>✅</span><span>Hadir</span>
                        </div>
                        <div class="bf-attend-opt" onclick="bfAttend('mungkin')" data-val="mungkin">
                            <span>🤔</span><span>Mungkin</span>
                        </div>
                        <div class="bf-attend-opt" onclick="bfAttend('tidak_hadir')" data-val="tidak_hadir">
                            <span>❌</span><span>Tidak</span>
                        </div>
                    </div>
                </div>

                <div class="bf-form-field">
                    <label for="bf-jml">Jumlah Tamu</label>
                    <select id="bf-jml" name="guests_count">
                        <option value="1">1 Orang</option>
                        <option value="2">2 Orang</option>
                        <option value="3">3 Orang</option>
                        <option value="4">4+ Orang</option>
                    </select>
                </div>

                <div class="bf-form-field">
                    <label for="bf-pesan">Ucapan &amp; Doa</label>
                    <textarea id="bf-pesan" name="message" rows="3"
                              placeholder="Tulis ucapan terbaik Anda…"></textarea>
                </div>

                <button type="submit" class="bf-submit">Kirim Konfirmasi 🎉</button>
            </form>
            <div class="bf-rsvp-ok" id="bf-rsvp-ok" style="display:none;">
                <span class="bf-ok-icon">🎊</span>
                <p class="bf-ok-text">Terima Kasih!</p>
                <p class="bf-ok-sub">Konfirmasi Anda telah kami terima. Sampai jumpa di pesta! 🎉</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Ucapan list — hanya tampil untuk Premium/VIP --}}
        @if($wedding->hasMusicAccess() && $rsvps->count() > 0)
        <div class="bf-wishes bf-rv">
            <h3 class="bf-wishes-title">💬 Ucapan</h3>
            @foreach($rsvps->take(20) as $r)
            <div class="bf-wish-bubble">
                <div class="bf-wish-header">
                    <span class="bf-wish-name">{{ $r->guest_name ?? 'Tamu' }}</span>
                    @if(isset($r->is_attending))
                    <span class="bf-wish-status">{{ $r->is_attending ? '✅' : '❌' }}</span>
                    @endif
                </div>
                @php $bfMsg = trim(str_replace('[RSVP]', '', $r->notes ?? '')); @endphp
                @if($bfMsg)
                <p class="bf-wish-msg">{{ $bfMsg }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </section>

    {{-- ── FOOTER ─────────────────────────────────────── --}}
    <footer class="bf-footer">
        <div class="bf-footer-emoji">🎂 🎉 🎁 🎈 🎊</div>
        <div class="bf-footer-name">{{ $wedding->bride_name ?? 'Birthday Star' }}</div>
        @if($wedding->event_date)
        <div class="bf-footer-date">
            {{ $wedding->event_date->locale('id')->translatedFormat('d F Y') }}
        </div>
        @endif
        <div class="bf-footer-note">Terima kasih atas doa dan kehadiran Anda 💕</div>
        <div class="bf-footer-credit">Birthday Invitation &mdash; {{ date('Y') }}</div>
    </footer>

</div>{{-- /#bf-main --}}

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
@if(($wedding->hasMusicAccess() || !empty($isPreview)) && (!empty($_musUrl) || !empty($_ytId)))
<button id="wp-music-btn" onclick="toggleMusic()" title="Putar Musik" style="position:fixed;bottom:20px;left:16px;z-index:9999;width:44px;height:44px;border-radius:50%;border:2px solid rgba(255,255,255,.25);background:rgba(0,0,0,.55);color:#fff;font-size:20px;cursor:pointer;display:none;align-items:center;justify-content:center;backdrop-filter:blur(8px);transition:all .2s;box-shadow:0 2px 12px rgba(0,0,0,.4);">&#9834;</button>
@endif
@if(($wedding->hasMusicAccess() || !empty($isPreview)) && !empty($_ytId))
<div style="position:fixed;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;top:-9999px;left:-9999px;" aria-hidden="true"><div id="wp-yt-player"></div></div>
@endif

@endsection

@push('scripts')
<script>
@php
$eventTimestamp = $wedding->event_date ? ($wedding->event_date->timestamp * 1000) : 0;
@endphp
window.birthdayData = {
    eventDate: '{{ $wedding->event_date ? $wedding->event_date->format("Y-m-d") : "" }}',
    timestamp: {{ $eventTimestamp }}
};
window.weddingPackage = '{{ $wedding->package ?? "basic" }}';
window.weddingSlug    = '{{ $wedding->slug }}';
</script>
<script src="{{ asset('js/birthday-fun.js') }}"></script>
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
@if(($wedding->hasMusicAccess() || !empty($isPreview)) && (!empty($_musUrl) || !empty($_ytId)))
@if(!empty($_ytId))<script src="https://www.youtube.com/iframe_api" async defer></script>@endif
<script>window.musicData={url:'{{ $_musUrl ?? "" }}',ytId:'{{ $_ytId ?? "" }}',btnId:'wp-music-btn'};</script>
<script src="{{ asset('js/music-player.js') }}"></script>
@endif
@endpush
