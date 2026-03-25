<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Undangan Fisik — {{ $wedding->bride_name }} & {{ $wedding->groom_name }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=Jost:wght@200;300;400;500&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
<style>
/* ── Force zero page margins (removes browser URL / date / time headers) ── */
@page {
    size: A5 portrait;
    margin: 0;
}

/* ── Force Chrome/Edge to print colors, backgrounds, and images ── */
* {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color-adjust: exact !important;
}

/* ═══════════════════════════════════════════════
   CONTROL PANEL (screen only)
═══════════════════════════════════════════════ */
.ctrl-panel {
    position: sticky; top: 0; z-index: 100;
    background: #1e1b2e;
    border-bottom: 1px solid rgba(255,255,255,.1);
    padding: 14px 24px;
    display: flex; flex-wrap: wrap; align-items: center; gap: 12px;
    font-family: system-ui, sans-serif;
}
.ctrl-title { color: #f1f5f9; font-weight: 700; font-size: 15px; flex: 1; }
.ctrl-sub   { color: #94a3b8; font-size: 12px; }
.ctrl-btn {
    padding: 8px 20px; border-radius: 10px; border: none;
    font-size: 13px; font-weight: 700; cursor: pointer; transition: all .2s;
}
.ctrl-btn-print {
    background: linear-gradient(135deg, #c49a6c, #a07040);
    color: #fff; box-shadow: 0 4px 16px rgba(196,154,108,.35);
}
.ctrl-btn-print:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(196,154,108,.5); }
.ctrl-btn-back {
    background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12); color: #94a3b8;
}
.ctrl-btn-back:hover { background: rgba(255,255,255,.12); color: #e2e8f0; }
.ctrl-count {
    background: rgba(196,154,108,.15); border: 1px solid rgba(196,154,108,.3);
    color: #c49a6c; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px;
}
.ctrl-filter { position: relative; }
.ctrl-filter summary {
    list-style: none; cursor: pointer; padding: 8px 16px; border-radius: 10px;
    background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
    color: #94a3b8; font-size: 13px; font-weight: 600; transition: all .2s;
}
.ctrl-filter summary:hover { background: rgba(255,255,255,.1); }
.ctrl-filter-panel {
    position: absolute; top: calc(100% + 8px); right: 0; background: #1e1b2e;
    border: 1px solid rgba(255,255,255,.12); border-radius: 14px; padding: 16px;
    min-width: 280px; max-height: 350px; overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.6); z-index: 200;
}
.ctrl-filter-item {
    display: flex; align-items: center; gap: 8px; padding: 6px 0; cursor: pointer;
    font-size: 12px; color: #cbd5e1; font-family: system-ui, sans-serif;
}
.ctrl-filter-item input { accent-color: #c49a6c; width: 14px; height: 14px; }
.ctrl-filter-all {
    padding-bottom: 8px; margin-bottom: 8px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    font-size: 12px; font-weight: 700; color: #c49a6c;
}
.filter-apply {
    margin-top: 12px; width: 100%; padding: 8px; border-radius: 8px;
    background: linear-gradient(135deg, #c49a6c, #a07040);
    color: #fff; font-weight: 700; font-size: 12px; border: none; cursor: pointer;
}

/* ═══════════════════════════════════════════════
   PAGE LAYOUT
═══════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; background: #12101e; }
.pages-wrap {
    padding: 40px 24px;
    display: flex; flex-direction: column; align-items: center; gap: 48px;
}
.empty-state {
    color: #64748b; font-family: system-ui, sans-serif;
    text-align: center; padding: 80px 24px;
}

/* ═══════════════════════════════════════════════
   A5 CARD  (148mm × 210mm)
═══════════════════════════════════════════════ */
.inv-card {
    width: 148mm;
    min-height: 210mm;
    background: #fdf7f0;
    box-shadow: 0 20px 60px rgba(0,0,0,.55), 0 4px 16px rgba(0,0,0,.3);
    position: relative;
    overflow: hidden;
    page-break-after: always;
    break-after: page;
    display: flex; flex-direction: column;
    font-family: 'Jost', sans-serif;
}

/* ── Outer gold border ── */
.inv-card::before {
    content: '';
    position: absolute; inset: 7px;
    border: 1px solid rgba(196,164,107,.45);
    pointer-events: none; z-index: 10;
}
.inv-card::after {
    content: '';
    position: absolute; inset: 10px;
    border: .5px solid rgba(196,164,107,.2);
    pointer-events: none; z-index: 10;
}

/* ── SVG Botanical corners ── */
.inv-botanical {
    position: absolute; pointer-events: none; z-index: 5;
}
.inv-botanical.tl { top: 0; left: 0; }
.inv-botanical.tr { top: 0; right: 0; transform: scaleX(-1); }
.inv-botanical.bl { bottom: 0; left: 0; transform: scaleY(-1); }
.inv-botanical.br { bottom: 0; right: 0; transform: scale(-1); }

/* ── Header (always top, light elegant) ── */
.inv-header {
    background: linear-gradient(160deg, #2c1a0e 0%, #4a2c1a 40%, #3a2218 100%);
    padding: 10px 20px 8px; text-align: center; position: relative; flex-shrink: 0;
}
.inv-header::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0; height: 1.5px;
    background: linear-gradient(90deg, transparent, rgba(196,164,107,.7), transparent);
}
.inv-header-eyebrow {
    font-size: 5.5px; letter-spacing: .55em; text-transform: uppercase;
    color: rgba(255,255,255,.6); margin-bottom: 5px; font-family: 'Jost', sans-serif;
}
.inv-header-title {
    font-family: 'Great Vibes', cursive;
    font-size: 24px; color: #fff; line-height: 1;
    text-shadow: 0 2px 10px rgba(0,0,0,.3);
}
.inv-header-goldline {
    display: flex; align-items: center; gap: 8px; justify-content: center; margin-top: 5px;
}
.inv-header-goldline .gl { flex: 1; height: .5px; background: rgba(196,164,107,.45); max-width: 36px; }
.inv-header-goldline .gd { width: 3px; height: 3px; border-radius: 50%; background: rgba(196,164,107,.65); flex: none; }

/* ── Photo section (below guest name) ── */
.inv-photos {
    display: flex; justify-content: center; align-items: flex-end;
    gap: 12px; padding: 8px 20px 4px;
    border-bottom: .5px solid rgba(196,164,107,.15);
    flex-shrink: 0;
}
.inv-photo-couple {
    width: 100%; height: 38mm;
    object-fit: cover; object-position: center top;
    display: block;
    border-top: 1px solid rgba(196,164,107,.2);
    border-bottom: 1px solid rgba(196,164,107,.2);
    filter: brightness(.85) saturate(.9);
}
.inv-pframe {
    display: flex; flex-direction: column; align-items: center; gap: 4px;
}
.inv-pframe img {
    width: 44px; height: 56px;
    object-fit: cover; object-position: center top;
    border-radius: 44px 44px 4px 4px;
    border: 1.5px solid rgba(196,164,107,.55);
    box-shadow: 0 4px 16px rgba(0,0,0,.18);
}
.inv-pframe span {
    font-size: 6px; letter-spacing: .12em; text-transform: uppercase;
    color: rgba(44,26,14,.5); font-family: 'Jost', sans-serif; font-weight: 400;
}
.inv-pamp {
    font-family: 'Great Vibes', cursive;
    font-size: 22px; color: #b07840; line-height: 1;
    margin-bottom: 12px; align-self: flex-end;
}

/* ── Guest band ── */
.inv-guest-band {
    text-align: center; padding: 6px 20px 5px;
    position: relative;
}
.inv-guest-band::before {
    content: '';
    display: block; height: 1px; margin: 0 auto 6px;
    width: 80%; background: linear-gradient(90deg, transparent, rgba(196,164,107,.4), transparent);
}
.inv-guest-band::after {
    content: '';
    display: block; height: 1px; margin: 5px auto 0;
    width: 80%; background: linear-gradient(90deg, transparent, rgba(196,164,107,.4), transparent);
}
.inv-kepada {
    font-size: 6px; letter-spacing: .4em; text-transform: uppercase;
    color: rgba(44,26,14,.45); margin-bottom: 4px; font-family: 'Jost', sans-serif;
}
.inv-guest-name {
    font-family: 'Cormorant Garamond', serif;
    font-size: 16px; font-weight: 600; font-style: italic;
    color: #7a3a24; line-height: 1.2;
}
.inv-guest-group {
    font-size: 6.5px; color: rgba(95,55,30,.45);
    letter-spacing: .08em; margin-top: 2px; font-family: 'Jost', sans-serif;
}

/* ── Body ── */
.inv-body {
    flex: 1; padding: 6px 22px 4px;
    text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0;
}

.inv-bismillah {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic; font-size: 8px;
    color: #1a1a1a; letter-spacing: .02em;
    margin-bottom: 5px; line-height: 1.6;
    padding: 0 4px;
}

.inv-ornament {
    display: flex; align-items: center; gap: 6px; justify-content: center; width: 100%;
    margin: 4px 0;
}
.inv-ornament .line { flex: 1; height: .5px; background: linear-gradient(90deg, transparent, rgba(196,164,107,.5)); }
.inv-ornament .line.r { background: linear-gradient(90deg, rgba(196,164,107,.5), transparent); }
.inv-ornament .lozenge {
    width: 5px; height: 5px; background: rgba(196,164,107,.6);
    transform: rotate(45deg); flex-shrink: 0;
}

.inv-names {
    font-family: 'Cormorant Garamond', serif;
    font-size: 20px; font-weight: 400; font-style: italic;
    color: #2c1a0e; line-height: 1.2;
}
.inv-amp {
    font-family: 'Great Vibes', cursive;
    font-size: 24px; color: #b07840; display: block; line-height: 1.1; margin: 0;
}

.inv-parents {
    font-size: 7px; color: rgba(44,26,14,.5); line-height: 1.6;
    letter-spacing: .02em; margin-top: 5px; font-family: 'Jost', sans-serif; font-weight: 300;
}

/* ── Event section ── */
.inv-divider {
    width: calc(100% - 44px); margin: 4px auto;
    height: .5px; background: linear-gradient(90deg, transparent, rgba(196,164,107,.4), transparent);
    flex-shrink: 0;
}

.inv-events {
    display: flex; width: 100%; flex-shrink: 0;
    padding: 0 16px;
}
.inv-event-col {
    flex: 1; text-align: center; padding: 4px 8px;
}
.inv-event-col + .inv-event-col {
    border-left: .5px solid rgba(196,164,107,.25);
}
.inv-event-tag {
    display: inline-block;
    font-size: 5.5px; letter-spacing: .35em; text-transform: uppercase;
    color: #b07840; border: .5px solid rgba(176,120,64,.4);
    padding: 2px 7px; border-radius: 20px; margin-bottom: 5px;
    font-family: 'Jost', sans-serif; font-weight: 400;
}
.inv-event-date {
    font-family: 'Cormorant Garamond', serif;
    font-size: 10px; font-weight: 600; color: #2c1a0e; line-height: 1.45;
}
.inv-event-time-big {
    font-family: 'Playfair Display', serif;
    font-size: 16px; font-weight: 700; color: #b07840; line-height: 1;
    margin: 2px 0;
}
.inv-event-loc {
    font-size: 6.5px; color: rgba(44,26,14,.5); line-height: 1.5; margin-top: 3px;
    font-family: 'Jost', sans-serif; font-weight: 300;
}

/* ── QR strip ── */
.inv-qr-strip {
    display: flex; align-items: center; gap: 10px;
    padding: 5px 14px 6px; margin-top: auto;
    border-top: .5px solid rgba(196,164,107,.2);
    flex-shrink: 0;
}
.inv-qr-box {
    width: 46px; height: 46px; flex-shrink: 0;
    border: 1px solid rgba(196,164,107,.4);
    border-radius: 4px; overflow: hidden; background: #fff;
    padding: 2px;
}
.inv-qr-box img { width: 100%; height: 100%; display: block; }
.inv-qr-info { flex: 1; text-align: left; }
.inv-qr-label {
    font-size: 5.5px; letter-spacing: .3em; text-transform: uppercase;
    color: rgba(44,26,14,.4); margin-bottom: 2px; font-family: 'Jost', sans-serif;
}
.inv-qr-url {
    font-size: 7px; color: #b07840; word-break: break-all; line-height: 1.5;
    font-family: 'Jost', sans-serif; font-weight: 400;
}
.inv-qr-note {
    font-size: 6px; color: rgba(44,26,14,.38); margin-top: 2px;
    line-height: 1.5; font-family: 'Jost', sans-serif; font-weight: 300;
}

/* ── Footer ── */
.inv-footer {
    background: linear-gradient(160deg, #2c1a0e, #3d2318);
    padding: 5px 20px; text-align: center; flex-shrink: 0;
}
.inv-footer-text {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic; font-size: 8px; color: rgba(255,255,255,.55);
    letter-spacing: .05em;
}
.inv-footer-credit {
    font-size: 5.5px; color: rgba(255,255,255,.25); letter-spacing: .2em;
    text-transform: uppercase; margin-top: 2px; font-family: 'Jost', sans-serif;
}

/* ═══════════════════════════════════════════════
   PRINT CSS
═══════════════════════════════════════════════ */
@media print {
    body { background: #fff; margin: 0; }
    .ctrl-panel { display: none !important; }
    .pages-wrap { padding: 0; gap: 0; }
    .inv-card {
        box-shadow: none; width: 148mm; min-height: 210mm;
        page-break-after: always; break-after: page; margin: 0;
    }
    .inv-card:last-child { page-break-after: avoid; break-after: avoid; }
    /* Ensure images are visible when printing */
    .inv-photo-couple,
    .inv-pframe img,
    .inv-qr-box img {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    .inv-photo-couple {
        filter: brightness(.85) saturate(.9) !important;
    }
}
</style>
</head>
<body>

{{-- ══════ CONTROL PANEL ══════ --}}
<div class="ctrl-panel" id="ctrl-panel">
    <div>
        <div class="ctrl-title">🌸 Cetak Undangan Fisik</div>
        <div class="ctrl-sub">{{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}</div>
    </div>

    <span class="ctrl-count">{{ $guests->count() }} kartu dipilih</span>

    {{-- Filter tamu --}}
    <details class="ctrl-filter" id="filter-details">
        <summary>🔍 Filter Tamu</summary>
        <form method="GET" action="{{ url()->current() }}" style="display:contents;">
            <div class="ctrl-filter-panel">
                <label class="ctrl-filter-item ctrl-filter-all">
                    <input type="checkbox" id="check-all"> Pilih Semua
                </label>
                @foreach($allGuests as $g)
                <label class="ctrl-filter-item">
                    <input type="checkbox" name="guests[]" value="{{ $g->id }}"
                           {{ $guests->contains('id', $g->id) ? 'checked' : '' }}>
                    {{ $g->guest_name }}
                    @if($g->group_name) <span style="color:#64748b"> · {{ $g->group_name }}</span>@endif
                </label>
                @endforeach
                <button type="submit" class="filter-apply">Terapkan Filter</button>
            </div>
        </form>
    </details>

    <button class="ctrl-btn ctrl-btn-print" onclick="printWhenReady()">🖨️ Cetak Sekarang</button>
    <button class="ctrl-btn ctrl-btn-back" onclick="history.back()">← Kembali</button>

    {{-- Print tip --}}
    <div style="width:100%; background:rgba(196,164,107,.08); border:1px solid rgba(196,164,107,.25); border-radius:10px; padding:10px 16px; display:flex; align-items:flex-start; gap:10px;">
        <span style="font-size:16px; flex-shrink:0;">💡</span>
        <div style="font-size:12px; color:#94a3b8; line-height:1.6;">
            <strong style="color:#c49a6c;">Agar tanggal &amp; URL tidak muncul saat cetak:</strong><br>
            Di dialog print Chrome → klik <strong style="color:#e2e8f0;">"More settings"</strong> → matikan <strong style="color:#e2e8f0;">"Headers and footers"</strong>. Ini pengaturan browser yang tidak bisa diubah lewat kode.
        </div>
    </div>
</div>

{{-- ══════ CARDS ══════ --}}
<div class="pages-wrap" id="pages-wrap">

@if($guests->isEmpty())
<div class="empty-state">
    <div style="font-size:48px;margin-bottom:12px;">📭</div>
    <div style="color:#e07a94; font-size:16px; font-weight:700; margin-bottom:6px;">Belum ada tamu</div>
    <div style="font-size:13px;">Tambahkan daftar tamu terlebih dahulu sebelum mencetak undangan fisik.</div>
</div>
@else
@php
    $eventDate = $wedding->event_date;
    $akadDate  = $wedding->akad_date  ?? $eventDate;
    $resepsi   = $wedding->reception_date ?? $eventDate;

    $akadTime    = $wedding->akad_time    ?? '08.00';
    $resepsiTime = $wedding->reception_time ?? '10.00';
    $resepsiLoc  = $wedding->reception_location ?? $wedding->location ?? '';
    $akadLoc     = $wedding->akad_location ?? $wedding->location ?? '';

    $eventStr = $eventDate
        ? $eventDate->locale('id')->translatedFormat('l, d F Y')
        : '-';

    $couplePhoto  = $wedding->couple_photo  ? asset('storage/'.$wedding->couple_photo)  : null;
    $bridePhoto   = $wedding->bride_photo    ? asset('storage/'.$wedding->bride_photo)   : null;
    $groomPhoto   = $wedding->groom_photo    ? asset('storage/'.$wedding->groom_photo)   : null;
@endphp

@foreach($guests as $guest)
@php
    $guestSlug = \Illuminate\Support\Str::slug($guest->guest_name);
    $guestUrl  = url('/' . $wedding->slug . '?to=' . $guestSlug);
    $qrUrl     = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&ecc=H&margin=4&data=' . urlencode($guestUrl);
    $shortUrl  = parse_url($guestUrl, PHP_URL_HOST) . '/' . $wedding->slug . '?to=' . $guestSlug;
@endphp

<div class="inv-card">

    {{-- SVG botanical corners --}}
    @php
    $botanicalSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
      <path d="M4 68 Q8 40 36 36 Q40 8 68 4" stroke="rgba(176,120,64,.35)" stroke-width=".8" fill="none"/>
      <path d="M4 56 Q12 36 28 30" stroke="rgba(176,120,64,.25)" stroke-width=".6" fill="none"/>
      <path d="M18 68 Q24 52 36 44" stroke="rgba(176,120,64,.2)" stroke-width=".6" fill="none"/>
      <ellipse cx="28" cy="30" rx="5" ry="3" transform="rotate(-35 28 30)" fill="rgba(176,120,64,.2)"/>
      <ellipse cx="36" cy="44" rx="4" ry="2.5" transform="rotate(20 36 44)" fill="rgba(176,120,64,.18)"/>
      <ellipse cx="14" cy="58" rx="3.5" ry="2" transform="rotate(-55 14 58)" fill="rgba(176,120,64,.15)"/>
      <circle cx="36" cy="36" r="1.5" fill="rgba(196,164,107,.4)"/>
    </svg>';
    @endphp
    <span class="inv-botanical tl">{!! $botanicalSvg !!}</span>
    <span class="inv-botanical tr">{!! $botanicalSvg !!}</span>
    <span class="inv-botanical bl">{!! $botanicalSvg !!}</span>
    <span class="inv-botanical br">{!! $botanicalSvg !!}</span>

    {{-- ── Header: selalu di atas ── --}}
    <div class="inv-header">
        <div class="inv-header-eyebrow">— Undangan Pernikahan —</div>
        <div class="inv-header-title">Wedding Invitation</div>
        <div class="inv-header-goldline">
            <span class="gl"></span>
            <div class="gd"></div><div class="gd"></div><div class="gd"></div>
            <span class="gl"></span>
        </div>
    </div>

    {{-- ── Guest name ── --}}
    <div class="inv-guest-band">
        <div class="inv-kepada">Kepada Yth.</div>
        <div class="inv-guest-name">{{ $guest->guest_name }}</div>
        @if($guest->group_name)
        <div class="inv-guest-group">{{ $guest->group_name }}</div>
        @endif
    </div>

    {{-- ── Foto pengantin ── --}}
    @if($couplePhoto)
    <img class="inv-photo-couple" src="{{ $couplePhoto }}" alt="{{ $wedding->bride_name }} &amp; {{ $wedding->groom_name }}" loading="eager">
    @elseif($bridePhoto || $groomPhoto)
    <div class="inv-photos">
        @if($bridePhoto)
        <div class="inv-pframe">
            <img src="{{ $bridePhoto }}" alt="{{ $wedding->bride_name }}" loading="eager">
            <span>{{ $wedding->bride_name }}</span>
        </div>
        @endif
        @if($bridePhoto && $groomPhoto)
        <span class="inv-pamp">&amp;</span>
        @endif
        @if($groomPhoto)
        <div class="inv-pframe">
            <img src="{{ $groomPhoto }}" alt="{{ $wedding->groom_name }}" loading="eager">
            <span>{{ $wedding->groom_name }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- ── Body ── --}}
    <div class="inv-body">
        <div class="inv-bismillah">{{ $wedding->opening_text ?: 'Dengan memohon rahmat dan ridho Allah SWT' }}</div>

        <div class="inv-ornament">
            <span class="line"></span>
            <div class="lozenge"></div>
            <span class="line r"></span>
        </div>

        <div class="inv-names">
            {{ $wedding->bride_name }}
            <span class="inv-amp">&amp;</span>
            {{ $wedding->groom_name }}
        </div>

        @if($wedding->bride_parent || $wedding->groom_parent)
        <div class="inv-parents">
            @if($wedding->bride_parent)Putri dari {{ $wedding->bride_parent }}<br>@endif
            @if($wedding->groom_parent)Putra dari {{ $wedding->groom_parent }}@endif
        </div>
        @endif

        <div class="inv-ornament" style="margin-top:10px;">
            <span class="line"></span>
            <div class="lozenge"></div>
            <span class="line r"></span>
        </div>
    </div>

    {{-- ── Events ── --}}
    <div class="inv-divider"></div>
    <div class="inv-events">
        @if($akadDate)
        <div class="inv-event-col">
            <div class="inv-event-tag">Akad Nikah</div>
            <div class="inv-event-date">{{ $akadDate->locale('id')->translatedFormat('l') }}<br>{{ $akadDate->locale('id')->translatedFormat('d F Y') }}</div>
            <div class="inv-event-time-big">{{ $akadTime }}</div>
            @if($akadLoc)<div class="inv-event-loc">{{ Str::limit($akadLoc, 45) }}</div>@endif
        </div>
        @endif

        @if($resepsi && (!$akadDate || $resepsi->ne($akadDate)))
        <div class="inv-event-col">
            <div class="inv-event-tag">Resepsi</div>
            <div class="inv-event-date">{{ $resepsi->locale('id')->translatedFormat('l') }}<br>{{ $resepsi->locale('id')->translatedFormat('d F Y') }}</div>
            <div class="inv-event-time-big">{{ $resepsiTime }}</div>
            @if($resepsiLoc)<div class="inv-event-loc">{{ Str::limit($resepsiLoc, 45) }}</div>@endif
        </div>
        @endif

        @if($akadDate && $resepsi && $akadDate->eq($resepsi))
        <div class="inv-event-col">
            <div class="inv-event-tag">Akad &amp; Resepsi</div>
            <div class="inv-event-date">{{ $akadDate->locale('id')->translatedFormat('l') }}<br>{{ $akadDate->locale('id')->translatedFormat('d F Y') }}</div>
            <div class="inv-event-time-big">{{ $akadTime }}</div>
            @if($akadLoc)<div class="inv-event-loc">{{ Str::limit($akadLoc, 55) }}</div>@endif
        </div>
        @endif
    </div>
    <div class="inv-divider"></div>

    {{-- ── QR strip ── --}}
    <div class="inv-qr-strip">
        <div class="inv-qr-box">
            <img src="{{ $qrUrl }}" alt="QR {{ $guest->guest_name }}" loading="eager">
        </div>
        <div class="inv-qr-info">
            <div class="inv-qr-label">🔗 Undangan Digital Anda</div>
            <div class="inv-qr-url">{{ $shortUrl }}</div>
            <div class="inv-qr-note">Scan QR atau ketik URL di atas untuk membuka undangan digital lengkap.</div>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="inv-footer">
        <div class="inv-footer-text">Merupakan kehormatan &amp; kebahagiaan atas kehadiran Anda</div>
        <div class="inv-footer-credit">TretanInvite · Undangan Digital &amp; Fisik</div>
    </div>

</div>

@endforeach
@endif

</div>{{-- /.pages-wrap --}}

<script @nonce>
// Wait for all images to fully load before opening print dialog
function printWhenReady() {
    var imgs = document.querySelectorAll('.pages-wrap img');
    var totalImgs = imgs.length;
    if (totalImgs === 0) { window.print(); return; }

    var btn = document.querySelector('.ctrl-btn-print');
    btn.textContent = '⏳ Memuat gambar...';
    btn.disabled = true;

    var loaded = 0;
    function onLoad() {
        loaded++;
        if (loaded >= totalImgs) {
            // Extra 300ms buffer for rendering
            setTimeout(function () {
                btn.textContent = '🖨️ Cetak Sekarang';
                btn.disabled = false;
                window.print();
            }, 300);
        }
    }
    imgs.forEach(function (img) {
        if (img.complete && img.naturalWidth > 0) { onLoad(); }
        else {
            img.addEventListener('load', onLoad);
            img.addEventListener('error', onLoad); // count errors too so we don't hang
        }
    });
}

// "Check All" toggle
var checkAll = document.getElementById('check-all');
if (checkAll) {
    checkAll.addEventListener('change', function () {
        document.querySelectorAll('.ctrl-filter-panel input[name="guests[]"]')
            .forEach(function (c) { c.checked = checkAll.checked; });
    });
}
// Close panel when clicking outside
document.addEventListener('click', function (e) {
    var det = document.getElementById('filter-details');
    if (det && det.open && !det.contains(e.target)) det.removeAttribute('open');
});
</script>
</body>
</html>
