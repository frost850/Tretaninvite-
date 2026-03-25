{{--
  Gallery Innovations — Cinematic Edition
  Params: $galleryPhotos, $galleryMode ('slider'|'polaroid'), $galleryBgColor, $galleryAccent
--}}
@php
    $gPhotos = $galleryPhotos  ?? [];
    $gMode   = $galleryMode    ?? 'slider';
    $gBg     = $galleryBgColor ?? '#111';
    $gAccent = $galleryAccent  ?? 'rgba(139,20,40,0.85)';
    $nPh     = count($gPhotos);
    $gId     = 'g'.substr(md5(implode('',$gPhotos).$gMode), 0, 7);
@endphp
@if($nPh > 0)
<style>
/* ══════════════════════════════════════════════════════════
   GALLERY INNOVATIONS — CINEMATIC EDITION
   Scoped to #gin-{{ $gId }}
══════════════════════════════════════════════════════════ */
#gin-{{ $gId }} { width:100%; position:relative; user-select:none; }

/* ────────────────────────────────────────────────
   SLIDER MODE — cinematic wipe + parallax + tilt
──────────────────────────────────────────────── */
#gin-{{ $gId }} .ginc-stage {
    position:relative; width:100%; height:480px;
    overflow:hidden; background:#0a0a0a; cursor:none;
    border-radius:2px;
}
@media(max-width:680px){ #gin-{{ $gId }} .ginc-stage{ height:280px; cursor:default; } }

/* ── Slides ── */
#gin-{{ $gId }} .ginc-slide {
    position:absolute; inset:0; z-index:1;
    clip-path: inset(0 100% 0 0);
    transition: clip-path 0s;
    will-change: clip-path;
}
#gin-{{ $gId }} .ginc-slide.ginc-active {
    z-index:3;
    animation: ginc-wipe-{{ $gId }} 0.72s cubic-bezier(.77,0,.18,1) forwards;
}
@keyframes ginc-wipe-{{ $gId }} {
    from { clip-path: inset(0 100% 0 0); }
    to   { clip-path: inset(0 0%   0 0); }
}
#gin-{{ $gId }} .ginc-slide.ginc-shown {
    z-index:2; clip-path: inset(0 0% 0 0);
}
#gin-{{ $gId }} .ginc-slide.ginc-leaving {
    z-index:2;
    animation: ginc-leave-{{ $gId }} 0.72s cubic-bezier(.77,0,.18,1) forwards;
}
@keyframes ginc-leave-{{ $gId }} {
    from { clip-path: inset(0 0% 0 0); transform:scale(1); opacity:1; }
    to   { clip-path: inset(0 0% 0 0); transform:scale(1.04); opacity:0; }
}

/* ── Image inside slide — Ken Burns ── */
#gin-{{ $gId }} .ginc-slide img {
    width:100%; height:100%; object-fit:cover; display:block;
    transform-origin:50% 50%;
    transform: scale(1.15) translate(0,0);
    transition: transform 0.2s ease-out; /* mouse parallax */
    pointer-events:none;
}
#gin-{{ $gId }} .ginc-slide.ginc-active img {
    animation: ginc-kb-{{ $gId }} 5s cubic-bezier(.25,.46,.45,.94) forwards;
}
@keyframes ginc-kb-{{ $gId }} {
    0%   { transform: scale(1.18) translate(2%,1%); }
    100% { transform: scale(1.04) translate(-1%,-0.5%); }
}

/* ── Dark gradient overlay ── */
#gin-{{ $gId }} .ginc-slide::after {
    content:''; position:absolute; inset:0; z-index:4; pointer-events:none;
    background:
        linear-gradient(to top, rgba(0,0,0,.72) 0%, rgba(0,0,0,.12) 40%, transparent 65%),
        linear-gradient(to right, rgba(0,0,0,.3) 0%, transparent 30%);
}

/* ── Custom cursor ── */
#gin-{{ $gId }} .ginc-cursor {
    position:absolute; z-index:50; pointer-events:none;
    width:46px; height:46px; border-radius:50%;
    border:2px solid rgba(255,255,255,.75);
    transform:translate(-50%,-50%) scale(0);
    transition:transform .2s, width .2s, height .2s, background .2s;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:.65rem; letter-spacing:.06em;
    backdrop-filter:blur(3px);
    background: rgba(255,255,255,.08);
}
#gin-{{ $gId }} .ginc-stage:hover .ginc-cursor { transform:translate(-50%,-50%) scale(1); }

/* ── Slide-in text overlay ── */
#gin-{{ $gId }} .ginc-slide-meta {
    position:absolute; bottom:0; left:0; right:0; z-index:10;
    padding:24px 28px; display:flex; align-items:flex-end; justify-content:space-between;
}
#gin-{{ $gId }} .ginc-num {
    font-size:3.5rem; font-weight:700; letter-spacing:-.04em;
    color:rgba(255,255,255,.12); line-height:1;
    font-family:'Georgia',serif;
    transition:color .5s;
}
#gin-{{ $gId }} .ginc-slide.ginc-active .ginc-num {
    animation: ginc-numfade-{{ $gId }} .6s .3s both;
}
@keyframes ginc-numfade-{{ $gId }} {
    from{ opacity:0; transform:translateY(12px); }
    to  { opacity:1; transform:translateY(0); }
}

/* ── Progress bar ── */
#gin-{{ $gId }} .ginc-prog-wrap {
    position:absolute; top:0; left:0; right:0; height:3px; z-index:20;
    background:rgba(255,255,255,.12);
}
#gin-{{ $gId }} .ginc-prog {
    height:100%; width:0%;
    background: linear-gradient(to right, {{ $gAccent }}, rgba(255,255,255,.9));
    transition:none;
}
#gin-{{ $gId }} .ginc-prog.go {
    transition: width var(--ginc-dur,3.5s) linear; width:100%;
}

/* ── Arrows ── */
#gin-{{ $gId }} .ginc-arr {
    position:absolute; top:50%; transform:translateY(-50%); z-index:20;
    background:transparent; border:1.5px solid rgba(255,255,255,.35);
    color:#fff; width:48px; height:48px;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; border-radius:50%; font-size:1.5rem;
    transition:background .3s, border-color .3s, transform .3s;
    backdrop-filter:blur(6px); padding:0;
}
#gin-{{ $gId }} .ginc-arr:hover {
    background:rgba(255,255,255,.18); border-color:rgba(255,255,255,.8);
}
#gin-{{ $gId }} .ginc-arr-l { left:20px; }
#gin-{{ $gId }} .ginc-arr-r { right:20px; }
#gin-{{ $gId }} .ginc-arr-l:hover { transform:translateY(-50%) translateX(-3px); }
#gin-{{ $gId }} .ginc-arr-r:hover { transform:translateY(-50%) translateX(3px); }

/* ── Dots ── */
#gin-{{ $gId }} .ginc-dots {
    position:absolute; bottom:18px; left:50%; transform:translateX(-50%);
    z-index:20; display:flex; gap:8px; align-items:center;
}
#gin-{{ $gId }} .ginc-dot {
    width:6px; height:6px; border-radius:50%;
    background:rgba(255,255,255,.3); cursor:pointer;
    transition:background .35s, width .4s cubic-bezier(.4,0,.2,1), border-radius .4s;
    border:none; padding:0;
}
#gin-{{ $gId }} .ginc-dot.on {
    background:rgba(255,255,255,.92); width:28px; border-radius:3px;
}

/* ── Thumbnail strip ── */
#gin-{{ $gId }} .ginc-thumbrow {
    display:flex; gap:4px; margin-top:4px;
    overflow-x:auto; scrollbar-width:none;
}
#gin-{{ $gId }} .ginc-thumbrow::-webkit-scrollbar { display:none; }
#gin-{{ $gId }} .ginc-th {
    flex-shrink:0;
    width:calc((100% - 20px) / 6);
    min-width:60px; height:68px;
    overflow:hidden; cursor:pointer; position:relative;
    transition:opacity .35s, transform .35s;
    opacity:.48; transform:scale(.97);
}
#gin-{{ $gId }} .ginc-th.on {
    opacity:1; transform:scale(1);
    outline:2px solid rgba(255,255,255,.7); outline-offset:-2px;
}
#gin-{{ $gId }} .ginc-th:hover:not(.on) { opacity:.75; transform:scale(.99); }
#gin-{{ $gId }} .ginc-th img {
    width:100%; height:100%; object-fit:cover; display:block;
    transition:transform .5s;
}
#gin-{{ $gId }} .ginc-th:hover img { transform:scale(1.12); }
#gin-{{ $gId }} .ginc-th.on img {
    animation: ginc-thkb .8s ease-out both;
}
@keyframes ginc-thkb {
    from{ transform:scale(1.1); }
    to  { transform:scale(1.0); }
}
@media(max-width:480px){
    #gin-{{ $gId }} .ginc-th{ height:52px; min-width:46px; }
}

/* ────────────────────────────────────────────────
   POLAROID MODE — floating + 3D tilt + scatter
──────────────────────────────────────────────── */
#gin-{{ $gId }} .ginp-stage {
    display:flex; flex-direction:column; align-items:center;
    gap:20px; padding:10px 0 20px;
}

/* Featured polaroid */
#gin-{{ $gId }} .ginp-feat {
    position:relative; width:240px;
    background:#f9f2ed; padding:10px 10px 48px;
    box-shadow:
        0 0 0 1px rgba(0,0,0,.06),
        0 6px 20px rgba(0,0,0,.35),
        0 24px 60px rgba(0,0,0,.45),
        0 2px 4px rgba(255,255,255,.5) inset;
    transform-style:preserve-3d;
    transform:rotate(-1.5deg);
    transition:transform .5s cubic-bezier(.4,0,.2,1), box-shadow .5s;
    cursor:pointer;
    animation: ginp-float-{{ $gId }} 4s ease-in-out infinite;
}
@keyframes ginp-float-{{ $gId }} {
    0%,100%{ transform:rotate(-1.5deg) translateY(0); }
    50%     { transform:rotate(-1.5deg) translateY(-8px); }
}
#gin-{{ $gId }} .ginp-feat.ginp-tilted {
    animation:none; /* mouse takes over */
}
#gin-{{ $gId }} .ginp-feat::before {
    content:''; position:absolute; top:-14px; left:50%;
    transform:translateX(-50%); width:54px; height:22px;
    background:rgba(255,248,200,.72);
    border:1px solid rgba(200,180,80,.35); z-index:5;
    box-shadow:0 1px 3px rgba(0,0,0,.18);
}
#gin-{{ $gId }} .ginp-feat img {
    width:100%; height:260px; object-fit:cover; display:block;
    transition:opacity .45s ease;
    filter:saturate(.95) contrast(1.05);
}
#gin-{{ $gId }} .ginp-feat-lbl {
    position:absolute; bottom:14px; left:0; right:0;
    text-align:center;
    font-family:'Caveat','Dancing Script',cursive,sans-serif;
    font-size:1rem; color:#7a6060; pointer-events:none;
    transition:opacity .3s;
}
/* Shine overlay for 3D effect */
#gin-{{ $gId }} .ginp-feat-shine {
    position:absolute; inset:0; pointer-events:none; z-index:6;
    background:radial-gradient(circle at 30% 30%, rgba(255,255,255,.18) 0%, transparent 65%);
    transition:background .15s;
}

/* Polaroid wall */
#gin-{{ $gId }} .ginp-wall {
    display:flex; flex-wrap:wrap; justify-content:center;
    gap:12px; padding:8px 10px; perspective:1200px;
}
#gin-{{ $gId }} .ginp-pol {
    position:relative; background:#f9f2ed;
    padding:6px 6px 30px;
    box-shadow:0 4px 14px rgba(0,0,0,.4),0 10px 30px rgba(0,0,0,.3),
               0 1px 0 rgba(255,255,255,.6) inset;
    transform:rotate(var(--pr)) translateZ(0);
    transform-origin:center bottom; transform-style:preserve-3d;
    transition:transform .45s cubic-bezier(.4,0,.2,1),box-shadow .45s,z-index 0s;
    cursor:pointer; width:118px; z-index:1;
    animation: ginp-sway-{{ $gId }} var(--sw-dur,5s) var(--sw-del,0s) ease-in-out infinite;
}
@keyframes ginp-sway-{{ $gId }} {
    0%,100%{ transform:rotate(var(--pr)) translateY(0) translateZ(0); }
    50%     { transform:rotate(var(--pr)) translateY(-5px) translateZ(0); }
}
#gin-{{ $gId }} .ginp-pol:nth-child(1){ --pr:-6deg; --sw-dur:4.2s; --sw-del:0s; }
#gin-{{ $gId }} .ginp-pol:nth-child(2){ --pr: 4deg; --sw-dur:5.1s; --sw-del:.4s; }
#gin-{{ $gId }} .ginp-pol:nth-child(3){ --pr:-2deg; --sw-dur:4.7s; --sw-del:.8s; }
#gin-{{ $gId }} .ginp-pol:nth-child(4){ --pr: 7deg; --sw-dur:5.4s; --sw-del:.2s; }
#gin-{{ $gId }} .ginp-pol:nth-child(5){ --pr:-4deg; --sw-dur:4.9s; --sw-del:.6s; }
#gin-{{ $gId }} .ginp-pol:nth-child(6){ --pr: 3deg; --sw-dur:5.2s; --sw-del:1s; }
#gin-{{ $gId }} .ginp-pol:hover {
    animation:none;
    transform:rotate(0) scale(1.18) translateY(-22px) translateZ(50px) !important;
    box-shadow:0 30px 70px rgba(0,0,0,.7),0 6px 18px rgba(0,0,0,.3); z-index:30;
}
#gin-{{ $gId }} .ginp-pol.ginp-pol-active {
    animation:none;
    transform:rotate(var(--pr)) scale(.9) translateY(4px) !important;
    opacity:.38;
}
#gin-{{ $gId }} .ginp-pol img {
    width:100%; height:92px; object-fit:cover; display:block;
    filter:saturate(.88) contrast(1.06);
    transition:filter .4s, transform .4s;
}
#gin-{{ $gId }} .ginp-pol:hover img { filter:saturate(1.1) contrast(1); transform:scale(1.05); }
#gin-{{ $gId }} .ginp-pol-lbl {
    position:absolute; bottom:7px; left:0; right:0; text-align:center;
    font-family:'Caveat','Dancing Script',cursive,sans-serif;
    font-size:.6rem; color:#8a7070; letter-spacing:.04em;
}
#gin-{{ $gId }} .ginp-pol::before {
    content:''; position:absolute; top:-9px; left:50%;
    transform:translateX(-50%); width:28px; height:15px;
    background:rgba(255,248,200,.6); border:1px solid rgba(200,180,80,.3); z-index:2;
}
@media(max-width:480px){
    #gin-{{ $gId }} .ginp-pol{ width:100px; padding:5px 5px 26px; }
    #gin-{{ $gId }} .ginp-pol img{ height:78px; }
    #gin-{{ $gId }} .ginp-feat{ width:190px; }
    #gin-{{ $gId }} .ginp-feat img{ height:220px; }
}

/* ════════════════════════════════════
   LIGHTBOX
════════════════════════════════════ */
.ginc-lb {
    position:fixed; inset:0; z-index:99999;
    background:rgba(4,2,3,.96); backdrop-filter:blur(20px) saturate(1.5);
    display:flex; align-items:center; justify-content:center;
    cursor:zoom-out; padding:20px; box-sizing:border-box;
    animation:ginc-lbin .22s ease;
}
@keyframes ginc-lbin{ from{opacity:0;backdrop-filter:blur(0)}to{opacity:1;backdrop-filter:blur(20px)} }
.ginc-lb-wrap {
    position:relative; max-width:min(90vw,1080px); max-height:82vh;
    display:flex; align-items:center; justify-content:center;
}
.ginc-lb-wrap img {
    max-width:100%; max-height:82vh; object-fit:contain; cursor:default;
    box-shadow:0 8px 100px rgba(0,0,0,.9); display:block;
    animation:ginc-lbzoom .32s cubic-bezier(.4,0,.2,1);
}
@keyframes ginc-lbzoom{
    from{transform:scale(.78) translateY(24px);opacity:0}
    to  {transform:scale(1)   translateY(0);  opacity:1}
}
.ginc-lb button{ border:none; background:none; cursor:pointer; }
.ginc-lb-close {
    position:fixed; top:18px; right:22px; font-size:2.2rem;
    color:rgba(255,255,255,.65); z-index:100001;
    transition:color .2s, transform .2s; line-height:1;
    padding:4px 8px;
}
.ginc-lb-close:hover{ color:#fff; transform:rotate(90deg) scale(1.15); }
.ginc-lb-nav {
    position:fixed; top:50%; transform:translateY(-50%);
    font-size:2.6rem; color:rgba(255,255,255,.55);
    padding:14px 20px; z-index:100001;
    transition:color .2s, background .2s; border-radius:4px;
}
.ginc-lb-nav:hover{ color:#fff; background:rgba(255,255,255,.08); }
.ginc-lb-prev{ left:6px; } .ginc-lb-next{ right:6px; }
.ginc-lb-thumbs {
    position:fixed; bottom:48px; left:50%; transform:translateX(-50%);
    display:flex; gap:6px; z-index:100001; max-width:90vw; overflow-x:auto;
}
.ginc-lb-th {
    width:50px; height:38px; flex-shrink:0; object-fit:cover;
    opacity:.38; cursor:pointer; transition:opacity .25s,transform .25s;
    border:2px solid transparent;
    transform:scale(.9);
}
.ginc-lb-th.active{ opacity:1; border-color:rgba(255,255,255,.8); transform:scale(1); }
.ginc-lb-counter {
    position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
    color:rgba(255,255,255,.4); font-size:.72rem; letter-spacing:.3em; z-index:100001;
}
</style>

<div id="gin-{{ $gId }}">

@if($gMode === 'polaroid')
{{-- ══ POLAROID MODE ══ --}}
<div class="ginp-stage">
    <div class="ginp-feat" id="{{ $gId }}-feat">
        <div class="ginp-feat-shine" id="{{ $gId }}-shine"></div>
        <img src="{{ $gPhotos[0] }}" alt="Foto Pilihan" id="{{ $gId }}-fimg"
             loading="eager">
        <span class="ginp-feat-lbl" id="{{ $gId }}-flbl">♥ 2024 ♥</span>
    </div>
    <div class="ginp-wall" id="{{ $gId }}-wall">
        @foreach(array_slice($gPhotos,0,6) as $i => $url)
        <div class="ginp-pol{{ $i===0?' ginp-pol-active':'' }}"
             data-url="{{ $url }}" data-i="{{ $i }}"
             style="--pr:{{ [-6,4,-2,7,-4,3][$i%6] }}deg;">
            <img src="{{ $url }}" alt="Foto {{ $i+1 }}" loading="{{ $i<2?'eager':'lazy' }}">
            <span class="ginp-pol-lbl">{{ 2020+$i }} ♥</span>
        </div>
        @endforeach
    </div>
</div>

@else
{{-- ══ SLIDER MODE ══ --}}
<div class="ginc-stage" id="{{ $gId }}-stage" style="--ginc-dur:3.5s;">
    <div class="ginc-cursor" id="{{ $gId }}-cur">OPEN</div>

    @foreach($gPhotos as $i => $url)
    <div class="ginc-slide{{ $i===0?' ginc-shown':'' }}" id="{{ $gId }}-sl{{ $i }}">
        <img src="{{ $url }}" alt="Foto {{ $i+1 }}"
             loading="{{ $i<2?'eager':'lazy' }}"
             id="{{ $gId }}-img{{ $i }}">
        <div class="ginc-slide-meta">
            <span class="ginc-num">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
        </div>
    </div>
    @endforeach

    <div class="ginc-prog-wrap">
        <div class="ginc-prog" id="{{ $gId }}-prog"></div>
    </div>

    <button class="ginc-arr ginc-arr-l" id="{{ $gId }}-prev">&#8249;</button>
    <button class="ginc-arr ginc-arr-r" id="{{ $gId }}-next">&#8250;</button>

    <div class="ginc-dots" id="{{ $gId }}-dots">
        @for($i=0;$i<$nPh;$i++)
        <button class="ginc-dot{{ $i===0?' on':'' }}" data-gi="{{ $i }}"></button>
        @endfor
    </div>
</div>
<div class="ginc-thumbrow" id="{{ $gId }}-thumbs">
    @foreach($gPhotos as $i => $url)
    <div class="ginc-th{{ $i===0?' on':'' }}" data-gi="{{ $i }}">
        <img src="{{ $url }}" alt="Foto {{ $i+1 }}" loading="lazy">
    </div>
    @endforeach
</div>
@endif

</div>{{-- /#gin --}}

<script>
(function(){
'use strict';
var ID     = '{{ $gId }}';
var photos = @json($gPhotos);
var mode   = '{{ $gMode }}';
var DELAY  = 3500;
var lbEl   = null, lbIdx = 0;

/* ═══════════ LIGHTBOX ═══════════ */
function lbOpen(idx){
    if(idx<0) idx=photos.length-1;
    if(idx>=photos.length) idx=0;
    lbIdx=idx;
    if(lbEl){ lbEl.remove(); lbEl=null; }
    lbEl=document.createElement('div');
    lbEl.className='ginc-lb';
    var ths=photos.map(function(u,i){
        return '<img class="ginc-lb-th'+(i===idx?' active':'')+'" src="'+u+'" data-i="'+i+'" alt="">';
    }).join('');
    lbEl.innerHTML=
        '<button class="ginc-lb-close">&times;</button>'+
        '<button class="ginc-lb-nav ginc-lb-prev">&#8249;</button>'+
        '<div class="ginc-lb-wrap"><img src="'+photos[idx]+'" alt="Foto '+(idx+1)+'"></div>'+
        '<button class="ginc-lb-nav ginc-lb-next">&#8250;</button>'+
        '<div class="ginc-lb-thumbs">'+ths+'</div>'+
        '<div class="ginc-lb-counter">'+(idx+1)+' / '+photos.length+'</div>';
    document.body.appendChild(lbEl);
    lbEl.addEventListener('click',function(e){ if(e.target===lbEl) lbClose(); });
    lbEl.querySelector('.ginc-lb-close').addEventListener('click',lbClose);
    lbEl.querySelector('.ginc-lb-prev').addEventListener('click',function(e){ e.stopPropagation(); lbOpen(lbIdx-1); });
    lbEl.querySelector('.ginc-lb-next').addEventListener('click',function(e){ e.stopPropagation(); lbOpen(lbIdx+1); });
    lbEl.querySelectorAll('.ginc-lb-th').forEach(function(t){
        t.addEventListener('click',function(e){ e.stopPropagation(); lbOpen(parseInt(t.dataset.i)); });
    });
    document.addEventListener('keydown',lbKey);
    var tx=0;
    lbEl.addEventListener('touchstart',function(e){ tx=e.touches[0].clientX; },{passive:true});
    lbEl.addEventListener('touchend',function(e){
        var dx=e.changedTouches[0].clientX-tx;
        if(Math.abs(dx)>50){ dx<0?lbOpen(lbIdx+1):lbOpen(lbIdx-1); }
    },{passive:true});
}
function lbClose(){
    if(!lbEl) return;
    lbEl.style.animation='ginc-lbin .18s ease reverse';
    setTimeout(function(){ if(lbEl){ lbEl.remove(); lbEl=null; } },200);
    document.removeEventListener('keydown',lbKey);
}
function lbKey(e){
    if(e.key==='Escape')     lbClose();
    if(e.key==='ArrowLeft')  lbOpen(lbIdx-1);
    if(e.key==='ArrowRight') lbOpen(lbIdx+1);
}

/* ═══════════ SLIDER ═══════════ */
if(mode!=='polaroid'){
    var cur=0, total=photos.length, timer=null, paused=false, busy=false;
    var stageEl  = document.getElementById(ID+'-stage');
    var progEl   = document.getElementById(ID+'-prog');
    var dotsEl   = document.getElementById(ID+'-dots');
    var thumbsEl = document.getElementById(ID+'-thumbs');
    var cursorEl = document.getElementById(ID+'-cur');

    function sl(i)  { return document.getElementById(ID+'-sl'+i); }
    function img(i) { return document.getElementById(ID+'-img'+i); }
    function dot(i) { return dotsEl&&dotsEl.children[i]; }
    function th(i)  { return thumbsEl&&thumbsEl.children[i]; }

    function goTo(idx, dir){
        if(busy||idx===cur) return;
        if(idx<0) idx=total-1;
        if(idx>=total) idx=0;
        busy=true;

        /* outgoing */
        var oldSl=sl(cur), oldDot=dot(cur), oldTh=th(cur);
        if(oldSl){ oldSl.classList.remove('ginc-shown'); oldSl.classList.add('ginc-leaving');
            setTimeout(function(){ oldSl.classList.remove('ginc-leaving'); },750); }
        if(oldDot) oldDot.classList.remove('on');
        if(oldTh)  oldTh.classList.remove('on');

        cur=idx;

        /* incoming */
        var newSl=sl(cur);
        if(newSl){
            newSl.classList.remove('ginc-leaving','ginc-shown');
            newSl.classList.add('ginc-active');
            setTimeout(function(){
                newSl.classList.remove('ginc-active');
                newSl.classList.add('ginc-shown');
                busy=false;
            },750);
        } else { busy=false; }

        if(dot(cur)) dot(cur).classList.add('on');
        if(th(cur)){
            th(cur).classList.add('on');
            /* Scroll only the thumb strip, not the page */
            if(thumbsEl){
                var t=th(cur);
                var stripLeft=thumbsEl.scrollLeft;
                var stripW=thumbsEl.offsetWidth;
                var tLeft=t.offsetLeft;
                var tW=t.offsetWidth;
                var target=tLeft-(stripW/2)+(tW/2);
                thumbsEl.scrollTo({left:target,behavior:'smooth'});
            }
        }

        restartProg();
    }

    function startAuto(){
        stopAuto();
        if(total<=1) return;
        timer=setInterval(function(){ goTo(cur+1); },DELAY);
        restartProg();
    }
    function stopAuto(){
        clearInterval(timer); timer=null;
        if(progEl){ progEl.classList.remove('go'); progEl.style.width='0%'; }
    }
    function restartProg(){
        if(!progEl||paused) return;
        progEl.classList.remove('go'); progEl.style.width='0%';
        void progEl.offsetWidth;
        progEl.classList.add('go');
    }
    function bump(){ stopAuto(); startAuto(); }

    /* pause/resume */
    if(stageEl){
        stageEl.addEventListener('mouseenter',function(){ paused=true; stopAuto(); });
        stageEl.addEventListener('mouseleave',function(){ paused=false; startAuto(); });

        /* custom cursor follow */
        stageEl.addEventListener('mousemove',function(e){
            var r=stageEl.getBoundingClientRect();
            var x=e.clientX-r.left, y=e.clientY-r.top;
            if(cursorEl){ cursorEl.style.left=x+'px'; cursorEl.style.top=y+'px'; }

            /* Mouse parallax — move current image opposite to cursor */
            var cx=r.width/2, cy=r.height/2;
            var dx=(x-cx)/cx, dy=(y-cy)/cy;
            var curImg=img(cur);
            if(curImg){
                curImg.style.transition='transform .25s ease-out';
                curImg.style.transform='scale(1.08) translate('+(-dx*1.5)+'%, '+(-dy*1)+'%)';
            }
        });
        stageEl.addEventListener('mouseleave',function(){
            var curImg=img(cur);
            if(curImg){
                curImg.style.transition='transform .6s ease-out';
                curImg.style.transform='scale(1.04) translate(0,0)';
            }
        });

        /* click → lightbox */
        stageEl.addEventListener('click',function(e){
            if(e.target.closest('.ginc-arr')||e.target.closest('.ginc-dots')||e.target.closest('.ginc-cursor')) return;
            lbOpen(cur);
        });

        /* touch swipe */
        var hx=0;
        stageEl.addEventListener('touchstart',function(e){ hx=e.touches[0].clientX; },{passive:true});
        stageEl.addEventListener('touchend',function(e){
            var dx=e.changedTouches[0].clientX-hx;
            if(Math.abs(dx)>50){ goTo(cur+(dx<0?1:-1)); bump(); }
        },{passive:true});
    }

    /* buttons */
    var btnP=document.getElementById(ID+'-prev');
    var btnN=document.getElementById(ID+'-next');
    if(btnP) btnP.addEventListener('click',function(e){ e.stopPropagation(); goTo(cur-1); bump(); });
    if(btnN) btnN.addEventListener('click',function(e){ e.stopPropagation(); goTo(cur+1); bump(); });

    /* dots */
    if(dotsEl) Array.from(dotsEl.children).forEach(function(d){
        d.addEventListener('click',function(){ goTo(parseInt(d.dataset.gi)); bump(); });
    });

    /* thumbnails */
    if(thumbsEl) Array.from(thumbsEl.children).forEach(function(t){
        t.addEventListener('click',function(){ goTo(parseInt(t.dataset.gi)); bump(); });
    });

    /* keyboard */
    document.addEventListener('keydown',function(e){
        if(lbEl) return;
        if(e.key==='ArrowLeft') { goTo(cur-1); bump(); }
        if(e.key==='ArrowRight'){ goTo(cur+1); bump(); }
    });

    startAuto();
}

/* ═══════════ POLAROID ═══════════ */
if(mode==='polaroid'){
    var curPol=0;
    var featEl  = document.getElementById(ID+'-feat');
    var featImg = document.getElementById(ID+'-fimg');
    var featLbl = document.getElementById(ID+'-flbl');
    var shineEl = document.getElementById(ID+'-shine');
    var wall    = document.getElementById(ID+'-wall');

    function setPol(idx){
        if(idx===curPol) return;
        var url=photos[idx];
        if(featImg){
            featImg.style.opacity='0';
            setTimeout(function(){
                featImg.src=url;
                featImg.style.opacity='1';
            },420);
        }
        if(featLbl) featLbl.textContent='\u2665 '+(2020+idx)+' \u2665';
        if(wall) Array.from(wall.querySelectorAll('.ginp-pol')).forEach(function(p,i){
            p.classList.toggle('ginp-pol-active',i===idx);
        });
        curPol=idx;
    }

    /* 3D tilt on featured card */
    if(featEl){
        featEl.addEventListener('mousemove',function(e){
            featEl.classList.add('ginp-tilted');
            var r=featEl.getBoundingClientRect();
            var x=e.clientX-r.left, y=e.clientY-r.top;
            var cx=r.width/2, cy=r.height/2;
            var rx=((y-cy)/cy)*-14;
            var ry=((x-cx)/cx)*14;
            featEl.style.transform='rotateX('+rx+'deg) rotateY('+ry+'deg) scale(1.04)';
            featEl.style.boxShadow='0 '+Math.max(20,(cy-y)/3)+'px 60px rgba(0,0,0,.55), 0 24px 80px rgba(0,0,0,.5)';
            if(shineEl){
                var px=Math.round(x/r.width*100);
                var py=Math.round(y/r.height*100);
                shineEl.style.background='radial-gradient(circle at '+px+'% '+py+'%, rgba(255,255,255,.22) 0%, transparent 70%)';
            }
        });
        featEl.addEventListener('mouseleave',function(){
            featEl.classList.remove('ginp-tilted');
            featEl.style.transform='';
            featEl.style.boxShadow='';
            if(shineEl) shineEl.style.background='radial-gradient(circle at 30% 30%, rgba(255,255,255,.18) 0%, transparent 65%)';
        });
        featEl.addEventListener('click',function(){ lbOpen(curPol); });
    }

    /* wall clicks */
    if(wall) Array.from(wall.querySelectorAll('.ginp-pol')).forEach(function(p){
        p.addEventListener('click',function(){ setPol(parseInt(p.dataset.i)); });
    });

    /* auto-rotate */
    if(photos.length>1){
        setInterval(function(){
            setPol((curPol+1)%Math.min(photos.length,6));
        },3200);
    }
}

}());
</script>
@endif
