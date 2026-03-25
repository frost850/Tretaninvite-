@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=playfair-display:400,700,900,400i,700i|dm-sans:200,300,400,500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cerah.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- ══ FLOATING BUBBLES BACKGROUND ══ --}}
<div class="ce-bubbles" id="ce-bubbles"></div>

{{-- ══ CUSTOM CURSOR ══ --}}
<div class="ce-cursor" id="ce-cursor"></div>
<div class="ce-cursor-ring" id="ce-cursor-ring"></div>

{{-- ══ NAV ══ --}}
<nav class="ce-nav" id="ce-nav">
    <a href="#mempelai">💍 Mempelai</a>
    <a href="#acara">📅 Acara</a>
    @if($wedding->has_gallery || !empty($isPreview))
    <a href="#galeri">📸 Galeri</a>
    @endif
    <a href="#rsvp">💌 RSVP</a>
</nav>

{{-- ══ COVER CARD ══ --}}
<div class="ce-cover" id="ce-cover" onclick="ceOpen()">
    <div class="ce-cover-inner" style="perspective:900px;">
        <div id="cover-photo-bg"></div>
        <div class="ce-cover-card" id="ce-cover-card">
            <span class="ce-cover-flower">🌸</span>
            <div class="ce-cover-bismillah">Bismillahirrahmanirrahim</div>

            @if($guest)
            <div class="ce-cover-kepada">
                <div class="ce-cover-kepada-label">Kepada Yang Terhormat</div>
                <div class="ce-cover-guest">{{ $guest->guest_name }}</div>
            </div>
            @endif

            <div class="ce-cover-divider">🌸 ✨ 🌸</div>

            <div class="ce-cover-label">Undangan Pernikahan</div>
            <div class="ce-cover-names">
                <div class="ce-cover-name">{{ $wedding->bride_name }}</div>
                <div class="ce-cover-amp">&amp;</div>
                <div class="ce-cover-name">{{ $wedding->groom_name }}</div>
            </div>

            @if($wedding->event_date)
            <div class="ce-cover-date">
                {{ $wedding->event_date->locale('id')->translatedFormat('d F Y') }}
            </div>
            @endif

            <button class="ce-cover-btn" onclick="ceOpen()">
                Buka Undangan 💌
            </button>
        </div>
    </div>
</div>

{{-- ══ MAIN CONTENT ══ --}}
<div class="ce-main" id="ce-main">

    {{-- ── HERO ──────────────────────────────────────── --}}
    <section class="ce-hero ce-rv" id="top">
        <div class="ce-hero-badge">🌸 Undangan Pernikahan</div>

        @if($guest)
        <div class="ce-hero-guest">
            <span class="ce-hero-guest-label">Kepada Yang Terhormat</span>
            <span class="ce-hero-guest-name">{{ $guest->guest_name }}</span>
        </div>
        @endif

        <div class="ce-scene" id="ce-scene">
            <div class="ce-orbit-badge" style="--od:1; --oa:0deg">💍</div>
            <div class="ce-orbit-badge" style="--od:2; --oa:90deg">🌸</div>
            <div class="ce-orbit-badge" style="--od:3; --oa:180deg">💐</div>
            <div class="ce-orbit-badge" style="--od:4; --oa:270deg">🕊️</div>

            <div class="ce-card-3d" id="ce-3d">
                <div class="ce-ring-outer"></div>
                <div class="ce-ring-mid"></div>
                <div class="ce-photo-circle">
                    @if($wedding->bride_photo ?? false)
                        <img src="{{ asset('storage/' . $wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
                    @else
                        <span style="font-size:2.5rem;">💑</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="ce-names ce-rv">
            <span class="ce-hero-name">{{ $wedding->bride_name }}</span>
            <span class="ce-hero-amp">&amp;</span>
            <span class="ce-hero-name">{{ $wedding->groom_name }}</span>
            <span class="ce-hero-sub">Mengundang Anda ke Hari Bahagia Kami</span>
        </div>

        @if($wedding->event_date)
        <div class="ce-datestrip ce-rv">
            <div class="ce-ds-block">
                <div class="ce-ds-num">{{ $wedding->event_date->format('d') }}</div>
                <span class="ce-ds-lbl">Tanggal</span>
            </div>
            <div class="ce-ds-block">
                <div class="ce-ds-num" style="font-size:1.2rem;color:var(--coral)">{{ $wedding->event_date->locale('id')->translatedFormat('M') }}</div>
                <span class="ce-ds-lbl">Bulan</span>
            </div>
            <div class="ce-ds-block">
                <div class="ce-ds-num">{{ $wedding->event_date->format('Y') }}</div>
                <span class="ce-ds-lbl">Tahun</span>
            </div>
            <div class="ce-ds-block">
                <div class="ce-ds-num" style="font-size:1rem;color:var(--mint-d)">{{ $wedding->event_date->locale('id')->translatedFormat('D') }}</div>
                <span class="ce-ds-lbl">Hari</span>
            </div>
        </div>
        @endif
    </section>

    {{-- ── COUPLE ─────────────────────────────────────── --}}
    <section class="ce-couple-sec" id="mempelai">
        <div class="ce-couple-badge ce-rv">💕 Mempelai Kami</div>
        <h2 class="ce-sec-title ce-rv">Dua Hati Menjadi Satu</h2>

        <div class="ce-couple-grid ce-rv">
            <div class="ce-person-card">
                <div class="ce-card-top"></div>
                <div class="ce-card-avatar">
                    @if($wedding->bride_photo ?? false)
                        <img src="{{ asset('storage/' . $wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
                    @else
                        <span style="font-size:2rem;">👰</span>
                    @endif
                </div>
                <div class="ce-card-role">Mempelai Wanita</div>
                <div class="ce-card-name">{{ $wedding->bride_name }}</div>
                @if($wedding->bride_parent ?? false)
                <div class="ce-card-parent">{{ $wedding->bride_parent }}</div>
                @endif

            </div>

            <div class="ce-couple-sep">
                <div class="ce-sep-line"></div>
                <div class="ce-sep-heart">💝</div>
                <div class="ce-sep-line"></div>
            </div>

            <div class="ce-person-card">
                <div class="ce-card-top" style="background: linear-gradient(135deg,var(--mint),var(--blue))"></div>
                <div class="ce-card-avatar">
                    @if($wedding->groom_photo ?? false)
                        <img src="{{ asset('storage/' . $wedding->groom_photo) }}" alt="{{ $wedding->groom_name }}">
                    @else
                        <span style="font-size:2rem;">🤵</span>
                    @endif
                </div>
                <div class="ce-card-role">Mempelai Pria</div>
                <div class="ce-card-name">{{ $wedding->groom_name }}</div>
                @if($wedding->groom_parent ?? false)
                <div class="ce-card-parent">{{ $wedding->groom_parent }}</div>
                @endif

            </div>
        </div>
    </section>

    {{-- ── ACARA ──────────────────────────────────────── --}}
    <section class="ce-event-sec" id="acara">
        <div class="ce-couple-badge ce-rv">📅 Rangkaian Acara</div>
        <h2 class="ce-sec-title ce-rv">Hari Bahagia Kami</h2>

        <div class="ce-event-grid ce-rv">
            {{-- Akad --}}
            <div class="ce-event-card">
                <div class="ce-ev-icon-wrap">🕌</div>
                <div class="ce-ev-type">Akad Nikah</div>
                <div class="ce-ev-name">Ijab Kabul</div>
                <div class="ce-ev-rows">
                    @php $akadDate = ($wedding->akad_date ?? false) ? \Carbon\Carbon::parse($wedding->akad_date) : ($wedding->event_date ?? null); @endphp
                    @if($akadDate)
                    <div class="ce-ev-row">
                        <div class="ce-icon-pill">📅</div>
                        <div class="ce-ev-info">
                            <strong>{{ $akadDate->locale('id')->translatedFormat('l, d F Y') }}</strong>
                            @if($wedding->akad_time ?? false)<span>{{ $wedding->akad_time }} WIB</span>@endif
                        </div>
                    </div>
                    @endif
                    @if($wedding->akad_location ?? $wedding->location ?? false)
                    <div class="ce-ev-row">
                        <div class="ce-icon-pill">📍</div>
                        <div class="ce-ev-info">
                            <strong>{{ $wedding->akad_location ?? $wedding->location }}</strong>
                        </div>
                    </div>
                    @endif
                    @if($wedding->dresscode ?? false)
                    <div class="ce-ev-row">
                        <div class="ce-icon-pill">👔</div>
                        <div class="ce-ev-info"><strong>Dresscode: {{ $wedding->dresscode }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Resepsi --}}
            <div class="ce-event-card ce-reception">
                <div class="ce-ev-icon-wrap" style="background:linear-gradient(135deg,var(--lavender),var(--pink))">🎊</div>
                <div class="ce-ev-type" style="color:#8868c8">Resepsi Pernikahan</div>
                <div class="ce-ev-name">Pesta Pernikahan</div>
                <div class="ce-ev-rows">
                    @php $recDate = ($wedding->reception_date ?? false) ? \Carbon\Carbon::parse($wedding->reception_date) : ($wedding->event_date ?? null); @endphp
                    @if($recDate)
                    <div class="ce-ev-row">
                        <div class="ce-icon-pill" style="background:linear-gradient(135deg,#f8f0ff,var(--lavender))">📅</div>
                        <div class="ce-ev-info">
                            <strong>{{ $recDate->locale('id')->translatedFormat('l, d F Y') }}</strong>
                            @if($wedding->reception_time ?? false)<span>{{ $wedding->reception_time }} WIB</span>@endif
                        </div>
                    </div>
                    @endif
                    @if($wedding->reception_location ?? $wedding->location ?? false)
                    <div class="ce-ev-row">
                        <div class="ce-icon-pill" style="background:linear-gradient(135deg,#f8f0ff,var(--lavender))">📍</div>
                        <div class="ce-ev-info">
                            <strong>{{ $wedding->reception_location ?? $wedding->location }}</strong>
                        </div>
                    </div>
                    @endif
                    @if($wedding->dresscode ?? false)
                    <div class="ce-ev-row">
                        <div class="ce-icon-pill" style="background:linear-gradient(135deg,#f8f0ff,var(--lavender))">👗</div>
                        <div class="ce-ev-info"><strong>Dresscode: {{ $wedding->dresscode }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ── COUNTDOWN ──────────────────────────────────── --}}
    @if($wedding->event_date)
    <section class="ce-cd-sec">
        <div class="ce-cd-label ce-rv">⏳ Menuju Hari Bahagia</div>
        <div class="ce-cd-grid ce-rv">
            <div class="ce-cd-block">
                <span class="ce-cd-num" id="ce-hari">00</span>
                <span class="ce-cd-unit">Hari</span>
            </div>
            <div class="ce-cd-sep">:</div>
            <div class="ce-cd-block">
                <span class="ce-cd-num" id="ce-jam">00</span>
                <span class="ce-cd-unit">Jam</span>
            </div>
            <div class="ce-cd-sep">:</div>
            <div class="ce-cd-block">
                <span class="ce-cd-num" id="ce-menit">00</span>
                <span class="ce-cd-unit">Menit</span>
            </div>
            <div class="ce-cd-sep">:</div>
            <div class="ce-cd-block">
                <span class="ce-cd-num" id="ce-detik">00</span>
                <span class="ce-cd-unit">Detik</span>
            </div>
        </div>
    </section>
    @endif

    {{-- ── LOKASI ─────────────────────────────────────── --}}
    @if($wedding->location ?? $wedding->map_embed ?? false)
    <section class="ce-loc-sec" id="lokasi">
        <div class="ce-couple-badge ce-rv">📍 Lokasi</div>
        <h2 class="ce-sec-title ce-rv">Tempat Acara</h2>

        @if($wedding->location ?? false)
        <div class="ce-loc-name ce-rv">{{ $wedding->location }}</div>
        @endif

        <div class="ce-map-card ce-rv">
            @if($wedding->map_embed ?? false)
            {!! $wedding->map_embed !!}
            @else
            <div class="ce-map-img" style="display:flex; align-items:center; justify-content:center; background:var(--sky);">
                <span style="font-size:3rem; opacity:.4;">🗺️</span>
            </div>
            @endif
            @if($wedding->location ?? false)
            <div class="ce-map-info">
                <span class="ce-map-pin">📍</span>
                {{ $wedding->location }}
            </div>
            @endif
        </div>

        @if($wedding->map_link ?? false)
        <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="ce-maps-btn ce-rv">
            🗺️ Buka di Google Maps
        </a>
        @endif
    </section>
    @endif

    {{-- ── GALLERY ────────────────────────────────────── --}}
    @php
        $gallPhotos = [];
        if (!empty($isPreview) && !empty($demoPhotos)) {
            $gallPhotos = $demoPhotos;
        } elseif (($wedding->gallery ?? collect())->count() > 0) {
            $gallPhotos = $wedding->gallery->map(fn($g) => asset('storage/'.$g->path))->toArray();
        }
    @endphp
    @if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)
    <section class="ce-gallery-sec" id="galeri">
        <div class="ce-couple-badge ce-rv">📸 Momen Kami</div>
        <h2 class="ce-sec-title ce-rv">Galeri Foto</h2>
        @include('invitation.partials.gallery-innovations', [
            'galleryPhotos'  => $gallPhotos,
            'galleryMode'    => 'slider',
            'galleryBgColor' => '#fff0f8',
            'galleryAccent'  => 'rgba(255,100,180,0.25)',
        ])
    </section>
    @endif

    {{-- ── AMPLOP DIGITAL ─────────────────────────────── --}}
    @if(($wedding->bank_accounts ?? collect())->count() > 0 || ($wedding->bride_bank ?? false) || ($wedding->groom_bank ?? false))
    <section class="ce-gift-sec" id="amplop">
        <div class="ce-couple-badge ce-rv">🎁 Hadiah &amp; Doa</div>
        <h2 class="ce-sec-title ce-rv">Amplop Digital</h2>
        <p class="ce-gift-note ce-rv">Kehadiran dan doa restu Anda adalah hadiah terindah bagi kami 💕</p>

        <div class="ce-gift-grid ce-rv">
            @if($wedding->bank_accounts ?? false)
                @foreach($wedding->bank_accounts as $acc)
                <div class="ce-gift-card">
                    <div class="ce-gift-icon">💳</div>
                    <div class="ce-gift-bank">{{ $acc->bank_name }}</div>
                    <div class="ce-gift-norek">{{ $acc->account_number }}</div>
                    <div class="ce-gift-atas">a/n {{ $acc->account_name }}</div>
                    <button class="ce-copy-btn" onclick="ceCopy('{{ $acc->account_number }}', this)">Salin Nomor</button>
                </div>
                @endforeach
            @else
                @if($wedding->bride_bank ?? false)
                <div class="ce-gift-card">
                    <div class="ce-gift-icon">🌸</div>
                    <div class="ce-gift-bank">{{ $wedding->bride_bank }}</div>
                    <div class="ce-gift-norek">{{ $wedding->bride_norek }}</div>
                    <div class="ce-gift-atas">a/n {{ $wedding->bride_name }}</div>
                    <button class="ce-copy-btn" onclick="ceCopy('{{ $wedding->bride_norek }}', this)">Salin Nomor</button>
                </div>
                @endif
                @if($wedding->groom_bank ?? false)
                <div class="ce-gift-card">
                    <div class="ce-gift-icon">💙</div>
                    <div class="ce-gift-bank">{{ $wedding->groom_bank }}</div>
                    <div class="ce-gift-norek">{{ $wedding->groom_norek }}</div>
                    <div class="ce-gift-atas">a/n {{ $wedding->groom_name }}</div>
                    <button class="ce-copy-btn" onclick="ceCopy('{{ $wedding->groom_norek }}', this)">Salin Nomor</button>
                </div>
                @endif
            @endif
        </div>
    </section>
    @endif

    @include('invitation.partials.guestbook')

    {{-- ── RSVP ───────────────────────────────────────── --}}
    <section class="ce-rsvp-sec" id="rsvp">
        <div class="ce-couple-badge ce-rv">💌 Konfirmasi Kehadiran</div>
        <h2 class="ce-sec-title ce-rv">RSVP</h2>
        <p class="ce-rsvp-note ce-rv">Mohon konfirmasi kehadiran Anda agar kami dapat mempersiapkan segalanya 🌸</p>

        @if($guest)
        <div class="ce-form-card ce-rv">
            <form id="ce-rsvp-form" data-action="{{ route('rsvp.store', $wedding->slug) }}"
                  onsubmit="return false;">
                @csrf

                <div class="ce-form-field">
                    <label for="ce-nama">Nama Lengkap</label>
                    <input type="text" id="ce-nama" name="name" placeholder="Nama Anda"
                           value="{{ $guest->guest_name }}" required>
                </div>

                <div class="ce-form-field">
                    <label for="ce-wa">WhatsApp</label>
                    <input type="tel" id="ce-wa" name="phone" placeholder="+62 xxx xxxx xxxx">
                </div>

                <div class="ce-form-field">
                    <label>Konfirmasi Kehadiran</label>
                    <div class="ce-attend-opts">
                        <div class="ce-attend-opt ce-active" onclick="ceAttend(this)" data-val="hadir">
                            <span>✅</span><span>Hadir</span>
                        </div>
                        <div class="ce-attend-opt" onclick="ceAttend(this)" data-val="mungkin">
                            <span>🤔</span><span>Mungkin</span>
                        </div>
                        <div class="ce-attend-opt" onclick="ceAttend(this)" data-val="tidak_hadir">
                            <span>❌</span><span>Tidak</span>
                        </div>
                    </div>
                </div>

                <div class="ce-form-field">
                    <label for="ce-jml">Jumlah Tamu</label>
                    <select id="ce-jml" name="jumlah">
                        <option>1 Orang</option>
                        <option>2 Orang</option>
                        <option>3 Orang</option>
                        <option>4+ Orang</option>
                    </select>
                </div>

                <div class="ce-form-field">
                    <label for="ce-pesan">Ucapan &amp; Doa</label>
                    <textarea id="ce-pesan" name="pesan" rows="3"
                              placeholder="Tulis ucapan dan doa terbaik Anda…"></textarea>
                </div>

                <button type="submit" class="ce-submit">Kirim Konfirmasi ✨</button>
            </form>

            <div class="ce-rsvp-ok" id="ce-rsvp-ok" style="display:none;">
                <span class="ce-ok-icon">🎉</span>
                <p class="ce-ok-text">Terima Kasih, {{ $guest->guest_name }}!</p>
                <p class="ce-ok-sub">Ucapan dan doa Anda telah kami terima. Kami tunggu kehadiran Anda 💕</p>
            </div>
        </div>
        @endif

        {{-- Ucapan list --}}
        @if($rsvps->count() > 0)
        <div class="ce-wishes ce-rv">
            <h3 class="ce-wishes-title">💬 Ucapan &amp; Doa</h3>
            @foreach($rsvps->take(20) as $r)
            <div class="ce-wish-bubble">
                <div class="ce-wish-header">
                    <span class="ce-wish-name">{{ $r->guest_name ?? $r->name ?? 'Tamu' }}</span>
                    @if(isset($r->is_attending))
                    <span class="ce-wish-status">{{ $r->is_attending ? '✅ Hadir' : '❌ Tidak Hadir' }}</span>
                    @endif
                </div>
                @php $ceMsg = trim(str_replace('[RSVP]', '', $r->notes ?? '')); @endphp
                @if($ceMsg)
                <p class="ce-wish-msg">{{ $ceMsg }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </section>

    {{-- ── FOOTER ─────────────────────────────────────── --}}
    <footer class="ce-footer">
        <div class="ce-footer-flowers">🌸 🌺 💐 🌸 🌺</div>
        <div class="ce-footer-names">
            {{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}
        </div>
        @if($wedding->event_date)
        <div class="ce-footer-date">
            {{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}
        </div>
        @endif
        <div class="ce-footer-note">Dengan penuh cinta dan kebahagiaan 💕</div>
        <div class="ce-footer-credit">Wedding Invitation &mdash; {{ date('Y') }}</div>
    </footer>

</div>{{-- /#ce-main --}}

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
    eventDate: '{{ $wedding->event_date ? $wedding->event_date->format("Y-m-d") : "" }}'
};
</script>
<script src="{{ asset('js/cerah.js') }}"></script>
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
@if(!empty($_musUrl) || !empty($_ytId))
@if(!empty($_ytId))<script src="https://www.youtube.com/iframe_api" async defer></script>@endif
<script>window.musicData={url:'{{ $_musUrl ?? "" }}',ytId:'{{ $_ytId ?? "" }}',btnId:'wp-music-btn'};</script>
<script src="{{ asset('js/music-player.js') }}"></script>
@endif
@endpush
