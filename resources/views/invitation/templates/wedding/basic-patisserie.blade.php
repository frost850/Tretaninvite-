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

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">🌸</span></div>

    {{-- ══ MEMPELAI (no social links) ══ --}}
    <section class="vp-sec vp-sec-rose" id="mempelai">
        <div class="vp-rv"><span class="vp-pill">🌸 Yang Berbahagia</span></div>
        <h2 class="vp-title vp-rv">Mempelai</h2>
        <p class="vp-sub vp-rv">Dengan penuh syukur kami mengumumkan jalinan cinta kami</p>

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
            </div>
        </div>
    </section>

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">✿</span></div>

    {{-- ══ ACARA (akad + resepsi only — no dresscode, no calendar) ══ --}}
    <section class="vp-sec vp-sec-rose" id="acara">
        <div class="vp-rv"><span class="vp-pill">📜 Rangkaian Acara</span></div>
        <h2 class="vp-title vp-rv">Hari Istimewa</h2>
        <p class="vp-sub vp-rv">Dengan penuh kebahagiaan kami mengundang Anda hadir bersama kami</p>

        @php
            $akadDate    = $wedding->akad_date    ?? $eventDate;
            $resepsiDate = $wedding->reception_date ?? $eventDate;
        @endphp

        <div class="vp-event-grid vp-rv">
            @if($akadDate)
            <div class="vp-event-card">
                <span class="vp-event-icon">🌙</span>
                <div class="vp-event-type">Akad Nikah</div>
                <div class="vp-event-name">Ijab Qabul</div>
                <div class="vp-event-row">
                    <span class="vp-event-row-dot">✿</span>
                    <div>
                        <strong>{{ \Carbon\Carbon::parse($akadDate)->locale('id')->translatedFormat('l, d F Y') }}</strong>
                        &nbsp; {{ trim(preg_replace('/\s*wib\s*$/i', '', $wedding->akad_time ?? '08.00 – 10.00')) }} WIB
                    </div>
                </div>
                <div class="vp-event-row">
                    <span class="vp-event-row-dot">📍</span>
                    <div>{{ $wedding->akad_location ?? $location }}</div>
                </div>
            </div>
            @endif

            @if($resepsiDate)
            <div class="vp-event-card">
                <span class="vp-event-icon">🌸</span>
                <div class="vp-event-type">Resepsi</div>
                <div class="vp-event-name">{{ $wedding->customText('event_name', 'Walimatul Ursy') }}</div>
                <div class="vp-event-row">
                    <span class="vp-event-row-dot">✿</span>
                    <div>
                        <strong>{{ \Carbon\Carbon::parse($resepsiDate)->locale('id')->translatedFormat('l, d F Y') }}</strong>
                        &nbsp; {{ trim(preg_replace('/\s*wib\s*$/i', '', $wedding->reception_time ?? '11.00 – 14.00')) }} WIB
                    </div>
                </div>
                <div class="vp-event-row">
                    <span class="vp-event-row-dot">📍</span>
                    <div>{{ $wedding->reception_location ?? $location }}</div>
                </div>
            </div>
            @endif
        </div>
    </section>

    <div class="vp-sep" aria-hidden="true"><span class="vp-sep-icon">🌸</span></div>

    {{-- ══ LOKASI ══ --}}
    <section class="vp-sec vp-sec-mint" id="lokasi">
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
        <p class="vp-sub vp-rv">Mohon konfirmasi kehadiran Anda agar kami dapat mempersiapkan segalanya.</p>

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
                            var hd  = data.get('vr_hadir') || '';
                            var jml = data.get('jumlah')   || '1';
                            var lbl = hd === 'hadir' ? '✓ Hadir' : hd === 'tidak_hadir' ? '✕ Tidak Hadir' : '? Belum Pasti';
                            sumEl.innerHTML = lbl + (hd === 'hadir' ? ' · ' + jml + ' orang' : '');
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
