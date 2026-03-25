@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=cormorant-garamond:300,300i,400,400i,600,600i|jost:200,300,400|cinzel:400,700&display=swap" rel="stylesheet">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/luxury.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- ══ STARS BACKGROUND ══ --}}
<div class="lx-stars"></div>
<div id="lx-petals"></div>

{{-- ══ NAV ══ --}}
<nav class="lx-nav" id="lx-nav">
    <a href="#mempelai">Mempelai</a>
    <a href="#acara">Acara</a>
    @if($wedding->has_gallery || !empty($isPreview))
    <a href="#galeri">Galeri</a>
    @endif
    <a href="#rsvp">RSVP</a>
</nav>

{{-- ══ ENVELOPE COVER ══ --}}
<div class="lx-cover" id="lx-cover" onclick="openInvitation()">
    <div id="cover-photo-bg"></div>
    <div class="lx-env-wrap">
        <div class="lx-env-card">
            <span class="lx-env-gem">◈</span>
            <div class="lx-env-bismillah">Bismillahirrahmanirrahim</div>

            @if($guest)
            <div style="margin-bottom:22px;">
                <span class="lx-env-kepada">Kepada Yang Terhormat</span>
                <span class="lx-env-guest">{{ $guest->guest_name }}</span>
            </div>
            @endif

            <div class="lx-env-divider"><span>◆</span></div>

            <span class="lx-env-label">Undangan Pernikahan</span>
            <div class="lx-env-names" style="margin-top:16px;">
                <h1>{{ $wedding->bride_name }}</h1>
                <span class="lx-amp">&amp;</span>
                <h1>{{ $wedding->groom_name }}</h1>
            </div>

            @if($wedding->event_date)
            <div class="lx-env-date">
                {{ $wedding->event_date->locale('id')->translatedFormat('d F Y') }}
            </div>
            @endif

            <button class="lx-env-btn" onclick="openInvitation()">
                Buka Undangan
            </button>
        </div>
    </div>
</div>

{{-- ══ MAIN CONTENT ══ --}}
<div id="lx-main" class="lx-main">

    {{-- ── HERO ─────────────────────────────────────── --}}
    <section class="lx-sec lx-hero" id="top">

        @if($guest)
        <div class="lx-hero-guest">
            <span class="lx-section-label">Kepada Yang Terhormat</span>
            <div class="lx-hero-guest-name">{{ $guest->guest_name }}</div>
        </div>
        @endif

        <div class="lx-hero-top">Undangan Pernikahan</div>

        <div class="lx-photo-frame">
            <div class="lx-photo-wrap">
                <div class="lx-photo">
                    @if($wedding->bride_photo ?? false)
                    <img src="{{ asset('storage/'.$wedding->bride_photo) }}" alt="Foto Pasangan">
                    @elseif($wedding->couple_photo ?? false)
                    <img src="{{ asset('storage/'.$wedding->couple_photo) }}" alt="Foto Pasangan">
                    @else
                    🌹
                    @endif
                </div>
                <div class="lx-ring"></div>
                <div class="lx-ring2"></div>
            </div>
        </div>

        <div class="lx-couple">
            <span class="lx-name">{{ $wedding->bride_name }}</span>
            <span class="lx-hero-amp">&amp;</span>
            <span class="lx-name">{{ $wedding->groom_name }}</span>
        </div>

        @if($wedding->event_date)
        <div class="lx-date-box lx-rv">
            <span class="lx-date-num">{{ $wedding->event_date->format('d') }}</span>
            <div class="lx-date-sep"></div>
            <div class="lx-date-info">
                <span class="m">{{ $wedding->event_date->locale('id')->translatedFormat('F') }}</span>
                <span class="y">{{ $wedding->event_date->format('Y') }}</span>
                <span class="d">{{ $wedding->event_date->locale('id')->translatedFormat('l') }}</span>
            </div>
        </div>
        @endif

        <div class="lx-scroll-ind">Gulir</div>
    </section>

    {{-- ── AYAT ────────────────────────────────────── --}}
    <section class="lx-sec lx-quote-sec lx-sec-sm" style="min-height:50vh;">
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>
        <blockquote class="lx-quote lx-rv">
            "{{ $wedding->customText('quote_text', 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya, dan dijadikan-Nya di antaramu rasa kasih dan sayang.') }}"
        </blockquote>
        <div class="lx-quote-src lx-rv">{{ $wedding->customText('quote_source', 'QS. Ar-Rum : 21') }}</div>
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>
    </section>

    {{-- ── MEMPELAI ─────────────────────────────────── --}}
    <section class="lx-sec lx-couple-sec" id="mempelai">
        <span class="lx-section-label lx-rv">Yang Berbahagia</span>
        <h2 class="lx-section-title lx-rv">Mempelai</h2>
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>

        <div class="lx-cards-wrap">
            {{-- Mempelai Wanita --}}
            <div class="lx-card lx-rv">
                <div class="lx-card-photo">
                    @if($wedding->bride_photo ?? false)
                    <img src="{{ asset('storage/'.$wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
                    @else 🌸 @endif
                </div>
                <div class="lx-card-role">Mempelai Wanita</div>
                <div class="lx-card-name">{{ $wedding->bride_name }}</div>
                @if($wedding->bride_fullname ?? false)
                <div class="lx-card-full">{{ $wedding->bride_fullname }}</div>
                @endif
                @if(($wedding->bride_father ?? false) || ($wedding->bride_mother ?? false))
                <p class="lx-card-par">
                    Putri dari<br>
                    <strong>{{ $wedding->bride_father ?? '' }}</strong>
                    @if(($wedding->bride_father ?? false) && ($wedding->bride_mother ?? false)) &amp; @endif
                    <strong>{{ $wedding->bride_mother ?? '' }}</strong>
                </p>
                @endif
            </div>

            {{-- Mempelai Pria --}}
            <div class="lx-card lx-rv">
                <div class="lx-card-photo">
                    @if($wedding->groom_photo ?? false)
                    <img src="{{ asset('storage/'.$wedding->groom_photo) }}" alt="{{ $wedding->groom_name }}">
                    @else 🤵 @endif
                </div>
                <div class="lx-card-role">Mempelai Pria</div>
                <div class="lx-card-name">{{ $wedding->groom_name }}</div>
                @if($wedding->groom_fullname ?? false)
                <div class="lx-card-full">{{ $wedding->groom_fullname }}</div>
                @endif
                @if(($wedding->groom_father ?? false) || ($wedding->groom_mother ?? false))
                <p class="lx-card-par">
                    Putra dari<br>
                    <strong>{{ $wedding->groom_father ?? '' }}</strong>
                    @if(($wedding->groom_father ?? false) && ($wedding->groom_mother ?? false)) &amp; @endif
                    <strong>{{ $wedding->groom_mother ?? '' }}</strong>
                </p>
                @endif
            </div>
        </div>
    </section>

    {{-- ── ACARA ────────────────────────────────────── --}}
    <section class="lx-sec lx-event-sec" id="acara">
        <span class="lx-section-label lx-rv">Rangkaian Acara</span>
        <h2 class="lx-section-title lx-rv">Hari Istimewa</h2>
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>

        <div class="lx-event-grid">
            {{-- AKAD --}}
            @if($wedding->akad_date ?? $wedding->event_date)
            <div class="lx-event-card lx-rv">
                <div class="lx-corner tl"></div><div class="lx-corner tr"></div>
                <div class="lx-corner bl"></div><div class="lx-corner br"></div>
                <span class="lx-event-icon">☽</span>
                <div class="lx-event-type">Akad Nikah</div>
                <div class="lx-event-name">Ijab Qabul</div>
                <div class="lx-event-rows">
                    <div class="lx-event-row">
                        <span class="lx-event-row-icon">◈</span>
                        <div class="lx-event-row-txt">
                            <strong>{{ ($wedding->akad_date ?? $wedding->event_date)->locale('id')->translatedFormat('l, d F Y') }}</strong>
                            {{ $wedding->akad_time ?? '08.00 – 10.00' }} WIB
                        </div>
                    </div>
                    <div class="lx-event-row">
                        <span class="lx-event-row-icon">◈</span>
                        <div class="lx-event-row-txt">{{ $wedding->akad_location ?? ($wedding->location ?? 'Lokasi Akad Nikah') }}</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- RESEPSI --}}
            <div class="lx-event-card lx-rv">
                <div class="lx-corner tl"></div><div class="lx-corner tr"></div>
                <div class="lx-corner bl"></div><div class="lx-corner br"></div>
                <span class="lx-event-icon">◇</span>
                <div class="lx-event-type">Resepsi</div>
                <div class="lx-event-name">{{ $wedding->customText('event_name', 'Walimatul Ursy') }}</div>
                <div class="lx-event-rows">
                    <div class="lx-event-row">
                        <span class="lx-event-row-icon">◈</span>
                        <div class="lx-event-row-txt">
                            <strong>{{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}</strong>
                            {{ $wedding->reception_time ?? '11.00 – 14.00' }} WIB
                        </div>
                    </div>
                    <div class="lx-event-row">
                        <span class="lx-event-row-icon">◈</span>
                        <div class="lx-event-row-txt">{{ $wedding->reception_location ?? $wedding->location ?? 'Lokasi Resepsi' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── COUNTDOWN ────────────────────────────────── --}}
    <section class="lx-sec lx-cd-sec" id="countdown">
        <span class="lx-section-label lx-rv">Menuju Hari Bahagia</span>
        <h2 class="lx-section-title lx-rv">Hitung Mundur</h2>
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>
        <div class="lx-cd-wrap lx-rv">
            <div class="lx-cd-item">
                <span class="lx-cd-num" id="lx-hari">00</span>
                <span class="lx-cd-lbl">Hari</span>
            </div>
            <div class="lx-cd-sep">:</div>
            <div class="lx-cd-item">
                <span class="lx-cd-num" id="lx-jam">00</span>
                <span class="lx-cd-lbl">Jam</span>
            </div>
            <div class="lx-cd-sep">:</div>
            <div class="lx-cd-item">
                <span class="lx-cd-num" id="lx-menit">00</span>
                <span class="lx-cd-lbl">Menit</span>
            </div>
            <div class="lx-cd-sep">:</div>
            <div class="lx-cd-item">
                <span class="lx-cd-num" id="lx-detik">00</span>
                <span class="lx-cd-lbl">Detik</span>
            </div>
        </div>
    </section>

    {{-- ── GALERI ────────────────────────────────────── --}}
    @php
        $gallPhotos = [];
        if (!empty($isPreview) && !empty($demoPhotos)) {
            $gallPhotos = $demoPhotos;
        } elseif (($wedding->galleries ?? collect())->count() > 0) {
            $gallPhotos = $wedding->galleries->map(fn($g) => asset('storage/'.$g->path))->toArray();
        }
    @endphp
    @if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)
    <section class="lx-sec lx-gallery-sec lx-sec-sm" id="galeri" style="padding:80px 24px;">
        <span class="lx-section-label lx-rv">Momen Berharga</span>
        <h2 class="lx-section-title lx-rv">Galeri Foto</h2>
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>
        @include('invitation.partials.gallery-innovations', [
            'galleryPhotos'  => $gallPhotos,
            'galleryMode'    => 'slider',
            'galleryBgColor' => '#0a0600',
            'galleryAccent'  => 'rgba(201,168,76,0.25)',
        ])
    </section>
    @endif

    {{-- ── LOKASI ────────────────────────────────────── --}}
    <section class="lx-sec lx-sec-sm" id="lokasi" style="padding:80px 24px; background:rgba(0,0,0,.3);">
        <span class="lx-section-label lx-rv">Temukan Kami</span>
        <h2 class="lx-section-title lx-rv">Lokasi Acara</h2>
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>

        @if($wedding->location)
        <p class="lx-rv" style="color:var(--blush); opacity:.8; font-size:.88rem; letter-spacing:.08em; margin-top:8px; max-width:420px; line-height:1.8;">{{ $wedding->location }}</p>
        @endif

        <div class="lx-rv" style="width:100%; max-width:620px; margin:32px auto; border:1px solid rgba(201,168,76,.2); overflow:hidden;">
            @if($wedding->map_embed ?? false)
            {!! $wedding->map_embed !!}
            @else
            <div style="height:260px; background:rgba(30,15,0,.6); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:16px;">
                <span style="font-size:2rem; opacity:.4; color:var(--gold);">◈</span>
                <span style="font-size:.68rem; letter-spacing:.3em; text-transform:uppercase; color:var(--gold); opacity:.5;">{{ $wedding->location ?? 'Lokasi Acara' }}</span>
            </div>
            @endif
        </div>

        @if($wedding->map_link ?? false)
        <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener"
           class="lx-env-btn lx-rv" style="text-decoration:none; display:inline-block; margin-top:8px;">
            Buka di Google Maps
        </a>
        @endif
    </section>

    {{-- ── AMPLOP DIGITAL ───────────────────────────── --}}
    @if(($wedding->bank_accounts ?? collect())->count() > 0 || ($wedding->bride_bank ?? false) || ($wedding->groom_bank ?? false))
    <section class="lx-sec lx-gift-sec lx-sec-sm" id="amplop" style="padding:80px 24px;">
        <span class="lx-section-label lx-rv">Hadiah &amp; Doa</span>
        <h2 class="lx-section-title lx-rv">Amplop Digital</h2>
        <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>
        <p class="lx-rv" style="color:var(--blush); opacity:.75; font-size:.84rem; max-width:420px; line-height:1.9; margin:8px auto 0;">
            Kehadiran dan doa restu Anda adalah hadiah terindah bagi kami.
        </p>

        <div class="lx-gift-grid lx-rv">
            @if($wedding->bank_accounts ?? false)
                @foreach($wedding->bank_accounts as $acc)
                <div class="lx-gift-card">
                    <span class="lx-gift-icon">◈</span>
                    <div class="lx-gift-bank">{{ $acc->bank_name }}</div>
                    <div class="lx-gift-norek">{{ $acc->account_number }}</div>
                    <div class="lx-gift-atas">a/n {{ $acc->account_name }}</div>
                    <button class="lx-copy-btn" onclick="lxCopy('{{ $acc->account_number }}', this)">Salin Nomor</button>
                </div>
                @endforeach
            @else
                @if($wedding->bride_bank ?? false)
                <div class="lx-gift-card">
                    <span class="lx-gift-icon">🌸</span>
                    <div class="lx-gift-bank">{{ $wedding->bride_bank }}</div>
                    <div class="lx-gift-norek">{{ $wedding->bride_norek }}</div>
                    <div class="lx-gift-atas">a/n {{ $wedding->bride_name }}</div>
                    <button class="lx-copy-btn" onclick="lxCopy('{{ $wedding->bride_norek }}', this)">Salin Nomor</button>
                </div>
                @endif
                @if($wedding->groom_bank ?? false)
                <div class="lx-gift-card">
                    <span class="lx-gift-icon">🌿</span>
                    <div class="lx-gift-bank">{{ $wedding->groom_bank }}</div>
                    <div class="lx-gift-norek">{{ $wedding->groom_norek }}</div>
                    <div class="lx-gift-atas">a/n {{ $wedding->groom_name }}</div>
                    <button class="lx-copy-btn" onclick="lxCopy('{{ $wedding->groom_norek }}', this)">Salin Nomor</button>
                </div>
                @endif
            @endif
        </div>
    </section>
    @endif

    @include('invitation.partials.guestbook')

    {{-- ── RSVP ──────────────────────────────────────── --}}
    <section class="lx-sec lx-rsvp-sec lx-sec-sm" id="rsvp" style="padding:80px 24px;">
        <div class="lx-rsvp-inner">
            <span class="lx-section-label lx-rv">Konfirmasi Kehadiran</span>
            <h2 class="lx-section-title lx-rv">RSVP</h2>
            <div class="lx-gem-div lx-rv"><div class="lx-gem"></div></div>
            <p class="lx-rv" style="color:var(--blush); opacity:.75; font-size:.84rem; line-height:1.9; margin-bottom:0;">
                Mohon konfirmasi kehadiran Anda agar kami dapat mempersiapkan segalanya dengan baik.
            </p>

            @if($guest)
            {{-- Pre-filled for known guest --}}
            <form class="lx-rsvp-form lx-rv" id="lx-rsvp-form"
                  data-action="{{ route('rsvp.store', $wedding->slug) }}">
                @csrf
                <div>
                    <label for="lx-nama">Nama</label>
                    <input type="text" id="lx-nama" name="name" placeholder="Nama Anda"
                           value="{{ $guest->guest_name }}" required>
                </div>
                <div>
                    <label>Konfirmasi Kehadiran</label>
                    <div class="lx-hadir">
                        <input type="radio" name="lx_hadir" id="lx-h-ya"      value="hadir">
                        <label for="lx-h-ya">Hadir</label>
                        <input type="radio" name="lx_hadir" id="lx-h-tidak"   value="tidak_hadir">
                        <label for="lx-h-tidak">Tidak Hadir</label>
                        <input type="radio" name="lx_hadir" id="lx-h-mungkin" value="mungkin" checked>
                        <label for="lx-h-mungkin">Mungkin</label>
                    </div>
                </div>
                <div id="lx-row-jml" style="display:none;">
                    <label for="lx-jml">Jumlah Tamu</label>
                    <input type="number" id="lx-jml" name="jumlah" value="1" min="1" max="10">
                </div>
                <div>
                    <label for="lx-pesan">Ucapan &amp; Doa</label>
                    <textarea id="lx-pesan" name="pesan" rows="3" placeholder="Tulis ucapan dan doa terbaik Anda…"></textarea>
                </div>
                <button type="submit" class="lx-submit">Kirim Ucapan ✦</button>
            </form>

            <div class="lx-rsvp-ok" id="lx-rsvp-ok">
                <span style="font-size:2rem; display:block; margin-bottom:16px; color:var(--gold);">◈</span>
                <p style="font-family:'Cormorant Garamond',serif; font-style:italic; font-size:1.4rem; color:var(--gold-l); line-height:1.6; margin-bottom:12px;">
                    Terima Kasih, {{ $guest->guest_name }}!
                </p>
                <p style="font-size:.8rem; color:var(--blush); opacity:.75; line-height:1.9; max-width:340px; margin:0 auto;">
                    Ucapan dan doa Anda telah kami terima. Kami tunggu kehadiran Anda.
                </p>
            </div>
            @endif

            {{-- Ucapan list --}}
            @if($rsvps->count() > 0)
            <div class="lx-ucapan-list lx-rv" style="margin-top:56px;">
                @foreach($rsvps->take(20) as $r)
                <div class="lx-ucapan-item">
                    <span class="lx-ucapan-nama">{{ $r->guest_name ?? $r->name ?? 'Tamu' }}</span>
                    @if(isset($r->is_attending))
                    <span class="lx-ucapan-stat">{{ $r->is_attending ? '✓ Hadir' : '✗ Tidak Hadir' }}</span>
                    @endif
                    @php $lxMsg = trim(str_replace('[RSVP]', '', $r->notes ?? '')); @endphp
                    @if($lxMsg)
                    <p class="lx-ucapan-msg">{{ $lxMsg }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ── FOOTER ────────────────────────────────────── --}}
    <footer class="lx-footer">
        <div class="lx-gem-div" style="margin-bottom:28px;"><div class="lx-gem"></div></div>
        <div class="lx-footer-names">
            {{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}
        </div>
        @if($wedding->event_date)
        <div class="lx-footer-note">
            {{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}<br>
            Dengan penuh cinta dan kebahagiaan
        </div>
        @endif
        <div class="lx-gem-div" style="margin-top:28px;"><div class="lx-gem"></div></div>
        <div class="lx-footer-credit">Wedding Invitation &mdash; {{ date('Y') }}</div>
    </footer>

</div>{{-- /#lx-main --}}

{{-- ══ MUSIC PLAYER ══ --}}
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
<button class="lx-music-btn" id="lx-music-btn" onclick="toggleMusic()" title="Putar Musik" style="display:none;">♪</button>
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
<script src="{{ asset('js/luxury.js') }}"></script>
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
<script>window.musicData={url:'{{ $_musUrl ?? "" }}',ytId:'{{ $_ytId ?? "" }}',btnId:'lx-music-btn'};</script>
<script src="{{ asset('js/music-player.js') }}"></script>
@endif
@endpush
