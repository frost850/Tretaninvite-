@extends('invitation.layout')

@push('fonts')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=playfair-display:400,400i,600|source-sans-3:300,400,600&display=swap" rel="stylesheet" />
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/minimal.css') }}">
<link rel="stylesheet" href="{{ asset('css/photo-bg-slideshow.css') }}">
@endpush

@section('content')

<div id="photo-bg-reel"></div>
<div id="pbr-dots"></div>

{{-- -- HERO -- --}}
<section class="hero" id="top">
    @if($guest)
    <div style="margin-bottom:56px; text-align:center; width:100%; max-width:480px;">
        <div class="rule" style="margin-bottom:20px;"><span class="rl"></span></div>
        <span class="stag">Kepada Yang Terhormat</span>
        <p class="fp rv" style="font-size:clamp(1.3rem,3.5vw,1.9rem); color:var(--txt); margin-top:8px; font-style:italic;">{{ $guest->guest_name }}</p>
        <div class="rule" style="margin-top:20px;"><span class="rl"></span></div>
    </div>
    @endif

    <span class="stag rv">Kami mengundang Anda</span>

    <h1 class="rv" style="margin-top:8px;">{{ $wedding->bride_name }}</h1>
    <span class="hero-amp rv">dan</span>
    <h1 class="rv">{{ $wedding->groom_name }}</h1>

    @if($wedding->event_date)
    <div class="hero-date rv">
        {{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}
    </div>
    @endif
    @if($wedding->location)
    <p class="rv" style="margin-top:10px; font-size:.72rem; letter-spacing:.22em; text-transform:uppercase; color:var(--mut); font-weight:300;">{{ $wedding->location }}</p>
    @endif

    <a href="#mempelai" class="hero-cta rv" style="margin-top:48px;">
        Buka Undangan &nbsp; ?
    </a>
</section>


{{-- -- MEMPELAI -- --}}
<section class="sec" id="mempelai">
    <div class="rule rv"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Yang Berbahagia</span><span class="rl"></span></div>
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
            <p class="mp-par">Putri dari<br>{{ $wedding->bride_father ?? '' }}{{ ($wedding->bride_father && $wedding->bride_mother) ? ' &amp; ' : '' }}{{ $wedding->bride_mother ?? '' }}</p>
            @endif
        </div>

        <div class="mp-amp rv">�&nbsp;&amp;&nbsp;�</div>

        <div class="rv">
            <div class="mp-photo">
                @if($wedding->groom_photo ?? false)
                <img src="{{ asset('storage/'.$wedding->groom_photo) }}" alt="{{ $wedding->groom_name }}">
                @else ?? @endif
            </div>
            <div class="mp-name">{{ $wedding->groom_name }}</div>
            @if($wedding->groom_fullname ?? false)<div class="mp-full">{{ $wedding->groom_fullname }}</div>@endif
            @if(($wedding->groom_father ?? false) || ($wedding->groom_mother ?? false))
            <p class="mp-par">Putra dari<br>{{ $wedding->groom_father ?? '' }}{{ ($wedding->groom_father && $wedding->groom_mother) ? ' &amp; ' : '' }}{{ $wedding->groom_mother ?? '' }}</p>
            @endif
        </div>
    </div>
</section>


{{-- -- AYAT -- --}}
<section style="padding:0 32px 96px; max-width:640px; margin:0 auto;">
    <div class="ayat rv">
        <p>"{{ $wedding->customText('quote_text', 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya, dan dijadikan-Nya di antaramu rasa kasih dan sayang.') }}"</p>
        <span class="ayat-src">{{ $wedding->customText('quote_source', 'QS. Ar-Rum : 21') }}</span>
    </div>
</section>


{{-- -- ACARA -- --}}
<section class="sec" style="background:var(--bg2);" id="acara">
    <div class="rule rv"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Rangkaian Acara</span><span class="rl"></span></div>
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
            <span class="cicon">?</span>
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
<section class="sec" id="countdown">
    <div class="rule rv"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Menuju Hari Bahagia</span><span class="rl"></span></div>
    <h2 class="stit rv">Hitung Mundur</h2>
    <div class="cdg rv">
        <div class="cdi"><span class="cdn" id="n-hari">00</span><span class="cdl">Hari</span></div>
        <div class="cdi"><span class="cdn" id="n-jam">00</span><span class="cdl">Jam</span></div>
        <div class="cdi"><span class="cdn" id="n-menit">00</span><span class="cdl">Menit</span></div>
        <div class="cdi"><span class="cdn" id="n-detik">00</span><span class="cdl">Detik</span></div>
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
<section class="sec" style="background:var(--bg2);" id="galeri">
    <div class="rule rv"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Momen Berharga</span><span class="rl"></span></div>
    <h2 class="stit rv">Galeri Foto</h2>
    @include('invitation.partials.gallery-innovations', [
        'galleryPhotos'  => $gallPhotos,
        'galleryMode'    => 'slider',
        'galleryBgColor' => '#fafafa',
        'galleryAccent'  => 'rgba(0,0,0,0.15)',
    ])
</section>
@endif


{{-- -- LOKASI -- --}}
<section class="sec" id="lokasi">
    <div class="rule rv"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Temukan Kami</span><span class="rl"></span></div>
    <h2 class="stit rv">Lokasi Acara</h2>
    @if($wedding->location)<p class="ssub rv">{{ $wedding->location }}</p>@endif

    <div class="map-fr rv">
        @if($wedding->map_embed ?? false)
        {!! $wedding->map_embed !!}
        @else
        <div class="map-ph">
            <span style="font-size:1.8rem; opacity:.3;">?</span>
            <span style="font-size:.7rem; letter-spacing:.25em; text-transform:uppercase; color:var(--mut);">{{ $wedding->location ?? 'Lokasi Acara' }}</span>
        </div>
        @endif
    </div>
    @if($wedding->map_link ?? false)
    <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="btn-out rv">Buka di Google Maps</a>
    @endif
</section>


{{-- -- AMPLOP DIGITAL -- --}}
@if(($wedding->bank_accounts ?? collect())->count() > 0 || ($wedding->bride_bank ?? false) || ($wedding->groom_bank ?? false))
<section id="amplop" style="background:var(--bg2); border-top:1px solid var(--ln); padding:96px 32px;">
    <div style="max-width:640px; margin:0 auto; text-align:center;">
        <div class="rule rv"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Hadiah &amp; Doa</span><span class="rl"></span></div>
        <h2 class="stit rv">Amplop Digital</h2>
        <p class="ssub rv">Kehadiran dan doa restu Anda adalah hadiah terindah bagi kami.</p>
        <div class="gift-grid" style="margin-top:40px;">
            @if($wedding->bank_accounts ?? false)
            @foreach($wedding->bank_accounts as $acc)
            <div class="gift-card rv">
                <div class="gift-bank">{{ $acc->bank_name }}</div>
                <div class="gift-norek">{{ $acc->account_number }}</div>
                <div class="gift-atas">a/n {{ $acc->account_name }}</div>
                <button class="copy-btn" onclick="mnCopy('{{ $acc->account_number }}',this)">Salin Nomor</button>
            </div>
            @endforeach
            @else
            @if($wedding->bride_bank ?? false)
            <div class="gift-card rv">
                <div class="gift-bank">{{ $wedding->bride_bank }}</div>
                <div class="gift-norek">{{ $wedding->bride_norek }}</div>
                <div class="gift-atas">a/n {{ $wedding->bride_name }}</div>
                <button class="copy-btn" onclick="mnCopy('{{ $wedding->bride_norek }}',this)">Salin Nomor</button>
            </div>
            @endif
            @if($wedding->groom_bank ?? false)
            <div class="gift-card rv">
                <div class="gift-bank">{{ $wedding->groom_bank }}</div>
                <div class="gift-norek">{{ $wedding->groom_norek }}</div>
                <div class="gift-atas">a/n {{ $wedding->groom_name }}</div>
                <button class="copy-btn" onclick="mnCopy('{{ $wedding->groom_norek }}',this)">Salin Nomor</button>
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
            <div class="rule rv"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Konfirmasi &amp; Ucapan</span><span class="rl"></span></div>
            <h2 class="stit rv">Kirimkan Doa Anda</h2>
            <p class="ssub rv">Mohon konfirmasi kehadiran dan titipkan doa untuk kedua mempelai.</p>
        </div>

        <div class="rsvp-success" id="n-rsvp-ok">
            <p class="fp" style="font-size:1.6rem; color:var(--txt);">Terima Kasih</p>
            <p style="color:var(--mut); margin-top:8px; font-size:.88rem; font-weight:300;">Konfirmasi dan ucapan Anda telah kami terima. Sampai jumpa di hari bahagia kami.</p>
        </div>

        <form id="n-rsvp-form" class="rsvp-form rv" action="{{ route('rsvp.store', $wedding->slug) }}" method="POST">
            @csrf
            <div>
                <label for="n-name">Nama Lengkap</label>
                <input type="text" id="n-name" name="guest_name" value="{{ $guest->guest_name ?? '' }}" placeholder="Nama Anda" required>
            </div>
            <div>
                <label for="n-phone">Nomor WhatsApp <span style="text-transform:none;letter-spacing:0;opacity:.5;">(opsional)</span></label>
                <input type="tel" id="n-phone" name="phone" placeholder="+62 8xx xxxx xxxx">
            </div>
            <div>
                <label>Konfirmasi Kehadiran</label>
                <div class="hadir-toggle">
                    <input type="radio" name="attendance" id="n-hadir" value="hadir" checked>
                    <label for="n-hadir">Hadir</label>
                    <input type="radio" name="attendance" id="n-mungkin" value="mungkin">
                    <label for="n-mungkin">Mungkin</label>
                    <input type="radio" name="attendance" id="n-tidak" value="tidak_hadir">
                    <label for="n-tidak">Tidak Hadir</label>
                </div>
            </div>
            <div id="n-row-jml">
                <label for="n-jml">Jumlah Tamu</label>
                <input type="number" id="n-jml" name="guests_count" min="1" max="10" value="1">
            </div>
            <div>
                <label for="n-msg">Ucapan &amp; Doa</label>
                <textarea id="n-msg" name="message" placeholder="Tuliskan ucapan dan doa untuk kedua mempelai..."></textarea>
            </div>
            <button type="submit" class="rsvp-submit">Kirim Sekarang</button>
        </form>

        @if(($rsvps ?? collect())->count() > 0)
        <div style="margin-top:64px;">
            <div class="rule" style="margin-bottom:32px;"><span class="rl"></span><span class="stag" style="white-space:nowrap;">Ucapan &amp; Doa</span><span class="rl"></span></div>
            <div class="ucapan-list">
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
        </div>
        @endif
    </div>
</section>


{{-- -- FOOTER -- --}}
<footer class="footer">
    <div class="rule" style="margin-bottom:28px;"><span class="rl" style="max-width:80px;"></span></div>
    <div class="footer-logo fp">{{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}</div>
    <p class="footer-note">
        @if($wedding->event_date){{ $wedding->event_date->locale('id')->translatedFormat('l, d F Y') }}<br>@endif
        @if($wedding->location){{ $wedding->location }}<br>@endif
        <em style="color:var(--txt2); font-weight:400;">Dengan penuh cinta &amp; kebahagiaan</em>
    </p>
    <div class="rule" style="margin-top:36px;"><span class="rl" style="max-width:40px;"></span></div>
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
<script src="{{ asset('js/minimal.js') }}"></script>
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
