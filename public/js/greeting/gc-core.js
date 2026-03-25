/**
 * gc-core.js
 * Greeting Card — Core: buka kartu, confetti, balon, scroll reveal,
 *                       virtual card tilt, musik
 *
 * Dependensi (muat lebih dulu):
 *   gc-parallax.js  → window.GcParallax
 *   gc-threejs.js   → window.GcThreeJS   (butuh THREE / three.min.js)
 *   gc-ajax.js      → window.GcAjax
 *
 * window.GC_SLUG  — slug halaman
 * window.GC_DEMO  — boolean preview mode
 *
 * Entry points:
 *   window.gcOpenCard()    — dipanggil dari onclick cover
 *   window.gcToggleMusic() — dipanggil dari tombol musik
 */
(function () {
    'use strict';

    /* ── Boot AJAX module segera (perlu SLUG/CSRF) ── */
    if (window.GcAjax) GcAjax.boot();

    /* ════════════════════════════════════════
       1. BUKA KARTU
    ════════════════════════════════════════ */
    let _opened = false;

    window.gcOpenCard = function () {
        if (_opened) return;
        _opened = true;

        const cover = document.getElementById('gc-cover');
        const main  = document.getElementById('gc-main');
        if (cover) cover.classList.add('gc-cover--closing');

        setTimeout(() => {
            if (cover) cover.style.display = 'none';
            if (main)  main.classList.add('gc-main--visible');

            /* Inisialisasi modul visual */
            if (window.GcThreeJS)  GcThreeJS.init();
            if (window.GcParallax) GcParallax.init();

            gcLaunchConfetti();
            gcSpawnBalloons();
            _initScrollReveal();
            _initVirtualCardTilt();

            /* AJAX hanya di halaman publik (bukan preview) */
            if (!window.GC_DEMO && window.GcAjax) {
                GcAjax.loadReactions();
                GcAjax.loadGallery();
            }

            gcAutoPlayMusic();
        }, 650);
    };

    /* ════════════════════════════════════════
       2. CONFETTI
    ════════════════════════════════════════ */
    window.gcLaunchConfetti = function () {
        const canvas = document.getElementById('gc-canvas');
        if (!canvas) return;
        canvas.width  = window.innerWidth;
        canvas.height = window.innerHeight;
        canvas.style.display = 'block';

        const ctx    = canvas.getContext('2d');
        const colors = ['#ff6b9d','#ffd166','#06d6a0','#118ab2','#ef476f',
                         '#8338ec','#fb5607','#ffbe0b','#c084fc'];
        const pieces = Array.from({ length: 160 }, (_, i) => ({
            x:     Math.random() * canvas.width,
            y:     -20 - Math.random() * 300,
            w:     5  + Math.random() * 11,
            h:     4  + Math.random() * 7,
            color: colors[i % colors.length],
            vx:    (Math.random() - 0.5) * 5,
            vy:    2.5 + Math.random() * 4,
            angle: Math.random() * Math.PI * 2,
            spin:  (Math.random() - 0.5) * 0.18,
            life:  1,
        }));

        let frame = 0;
        (function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            let alive = false;
            pieces.forEach(p => {
                p.x += p.vx; p.y += p.vy; p.angle += p.spin; p.life -= 0.005;
                if (p.y < canvas.height + 40 && p.life > 0) {
                    alive = true;
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.angle);
                    ctx.globalAlpha = Math.max(0, p.life);
                    ctx.fillStyle   = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                }
            });
            frame++;
            if (alive || frame < 100) requestAnimationFrame(draw);
            else canvas.style.display = 'none';
        })();
    };

    /* ════════════════════════════════════════
       3. BALON MENGAMBANG
    ════════════════════════════════════════ */
    window.gcSpawnBalloons = function () {
        const wrap = document.getElementById('gc-balloons-hero');
        if (!wrap) return;
        const emojis = ['🎈','🎉','🌟','💖','✨','🎊','🎁','🎀'];
        for (let i = 0; i < 10; i++) {
            const el = document.createElement('span');
            el.className   = 'gc-float-ball';
            el.textContent = emojis[i % emojis.length];
            el.style.cssText =
                `left:${5 + Math.random() * 90}%;` +
                `font-size:${1 + Math.random() * 1.5}rem;` +
                `animation-duration:${5 + Math.random() * 5}s;` +
                `animation-delay:${Math.random() * 3}s;`;
            wrap.appendChild(el);
        }
    };

    /* ════════════════════════════════════════
       4. VIRTUAL CARD 3-D TILT
    ════════════════════════════════════════ */
    function _initVirtualCardTilt() {
        const card = document.getElementById('gc-virtual-card');
        if (!card) return;

        const applyTilt = (cx, cy, rect) => {
            const rx = (cy - rect.top  - rect.height / 2) / rect.height * -28;
            const ry = (cx - rect.left - rect.width  / 2) / rect.width  *  28;
            card.style.transform =
                `perspective(700px) rotateX(${rx}deg) rotateY(${ry}deg) scale(1.04)`;
        };

        card.addEventListener('mousemove',  e => applyTilt(e.clientX, e.clientY, card.getBoundingClientRect()));
        card.addEventListener('mouseleave', () => { card.style.transform = ''; });
        card.addEventListener('touchmove',  e => {
            const t = e.touches[0];
            applyTilt(t.clientX, t.clientY, card.getBoundingClientRect());
        }, { passive: true });
        card.addEventListener('touchend', () => { card.style.transform = ''; });
    }

    /* ════════════════════════════════════════
       5. SCROLL REVEAL (IntersectionObserver)
    ════════════════════════════════════════ */
    function _initScrollReveal() {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('gc-revealed');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });

        window._gcRevealObserver = io;   /* digunakan oleh gc-ajax.js untuk item baru */
        document.querySelectorAll('.gc-reveal').forEach(el => io.observe(el));
    }

    /* ════════════════════════════════════════
       6. MUSIK LATAR
    ════════════════════════════════════════ */
    let _musicPlaying = false;

    window.gcAutoPlayMusic = function () {
        const a = document.getElementById('gc-audio');
        if (!a) return;
        a.volume = 0.5;
        a.play().catch(() => {});   /* Autoplay mungkin diblokir browser */
        _musicPlaying = true;
        _updateMusicBtn();
    };

    window.gcToggleMusic = function () {
        const a = document.getElementById('gc-audio');
        if (!a) return;
        if (_musicPlaying) { a.pause(); _musicPlaying = false; }
        else               { a.play().catch(() => {}); _musicPlaying = true; }
        _updateMusicBtn();
    };

    function _updateMusicBtn() {
        const b = document.getElementById('gc-music-btn');
        if (b) b.textContent = _musicPlaying ? '🔇' : '🎵';
    }
})();
