@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=cinzel:400,600|dancing-script:400,600,700|nunito:300,400,600&display=swap" rel="stylesheet" />
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/floral.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- -- HERO -- --}}
<section class="hero" id="top">
    @if($guest)
    <div style="margin-bottom:44px;">
        <div class="fl-ornament">?? ?? ??</div>
        <span class="hero-tag">Kepada Yang Terhormat</span>
        <p class="fi" style="font-size:clamp(1.8rem,5vw,2.8rem); color:var(--txt); margin-top:4px;">{{ $guest->guest_name }}</p>
    </div>
    @endif

    <div class="fl-ornament rv" style="font-size:2rem;">?? ?? ??</div>
    <span class="hero-tag rv" style="margin-top:12px;">Undangan Pernikahan</span>

    <h1 class="rv" style="margin-top:10px;">{{ $wedding->bride_name }}</h1>
    <span class="hero-amp rv">� &amp; �</span>
    <h1 class="rv">{{ $wedding->groom_name }}</h1>

    @if($wedding->event_date)
    <div class="hero-date rv">
        ?? {{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }} ??
    </div>
    @endif
    @if($wedding->location)
    <p class="rv fc" style="margin-top:12px; font-size:.7rem; letter-spacing:.2em; color:var(--rose); opacity:.7;">{{ $wedding->location }}</p>
    @endif

    <a href="#mempelai" class="hero-cta rv" style="margin-top:44px;">
        <span>??</span><span>Buka Undangan</span><span>??</span>
    </a>
</section>


{{-- -- MEMPELAI -- --}}
<section class="sec" id="mempelai">
    <div class="fl-ornament rv">?? ?? ??</div>
    <span class="stag rv">Yang Berbahagia</span>
    <h2 class="stit rv">Dua Hati Bersatu</h2>
    <p class="ssub rv">Dengan penuh syukur dan kebahagiaan, kami mengumumkan pernikahan putra-putri kami.</p>

    <div class="mp-grid">
        <div class="rv">
            <div class="mp-photo">
                @if($wedding->bride_photo ?? false)
                <img src="{{ asset('storage/'.$wedding->bride_photo) }}" alt="{{ $wedding->bride_name }}">
                @else ?? @endif
            </div>
            <div class="mp-name">{{ $wedding->bride_name }}</div>
            @if($wedding->bride_fullname ?? false)<div class="mp-full">{{ $wedding->bride_fullname }}</div>@endif
            @if(($wedding->bride_father ?? false) || ($wedding->bride_mother ?? false))
            <p class="mp-par">Putri dari<br><strong style="color:var(--txt);">{{ $wedding->bride_father ?? '' }}{{ ($wedding->bride_father && $wedding->bride_mother) ? ' & ' : '' }}{{ $wedding->bride_mother ?? '' }}</strong></p>
            @endif
        </div>

        <div class="mp-amp rv">� &amp; �</div>

        <div class="rv">
            <div class="mp-photo">
                @if($wedding->groom_photo ?? false)
                <img src="{{ asset('storage/'.$wedding->groom_photo) }}" alt="{{ $wedding->groom_name }}">
                @else ?? @endif
            </div>
            <div class="mp-name">{{ $wedding->groom_name }}</div>
            @if($wedding->groom_fullname ?? false)<div class="mp-full">{{ $wedding->groom_fullname }}</div>@endif
            @if(($wedding->groom_father ?? false) || ($wedding->groom_mother ?? false))
            <p class="mp-par">Putra dari<br><strong style="color:var(--txt);">{{ $wedding->groom_father ?? '' }}{{ ($wedding->groom_father && $wedding->groom_mother) ? ' & ' : '' }}{{ $wedding->groom_mother ?? '' }}</strong></p>
            @endif
        </div>
    </div>
</section>


{{-- -- AYAT -- --}}
<section style="padding:0 24px 80px; max-width:680px; margin:0 auto;">
    <div class="ayat rv">
        <p>"{{ $wedding->customText('quote_text', 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya, dan dijadikan-Nya di antaramu rasa kasih dan sayang.') }}"</p>
        <span class="ayat-src">{{ $wedding->customText('quote_source', 'QS. Ar-Rum: 21') }}</span>
    </div>
</section>


{{-- -- ACARA -- --}}
<section class="sec" id="acara">
    <div class="fl-ornament rv">?? ?? ??</div>
    <span class="stag rv">Rangkaian Acara</span>
    <h2 class="stit rv">Agenda Hari Istimewa</h2>

    <div class="card-grid">
        @if($wedding->akad_date ?? $wedding->event_date)
        <div class="card rv">
            <span class="cicon">?</span>
            <div class="ctype">Akad Nikah</div>
            <div class="ctitle">Ijab Qabul</div>
            <div class="cinfo">
                <strong style="color:var(--txt);">{{ ($wedding->akad_date ?? $wedding->event_date)->locale('id')->translatedFormat('l, d F Y') }}</strong><br>
                {{ $wedding->akad_time ?? '08.00 � 10.00' }} WIB<br><br>
                {{ $wedding->akad_location ?? ($wedding->location ?? 'Lokasi Akad Nikah') }}
            </div>
        </div>
        @endif

        <div class="card rv">
            <span class="cicon">??</span>
            <div class="ctype">Resepsi</div>
            <div class="ctitle">{{ $wedding->customText('event_name', 'Walimatul Ursy') }}</div>
            <div class="cinfo">
                <strong style="color:var(--txt);">{{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}</strong><br>
                {{ $wedding->reception_time ?? '11.00 � 14.00' }} WIB<br><br>
                {{ $wedding->reception_location ?? $wedding->location ?? 'Lokasi Resepsi' }}
            </div>
        </div>
    </div>
</section>


{{-- -- COUNTDOWN -- --}}
<section class="sec" style="background:white; max-width:100%; padding-left:0; padding-right:0;" id="countdown">
    <div style="max-width:680px; margin:0 auto; padding:0 24px; text-align:center;">
        <span class="stag rv">Menuju Hari Bahagia</span>
        <h2 class="stit rv">Hitung Mundur</h2>
        <div class="cdg rv">
            <div class="cdi"><span class="cdn" id="f-hari">00</span><span class="cdl">Hari</span></div>
            <span class="cds">??</span>
            <div class="cdi"><span class="cdn" id="f-jam">00</span><span class="cdl">Jam</span></div>
            <span class="cds">??</span>
            <div class="cdi"><span class="cdn" id="f-menit">00</span><span class="cdl">Menit</span></div>
            <span class="cds">??</span>
            <div class="cdi"><span class="cdn" id="f-detik">00</span><span class="cdl">Detik</span></div>
        </div>
    </div>
</section>


{{-- -- GALLERY -- --}}
@php
    $gallPhotos = [];
    if (!empty($isPreview) && !empty($demoPhotos)) {
        $gallPhotos = $demoPhotos;
    } elseif (($wedding->galleries ?? collect())->count() > 0) {
        $gallPhotos = $wedding->galleries->map(fn($g) => asset('storage/'.$g->path))->toArray();
    }
@endphp
@if(($wedding->has_gallery || !empty($isPreview)) && count($gallPhotos) > 0)
<section class="sec" id="galeri">
    <div class="fl-ornament rv">?? ?? ??</div>
    <span class="stag rv">Momen Berharga</span>
    <h2 class="stit rv">Galeri Foto</h2>
    @include('invitation.partials.gallery-innovations', [
        'galleryPhotos'  => $gallPhotos,
        'galleryMode'    => 'slider',
        'galleryBgColor' => '#fdf5f8',
        'galleryAccent'  => 'rgba(221,119,160,0.25)',
    ])
</section>
@endif


{{-- -- LOKASI -- --}}
<section class="sec" id="lokasi">
    <div class="fl-ornament rv">?? ?? ??</div>
    <span class="stag rv">Temukan Kami</span>
    <h2 class="stit rv">Lokasi Acara</h2>
    @if($wedding->location)<p class="ssub rv">{{ $wedding->location }}</p>@endif

    <div class="map-fr rv">
        @if($wedding->map_embed ?? false)
        {!! $wedding->map_embed !!}
        @else
        <div class="map-ph">
            <span style="font-size:2.5rem;">??</span>
            <span style="font-family:'Cinzel',serif; font-size:.68rem; letter-spacing:.22em; color:var(--rose);">{{ $wedding->location ?? 'Lokasi Acara' }}</span>
        </div>
        @endif
    </div>
    @if($wedding->map_link ?? false)
    <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="btn-out rv">Buka di Google Maps</a>
    @endif
</section>


{{-- -- AMPLOP DIGITAL -- --}}
@if(($wedding->bank_accounts ?? collect())->count() > 0 || ($wedding->bride_bank ?? false) || ($wedding->groom_bank ?? false))
<section class="sec" id="amplop" style="background:var(--blush2); max-width:100%; padding-left:0; padding-right:0;">
    <div style="max-width:680px; margin:0 auto; padding:80px 24px; text-align:center;">
        <div class="fl-ornament rv">?? ?? ??</div>
        <span class="stag rv">Hadiah &amp; Doa</span>
        <h2 class="stit rv">Amplop Digital</h2>
        <p class="ssub rv">Kehadiran dan doa restu Anda adalah hadiah terindah bagi kami.</p>
        <div class="gift-grid">
            @if($wedding->bank_accounts ?? false)
            @foreach($wedding->bank_accounts as $acc)
            <div class="card rv">
                <span class="cicon">??</span>
                <div class="gift-bank">{{ $acc->bank_name }}</div>
                <div class="gift-norek">{{ $acc->account_number }}</div>
                <div class="gift-atas">a/n {{ $acc->account_name }}</div>
                <button class="copy-btn" onclick="flCopy('{{ $acc->account_number }}',this)">Salin Nomor</button>
            </div>
            @endforeach
            @else
            @if($wedding->bride_bank ?? false)
            <div class="card rv">
                <span class="cicon">??</span>
                <div class="gift-bank">{{ $wedding->bride_bank }}</div>
                <div class="gift-norek">{{ $wedding->bride_norek }}</div>
                <div class="gift-atas">a/n {{ $wedding->bride_name }}</div>
                <button class="copy-btn" onclick="flCopy('{{ $wedding->bride_norek }}',this)">Salin Nomor</button>
            </div>
            @endif
            @if($wedding->groom_bank ?? false)
            <div class="card rv">
                <span class="cicon">??</span>
                <div class="gift-bank">{{ $wedding->groom_bank }}</div>
                <div class="gift-norek">{{ $wedding->groom_norek }}</div>
                <div class="gift-atas">a/n {{ $wedding->groom_name }}</div>
                <button class="copy-btn" onclick="flCopy('{{ $wedding->groom_norek }}',this)">Salin Nomor</button>
            </div>
            @endif
            @endif
        </div>
    </div>
</section>
@endif


@include('invitation.partials.guestbook')

{{-- -- RSVP -- --}}
<section class="rsvp-sec" id="rsvp">
    <div class="rsvp-inner">
        <div style="text-align:center;">
            <div style="font-size:1.8rem; margin-bottom:12px; opacity:.7;">?? ?? ??</div>
            <span class="stag rv" style="color:var(--petal);">Konfirmasi &amp; Ucapan</span>
            <h2 class="stit rv" style="color:white;">Kirimkan Doa Anda</h2>
            <p class="ssub rv" style="color:rgba(255,255,255,.6);">Mohon konfirmasi kehadiran dan titipkan doa untuk kedua mempelai.</p>
        </div>

        <div class="rsvp-success" id="f-rsvp-ok">
            <div style="font-size:3rem; margin-bottom:16px;">??</div>
            <p class="fi" style="font-size:2.5rem; color:var(--petal);">Terima Kasih</p>
            <p style="color:rgba(255,255,255,.65); margin-top:8px; font-size:.88rem;">Konfirmasi dan ucapan Anda telah kami terima. Sampai jumpa! ??</p>
        </div>

        <form id="f-rsvp-form" class="rsvp-form rv" action="{{ route('rsvp.store', $wedding->slug) }}" method="POST">
            @csrf
            <div>
                <label for="f-name">Nama Lengkap</label>
                <input type="text" id="f-name" name="guest_name" value="{{ $guest->guest_name ?? '' }}" placeholder="Nama Anda" required>
            </div>
            <div>
                <label for="f-phone">Nomor WhatsApp <span style="text-transform:none;letter-spacing:0;opacity:.4;font-family:'Nunito',sans-serif;">(opsional)</span></label>
                <input type="tel" id="f-phone" name="phone" placeholder="+62 8xx xxxx xxxx">
            </div>
            <div>
                <label>Konfirmasi Kehadiran</label>
                <div class="hadir-toggle">
                    <input type="radio" name="attendance" id="f-hadir" value="hadir" checked>
                    <label for="f-hadir">Hadir</label>
                    <input type="radio" name="attendance" id="f-mungkin" value="mungkin">
                    <label for="f-mungkin">Mungkin</label>
                    <input type="radio" name="attendance" id="f-tidak" value="tidak_hadir">
                    <label for="f-tidak">Tidak Hadir</label>
                </div>
            </div>
            <div id="f-row-jml">
                <label for="f-jml">Jumlah Tamu</label>
                <input type="number" id="f-jml" name="guests_count" min="1" max="10" value="1">
            </div>
            <div>
                <label for="f-msg">Ucapan &amp; Doa</label>
                <textarea id="f-msg" name="message" placeholder="Tuliskan ucapan dan doa untuk kedua mempelai..."></textarea>
            </div>
            <button type="submit" class="rsvp-submit">?? Kirim Sekarang ??</button>
        </form>

        @if(($rsvps ?? collect())->count() > 0)
        <div class="ucapan-list" style="margin-top:56px;">
            <div style="text-align:center; margin-bottom:24px; font-size:1.5rem; opacity:.6;">?? ?? ??</div>
            <span class="stag" style="text-align:center; display:block; margin-bottom:24px; color:var(--petal);">Ucapan &amp; Doa</span>
            @foreach($rsvps as $rsvp)
            @php $msg = trim(str_replace('[RSVP]', '', $rsvp->notes ?? '')); @endphp
            <div class="ucapan-item">
                <div>
                    <span class="ucapan-nama">{{ $rsvp->guest_name }}</span>
                    <span class="ucapan-stat">� {{ $rsvp->is_attending ? '? Hadir' : '? Tidak Hadir' }}</span>
                </div>
                <p class="ucapan-msg">{{ $msg }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>


{{-- -- FOOTER -- --}}
<footer class="footer">
    <div class="fl-ornament" style="font-size:2rem; margin-bottom:20px;">?? ?? ??</div>
    <div class="footer-logo">{{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}</div>
    <p class="footer-note">
        @if($wedding->event_date){{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}<br>@endif
        @if($wedding->location){{ $wedding->location }}<br>@endif
        <em style="color:var(--rose); font-size:.9rem;">Dengan penuh cinta &amp; kebahagiaan ??</em>
    </p>
    <div class="fl-div" style="margin-top:32px;"><span class="fl-line"></span><span class="fl-dot">??</span><span class="fl-line"></span></div>
    <p class="footer-credit">Dibuat dengan ? � Laravel 11</p>
</footer>

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
window.weddingData = { eventDate: '{{ $wedding->event_date->format("Y-m-d") }}' };
</script>
<script src="{{ asset('js/floral.js') }}"></script>
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
