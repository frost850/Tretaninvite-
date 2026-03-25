{{--
    VIP Royal – Gallery Partial
    Included from: vip-royal.blade.php
    Variables expected:
        $gallPhotos  – array of photo URLs (already checked > 0 by parent)

    Layout: 3 cards, each flipping independently at staggered intervals.
    No two cards ever show the same photo simultaneously.
--}}
@php
    $gCount = count($gallPhotos);
    $s0 = 0 % $gCount;
    $s1 = 1 % $gCount;
    $s2 = 2 % $gCount;
@endphp

<style>
/* =======================================================
   VRG – VIP Royal Gallery  |  3-Card Staggered Flip
   ======================================================= */

/* Section-level transparent background */
.vr-gallery-sec {
    position: relative;
    overflow: hidden;
}
/* Couple photo background layers */
.vrg-bg {
    position: absolute; inset: 0;
    pointer-events: none; z-index: 0;
    overflow: hidden;
}
.vrg-bg-bride,
.vrg-bg-groom {
    position: absolute; top: 0; bottom: 0;
    width: 55%;
    background-size: cover;
    background-position: center top;
    filter: blur(28px) saturate(0.55) brightness(0.38);
    /* Fade smoothly toward center */
    opacity: 0.55;
}
.vrg-bg-bride {
    left: -5%;
    -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%);
    mask-image:         linear-gradient(to right, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%);
}
.vrg-bg-groom {
    right: -5%;
    -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%);
    mask-image:         linear-gradient(to left, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%);
}
/* Dark veil so dark theme dominates */
.vrg-bg-veil {
    position: absolute; inset: 0;
    background: rgba(5, 8, 24, 0.78);
    /* Radial punch-through: very slightly lighter in center where the cards are */
    background: radial-gradient(
        ellipse 70% 60% at 50% 45%,
        rgba(5,8,30,0.70) 0%,
        rgba(3,5,18,0.84) 100%
    );
}
/* Gold rim glow top & bottom */
.vrg-bg::before,
.vrg-bg::after {
    content: '';
    position: absolute; left: 0; right: 0; height: 1px;
    background: linear-gradient(to right, transparent 10%, rgba(201,168,76,.25) 50%, transparent 90%);
    z-index: 2;
}
.vrg-bg::before { top: 0; }
.vrg-bg::after  { bottom: 0; }

.vrg-scene {
    position: relative; width: 100%;
    padding: 20px 0 64px; overflow: hidden;
    z-index: 1; /* above .vrg-bg */
}

/* Floating geometry */
.vrg-geo {
    position: absolute; pointer-events: none; opacity: .11;
    animation: vrg-gfloat linear infinite;
}
@keyframes vrg-gfloat {
    0%   { transform: translateY(0)     rotate(0deg)   scale(1);   }
    33%  { transform: translateY(-24px) rotate(120deg) scale(1.1); }
    66%  { transform: translateY(12px)  rotate(240deg) scale(.9);  }
    100% { transform: translateY(0)     rotate(360deg) scale(1);   }
}

/* Stage */
.vrg-stage {
    display: flex; align-items: center; justify-content: center;
    gap: 16px; padding: 0 12px; position: relative; z-index: 2;
}

/* Arrow buttons */
.vrg-arr {
    background: rgba(201,168,76,.1); border: 1px solid rgba(201,168,76,.35);
    color: #c9a84c; width: 40px; height: 40px; border-radius: 50%;
    font-size: 22px; display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0; user-select: none;
    transition: background .2s, transform .2s, box-shadow .2s;
}
.vrg-arr:hover { background: rgba(201,168,76,.28); box-shadow: 0 0 18px rgba(201,168,76,.22); transform: scale(1.1); }
@media (max-width:480px) { .vrg-arr { width: 30px; height: 30px; font-size: 16px; } }

/* Slot containers */
.vrg-slot { flex-shrink: 0; perspective: 900px; }
.vrg-slot-center { z-index: 3; }
.vrg-slot-side {
    z-index: 2; opacity: .8; filter: brightness(.85);
    transition: opacity .3s, filter .3s;
}
.vrg-slot-side:hover { opacity: 1; filter: brightness(1); }

/* Card sizes */
.vrg-slot-center .vrg-flip-card { width: 240px; height: 340px; }
.vrg-slot-side   .vrg-flip-card { width: 180px; height: 265px; }
@media (max-width:700px) {
    .vrg-slot-center .vrg-flip-card { width: 160px; height: 230px; }
    .vrg-slot-side   .vrg-flip-card { width: 118px; height: 174px; }
    .vrg-stage { gap: 10px; }
}
@media (max-width:420px) {
    .vrg-slot-center .vrg-flip-card { width: 128px; height: 185px; }
    .vrg-slot-side   .vrg-flip-card { width: 94px;  height: 136px; }
    .vrg-stage { gap: 7px; padding: 0 4px; }
    .vrg-arr { display: none; }
}

/* Flip card */
.vrg-flip-card { border-radius: 12px; position: relative; cursor: pointer; }

@keyframes vrg-out {
    0%   { transform: rotateY(0deg);  opacity: 1; }
    100% { transform: rotateY(90deg); opacity: 0; }
}
@keyframes vrg-in {
    0%   { transform: rotateY(-90deg); opacity: 0; }
    100% { transform: rotateY(0deg);   opacity: 1; }
}
.vrg-flip-card.vrg-flipping-out { animation: vrg-out .35s ease-in  forwards; }
.vrg-flip-card.vrg-flipping-in  { animation: vrg-in  .42s ease-out forwards; }

/* Inner face */
.vrg-flip-inner {
    width: 100%; height: 100%; border-radius: 12px; overflow: hidden;
    border: 1.5px solid rgba(201,168,76,.38);
    box-shadow: 0 8px 40px rgba(0,0,0,.65), inset 0 0 40px rgba(0,0,0,.2);
    position: relative; background: #06091a;
}
.vrg-slot-center .vrg-flip-inner {
    border-color: rgba(201,168,76,.65);
    box-shadow: 0 12px 55px rgba(0,0,0,.75), 0 0 30px rgba(201,168,76,.13);
}
.vrg-flip-inner img {
    width: 100%; height: 100%; object-fit: cover; display: block;
    transition: transform .45s ease;
}
.vrg-flip-card:hover .vrg-flip-inner img { transform: scale(1.05); }

/* Shine overlay */
.vrg-flip-inner::after {
    content: ''; position: absolute; inset: 0; pointer-events: none; z-index: 1;
    background: linear-gradient(125deg, rgba(201,168,76,.17) 0%, transparent 55%, rgba(201,168,76,.07) 100%);
    border-radius: 12px; opacity: 0; transition: opacity .3s;
}
.vrg-flip-card:hover .vrg-flip-inner::after { opacity: 1; }

/* Corner accents */
.vrg-fc { position: absolute; width: 12px; height: 12px; border-color: rgba(201,168,76,.7); border-style: solid; pointer-events: none; z-index: 2; }
.vrg-fc.tl { top:6px;    left:6px;   border-width: 1.5px 0 0 1.5px; }
.vrg-fc.tr { top:6px;    right:6px;  border-width: 1.5px 1.5px 0 0; }
.vrg-fc.bl { bottom:6px; left:6px;   border-width: 0 0 1.5px 1.5px; }
.vrg-fc.br { bottom:6px; right:6px;  border-width: 0 1.5px 1.5px 0; }

/* Photo number */
.vrg-face-num {
    position: absolute; bottom: 7px; right: 9px; z-index: 3;
    font-family: 'Jost',sans-serif; font-size: 10px; color: rgba(201,168,76,.65);
    letter-spacing: .08em; text-shadow: 0 1px 5px rgba(0,0,0,.9);
}

/* Glow on flipping side cards */
.vrg-slot-side .vrg-flip-card.vrg-flipping-out,
.vrg-slot-side .vrg-flip-card.vrg-flipping-in {
    filter: drop-shadow(0 0 14px rgba(201,168,76,.45));
}

/* Dots */
.vrg-dots { display: flex; justify-content: center; gap: 6px; margin-top: 20px; flex-wrap: wrap; position: relative; z-index: 2; }
.vrg-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: rgba(201,168,76,.2); border: 1px solid rgba(201,168,76,.35);
    cursor: pointer; transition: background .25s, transform .2s; flex-shrink: 0;
}
.vrg-dot.active { background: #c9a84c; transform: scale(1.35); box-shadow: 0 0 8px rgba(201,168,76,.55); }

/* Progress bar */
.vrg-progress { width: 180px; max-width:55%; height: 2px; background: rgba(201,168,76,.12); border-radius: 2px; margin: 10px auto 0; overflow: hidden; position: relative; z-index: 2; }
.vrg-progress-bar { height: 100%; background: linear-gradient(90deg,#c9a84c,#f0d992); border-radius: 2px; width: 0%; transition: width linear; }

/* Strip */
.vrg-strip-wrap { margin-top: 32px; overflow: hidden; position: relative; }
.vrg-strip-wrap::before, .vrg-strip-wrap::after { content:''; position:absolute; top:0; bottom:0; width:80px; z-index:2; pointer-events:none; }
.vrg-strip-wrap::before { left:0;  background:linear-gradient(to right,#050818,transparent); }
.vrg-strip-wrap::after  { right:0; background:linear-gradient(to left, #050818,transparent); }
.vrg-strip { display:flex; gap:12px; width:max-content; padding:8px 0; animation:vrg-scroll-x 30s linear infinite; }
.vrg-strip:hover { animation-play-state:paused; }
@keyframes vrg-scroll-x { from{transform:translateX(0)} to{transform:translateX(-50%)} }
.vrg-thumb { width:160px; height:110px; border-radius:8px; overflow:hidden; flex-shrink:0; border:1.5px solid rgba(201,168,76,.2); cursor:pointer; position:relative; transition:border-color .25s,transform .25s,box-shadow .25s; }
.vrg-thumb:hover { border-color:rgba(201,168,76,.7); transform:scale(1.06) translateY(-3px); box-shadow:0 8px 28px rgba(201,168,76,.2); }
.vrg-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .35s; }
.vrg-thumb:hover img { transform:scale(1.08); }
.vrg-thumb.vrg-thumb-active { border-color:#c9a84c; box-shadow:0 0 18px rgba(201,168,76,.4); }
.vrg-thumb-num { position:absolute; bottom:5px; left:7px; font-size:10px; color:rgba(201,168,76,.65); font-family:'Jost',sans-serif; letter-spacing:.07em; text-shadow:0 1px 4px rgba(0,0,0,.9); }

/* Lightbox */
.vrg-lb { position:fixed; inset:0; z-index:99999; background:rgba(2,4,18,.95); backdrop-filter:blur(14px); display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:opacity .3s; }
.vrg-lb.open { opacity:1; pointer-events:auto; }
.vrg-lb-img-wrap { position:relative; transform:scale(.86); transition:transform .38s cubic-bezier(.34,1.56,.64,1); }
.vrg-lb.open .vrg-lb-img-wrap { transform:scale(1); }
.vrg-lb-img-wrap img { max-width:90vw; max-height:86vh; border-radius:10px; border:1px solid rgba(201,168,76,.4); box-shadow:0 0 100px rgba(201,168,76,.18); display:block; transition:opacity .18s,transform .2s; }
.vrg-lb-close { position:absolute; top:18px; right:22px; color:rgba(201,168,76,.8); font-size:30px; cursor:pointer; background:none; border:none; line-height:1; transition:color .2s,transform .25s; }
.vrg-lb-close:hover { color:#c9a84c; transform:rotate(90deg) scale(1.15); }
.vrg-lb-nav { position:absolute; top:50%; transform:translateY(-50%); background:rgba(201,168,76,.12); border:1px solid rgba(201,168,76,.3); color:#c9a84c; font-size:28px; width:50px; height:50px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s,transform .2s; }
.vrg-lb-nav:hover { background:rgba(201,168,76,.32); transform:translateY(-50%) scale(1.1); }
.vrg-lb-prev { left:14px; } .vrg-lb-next { right:14px; }
.vrg-lb-counter { position:absolute; bottom:14px; left:50%; transform:translateX(-50%); font-size:.8rem; color:rgba(201,168,76,.6); font-family:'Jost',sans-serif; letter-spacing:.1em; white-space:nowrap; }
</style>

{{-- Couple photo background --}}
<div class="vrg-bg">
    @if(!empty($bridePhotoUrl))
    <div class="vrg-bg-bride" style="background-image:url('{{ $bridePhotoUrl }}')"></div>
    @endif
    @if(!empty($groomPhotoUrl))
    <div class="vrg-bg-groom" style="background-image:url('{{ $groomPhotoUrl }}')"></div>
    @elseif(!empty($bridePhotoUrl))
    <div class="vrg-bg-groom" style="background-image:url('{{ $bridePhotoUrl }}')"></div>
    @endif
    <div class="vrg-bg-veil"></div>
</div>

<div class="vrg-scene">
    <!-- Floating geometry shapes -->
    <div class="vrg-geo" style="top:4%;left:2%;animation-duration:10s;animation-delay:0s;">
        <svg width="58" height="58" viewBox="0 0 58 58"><polygon points="29,2 56,15 56,43 29,56 2,43 2,15" fill="none" stroke="#c9a84c" stroke-width="1.4"/></svg>
    </div>
    <div class="vrg-geo" style="top:8%;right:3%;animation-duration:14s;animation-delay:-5s;">
        <svg width="42" height="42" viewBox="0 0 42 42"><polygon points="21,2 40,21 21,40 2,21" fill="none" stroke="#c9a84c" stroke-width="1.2"/></svg>
    </div>
    <div class="vrg-geo" style="bottom:22%;left:5%;animation-duration:12s;animation-delay:-3s;">
        <svg width="32" height="32" viewBox="0 0 32 32"><rect x="4" y="4" width="24" height="24" fill="none" stroke="#c9a84c" stroke-width="1" transform="rotate(45 16 16)"/></svg>
    </div>
    <div class="vrg-geo" style="bottom:12%;right:6%;animation-duration:17s;animation-delay:-8s;">
        <svg width="50" height="48" viewBox="0 0 50 48"><polygon points="25,2 48,14 48,34 25,46 2,34 2,14" fill="none" stroke="rgba(201,168,76,.55)" stroke-width="1.1"/></svg>
    </div>
    <div class="vrg-geo" style="top:44%;left:0%;animation-duration:19s;animation-delay:-11s;">
        <svg width="26" height="30" viewBox="0 0 26 30"><polygon points="13,1 25,8 25,22 13,29 1,22 1,8" fill="none" stroke="#c9a84c" stroke-width="1"/></svg>
    </div>

    <!-- 3-card flip row -->
    <div class="vrg-stage">
        <button class="vrg-arr" id="vrg-prev" aria-label="Sebelumnya">&#x2039;</button>

        <!-- Slot 0 (left) -->
        <div class="vrg-slot vrg-slot-side" id="vrg-slot-0">
            <div class="vrg-flip-card" id="vrg-card-0">
                <div class="vrg-flip-inner">
                    <img src="{{ $gallPhotos[$s0] }}" alt="Foto" id="vrg-img-0" loading="eager">
                    <div class="vrg-fc tl"></div><div class="vrg-fc tr"></div>
                    <div class="vrg-fc bl"></div><div class="vrg-fc br"></div>
                    <div class="vrg-face-num" id="vrg-num-0">{{ str_pad($s0+1,2,'0',STR_PAD_LEFT) }}/{{ str_pad($gCount,2,'0',STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <!-- Slot 1 (center) -->
        <div class="vrg-slot vrg-slot-center" id="vrg-slot-1">
            <div class="vrg-flip-card" id="vrg-card-1">
                <div class="vrg-flip-inner">
                    <img src="{{ $gallPhotos[$s1] }}" alt="Foto" id="vrg-img-1" loading="eager">
                    <div class="vrg-fc tl"></div><div class="vrg-fc tr"></div>
                    <div class="vrg-fc bl"></div><div class="vrg-fc br"></div>
                    <div class="vrg-face-num" id="vrg-num-1">{{ str_pad($s1+1,2,'0',STR_PAD_LEFT) }}/{{ str_pad($gCount,2,'0',STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <!-- Slot 2 (right) -->
        <div class="vrg-slot vrg-slot-side" id="vrg-slot-2">
            <div class="vrg-flip-card" id="vrg-card-2">
                <div class="vrg-flip-inner">
                    <img src="{{ $gallPhotos[$s2] }}" alt="Foto" id="vrg-img-2" loading="eager">
                    <div class="vrg-fc tl"></div><div class="vrg-fc tr"></div>
                    <div class="vrg-fc bl"></div><div class="vrg-fc br"></div>
                    <div class="vrg-face-num" id="vrg-num-2">{{ str_pad($s2+1,2,'0',STR_PAD_LEFT) }}/{{ str_pad($gCount,2,'0',STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <button class="vrg-arr" id="vrg-next" aria-label="Berikutnya">&#x203a;</button>
    </div>

    <!-- Dot indicators -->
    <div class="vrg-dots" id="vrg-dots">
        @foreach($gallPhotos as $idx => $photoUrl)
        <div class="vrg-dot{{ $idx === $s1 ? ' active' : '' }}" data-idx="{{ $idx }}"></div>
        @endforeach
    </div>

    <!-- Progress bar -->
    <div class="vrg-progress"><div class="vrg-progress-bar" id="vrg-bar"></div></div>

    <!-- Thumbnail strip -->
    @if($gCount > 1)
    <div class="vrg-strip-wrap" style="margin-top:32px;">
        <div class="vrg-strip" id="vrg-strip">
            @foreach($gallPhotos as $idx => $photoUrl)
            <div class="vrg-thumb{{ $idx === $s1 ? ' vrg-thumb-active' : '' }}" data-idx="{{ $idx }}" onclick="vrgOpen({{ $idx }})">
                <img src="{{ $photoUrl }}" alt="Foto {{ $idx+1 }}" loading="lazy">
                <div class="vrg-thumb-num">{{ str_pad($idx+1,2,'0',STR_PAD_LEFT) }}</div>
            </div>
            @endforeach
            @foreach($gallPhotos as $idx => $photoUrl)
            <div class="vrg-thumb" data-idx="{{ $idx }}" onclick="vrgOpen({{ $idx }})">
                <img src="{{ $photoUrl }}" alt="Foto {{ $idx+1 }}" loading="lazy">
                <div class="vrg-thumb-num">{{ str_pad($idx+1,2,'0',STR_PAD_LEFT) }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Lightbox -->
<div class="vrg-lb" id="vrg-lb" onclick="if(event.target===this)vrgClose()">
    <button class="vrg-lb-close" onclick="vrgClose()">&#x2715;</button>
    <button class="vrg-lb-nav vrg-lb-prev" onclick="vrgLbNav(-1)">&#x2039;</button>
    <div class="vrg-lb-img-wrap">
        <img src="{{ $gallPhotos[$s1] }}" id="vrg-lb-img" alt="">
    </div>
    <button class="vrg-lb-nav vrg-lb-next" onclick="vrgLbNav(1)">&#x203a;</button>
    <div class="vrg-lb-counter" id="vrg-lb-counter">{{ $s1+1 }} / {{ $gCount }}</div>
</div>

<script>
(function () {
    var photos  = @json($gallPhotos);
    var n       = photos.length;
    var SLOTS   = 3;

    /*
     * Each slot i starts at photo i (mod n).
     * When a slot flips, it advances by SLOTS steps to avoid duplicates.
     * e.g. slot0: 0→3→6…  slot1: 1→4→7…  slot2: 2→5→8…
     */
    var slotIdx   = [0 % n, 1 % n, 2 % n];
    var flipping  = [false, false, false];

    var cards = [0,1,2].map(function(i){ return document.getElementById('vrg-card-' + i); });
    var imgs  = [0,1,2].map(function(i){ return document.getElementById('vrg-img-'  + i); });
    var nums  = [0,1,2].map(function(i){ return document.getElementById('vrg-num-'  + i); });
    var dots  = Array.from(document.querySelectorAll('#vrg-dots .vrg-dot'));
    var thumbs = Array.from(document.querySelectorAll('#vrg-strip .vrg-thumb')).slice(0, n);
    var bar   = document.getElementById('vrg-bar');
    var lbCur = slotIdx[1];

    /* Number pad */
    function pad(v){ return (v < 10 ? '0' : '') + v; }

    /* Update dot + thumb active states to track center slot */
    function ui(centerIdx) {
        dots.forEach(function(d,i)  { d.classList.toggle('active', i === centerIdx); });
        thumbs.forEach(function(t,i){ t.classList.toggle('vrg-thumb-active', i === centerIdx); });
    }

    /* Flip a single slot to toIdx */
    function flipSlot(sid, toIdx, done) {
        if (flipping[sid]) { if (done) done(); return; }
        flipping[sid] = true;
        var c = cards[sid];
        c.classList.add('vrg-flipping-out');
        c.classList.remove('vrg-flipping-in');
        setTimeout(function() {
            slotIdx[sid] = toIdx;
            imgs[sid].src = photos[toIdx];
            nums[sid].textContent = pad(toIdx + 1) + '/' + pad(n);
            c.classList.remove('vrg-flipping-out');
            c.classList.add('vrg-flipping-in');
            setTimeout(function() {
                c.classList.remove('vrg-flipping-in');
                flipping[sid] = false;
                if (done) done();
            }, 430);
        }, 350);
    }

    /* Advance all 3 slots by dir (+1 or -1) */
    function advanceAll(dir) {
        var newIdx = slotIdx.map(function(cur) {
            return ((cur + dir * SLOTS) % n + n) % n;
        });
        for (var i = 0; i < SLOTS; i++) {
            (function(s, idx){ flipSlot(s, idx, null); })(i, newIdx[i]);
        }
        lbCur = newIdx[1];
        ui(newIdx[1]);
    }

    /* Manual nav */
    document.getElementById('vrg-prev').addEventListener('click', function(){
        stopTimers(); advanceAll(-1); startTimers();
    });
    document.getElementById('vrg-next').addEventListener('click', function(){
        stopTimers(); advanceAll(1); startTimers();
    });

    /* Click card → lightbox */
    cards.forEach(function(card, si) {
        card.addEventListener('click', function(){
            if (!flipping[si]) vrgOpen(slotIdx[si]);
        });
    });

    /*
     * Auto-advance timers — staggered wave:
     * Slot 0 flips first, then slot 1 after STAGGER ms, then slot 2 after STAGGER*2 ms.
     * Each slot has its own repeating interval = INTERVAL ms.
     */
    var INTERVAL = 3400;
    var STAGGER  = 950;
    var timers   = [null, null, null];
    var paused   = false;

    function scheduleSlot(sid, initialDelay) {
        timers[sid] = setTimeout(function tick() {
            if (paused) return;
            var ni = ((slotIdx[sid] + SLOTS) % n + n) % n;
            flipSlot(sid, ni, null);
            if (sid === 1) { ui(ni); lbCur = ni; }
            timers[sid] = setTimeout(tick, INTERVAL);
        }, initialDelay);
    }

    function startTimers() {
        paused = false;
        scheduleSlot(0, 0);
        scheduleSlot(1, STAGGER);
        scheduleSlot(2, STAGGER * 2);
        /* Progress bar tracks center slot cycle */
        bar.style.transition = 'none'; bar.style.width = '0%';
        bar.offsetWidth;
        bar.style.transition = 'width ' + (INTERVAL + STAGGER) + 'ms linear';
        bar.style.width = '100%';
    }
    function stopTimers() {
        paused = true;
        timers.forEach(function(t){ clearTimeout(t); });
        timers = [null, null, null];
        bar.style.transition = 'none'; bar.style.width = '0%';
    }

    /* Pause/resume on hover */
    var stage = document.querySelector('.vrg-stage');
    stage.addEventListener('mouseenter', stopTimers);
    stage.addEventListener('mouseleave', startTimers);

    /* Swipe on mobile */
    var tSX = 0;
    stage.addEventListener('touchstart', function(e){ tSX = e.touches[0].clientX; }, { passive: true });
    stage.addEventListener('touchend',   function(e){
        var dx = e.changedTouches[0].clientX - tSX;
        if (Math.abs(dx) > 40) { stopTimers(); advanceAll(dx < 0 ? 1 : -1); startTimers(); }
    }, { passive: true });

    /* Lightbox */
    window.vrgOpen = function(idx) {
        lbCur = idx;
        document.getElementById('vrg-lb-img').src = photos[lbCur];
        document.getElementById('vrg-lb-counter').textContent = (lbCur + 1) + ' / ' + n;
        document.getElementById('vrg-lb').classList.add('open');
        document.body.style.overflow = 'hidden';
        stopTimers();
    };
    window.vrgClose = function() {
        document.getElementById('vrg-lb').classList.remove('open');
        document.body.style.overflow = '';
        startTimers();
    };
    window.vrgLbNav = function(dir) {
        lbCur = (lbCur + dir + n) % n;
        var img = document.getElementById('vrg-lb-img');
        img.style.opacity = '0';
        img.style.transform = 'scale(.9) translateX(' + (dir * 28) + 'px)';
        img.style.transition = 'opacity .15s, transform .18s';
        setTimeout(function(){
            img.src = photos[lbCur];
            img.style.opacity = '1'; img.style.transform = '';
            document.getElementById('vrg-lb-counter').textContent = (lbCur + 1) + ' / ' + n;
        }, 190);
    };
    document.addEventListener('keydown', function(e){
        var lb = document.getElementById('vrg-lb');
        if (!lb.classList.contains('open')) return;
        if (e.key === 'ArrowRight') vrgLbNav(1);
        if (e.key === 'ArrowLeft')  vrgLbNav(-1);
        if (e.key === 'Escape')     vrgClose();
    });

    /* Init */
    ui(slotIdx[1]);
    startTimers();
}());
</script>
