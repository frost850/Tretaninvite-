/*!
 * VIP Royal — JS
 * TretanInvite exclusive template
 */
(function () {
    'use strict';

    /* ── helpers ── */
    const $ = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => [...c.querySelectorAll(s)];

    /* ════════════════════════════
       PARTICLES
    ════════════════════════════ */
    (function initParticles() {
        const canvas = document.getElementById('vr-particles');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let W, H, pts = [];

        const resize = () => {
            W = canvas.width  = window.innerWidth;
            H = canvas.height = window.innerHeight;
        };
        resize();
        window.addEventListener('resize', resize);

        const rand = (a, b) => a + Math.random() * (b - a);
        const COLORS = ['rgba(201,168,76,.25)', 'rgba(201,168,76,.12)', 'rgba(155,89,208,.18)', 'rgba(255,255,255,.08)'];

        for (let i = 0; i < 90; i++) {
            pts.push({
                x: rand(0, 1), y: rand(0, 1),
                r: rand(.6, 2.2),
                dx: rand(-.008, .008), dy: rand(-.006, -.002),
                c: COLORS[Math.floor(Math.random() * COLORS.length)],
                a: rand(.2, .9), da: rand(-.005, .005)
            });
        }

        (function loop() {
            ctx.clearRect(0, 0, W, H);
            pts.forEach(p => {
                p.x += p.dx; p.y += p.dy; p.a += p.da;
                if (p.x < 0) p.x = 1; if (p.x > 1) p.x = 0;
                if (p.y < 0) p.y = 1; if (p.y > 1) p.y = 0;
                if (p.a < .1 || p.a > .95) p.da *= -1;
                ctx.save();
                ctx.globalAlpha = p.a;
                ctx.fillStyle = p.c;
                ctx.beginPath();
                ctx.arc(p.x * W, p.y * H, p.r, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            });
            requestAnimationFrame(loop);
        })();
    })();

    /* ════════════════════════════
       COVER OPEN
    ════════════════════════════ */
    window.vrOpenInvitation = function () {
        const cover = document.getElementById('vr-cover');
        const main  = document.getElementById('vr-main');
        if (!cover || !main) return;
        cover.classList.add('open');
        main.classList.add('visible');
        setTimeout(() => {
            cover.style.display = 'none';
            window.dispatchEvent(new Event('scroll'));
        }, 750);
        setTimeout(vrStartMusic, 700); // 700ms: player ready but still within gesture window
    };

    /* ════════════════════════════
       NAVBAR SHOW ON SCROLL
    ════════════════════════════ */
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('vr-nav');
        if (!nav) return;
        nav.classList.toggle('shown', window.scrollY > 80);
    }, { passive: true });

    /* ════════════════════════════
       SCROLL REVEAL
    ════════════════════════════ */
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
        });
    }, { threshold: .12, rootMargin: '0px 0px -40px 0px' });

    $$('.vr-rv').forEach((el, i) => {
        el.style.transitionDelay = (i % 5) * .07 + 's';
        io.observe(el);
    });

    /* ════════════════════════════
       SPLIT-SCREEN HERO
    ════════════════════════════ */
    (function initSplitHero() {
        const hero  = document.getElementById('vr-hero');
        const splitL = document.getElementById('vr-split-l');
        const splitR = document.getElementById('vr-split-r');
        const line   = document.getElementById('vr-split-line');
        if (!hero || !splitL || !splitR) return;

        let raf = null;
        function applySplit(x) {
            // x: 0..1 position within hero
            const offset = (x - .5) * 16;       // ±8% zone
            const split  = 50 + offset;
            splitL.style.clipPath = `inset(0 ${100 - split}% 0 0)`;
            splitR.style.clipPath = `inset(0 0 0 ${split}%)`;
            if (line) line.style.left = split + '%';
            // Subtle parallax on photos
            const px = (-offset * 2).toFixed(1) + 'px';
            splitL.style.backgroundPositionX = `calc(50% + ${px})`;
            splitR.style.backgroundPositionX = `calc(50% - ${px})`;
        }

        hero.addEventListener('mousemove', function(e) {
            if (raf) cancelAnimationFrame(raf);
            const rect = hero.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width;
            raf = requestAnimationFrame(() => applySplit(Math.max(0, Math.min(1, x))));
        });

        hero.addEventListener('mouseleave', function() {
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => applySplit(.5));
        });

        hero.addEventListener('touchmove', function(e) {
            const touch = e.touches[0];
            const rect  = hero.getBoundingClientRect();
            const x = (touch.clientX - rect.left) / rect.width;
            applySplit(Math.max(0, Math.min(1, x)));
        }, { passive: true });
    })();

    /* ════════════════════════════
       COUNTDOWN
    ════════════════════════════ */
    (function initCountdown() {
        const target = window.vrEventDate;
        if (!target) return;
        const end = new Date(target + 'T00:00:00');
        const els = {
            h: document.getElementById('vr-hari'),
            j: document.getElementById('vr-jam'),
            m: document.getElementById('vr-menit'),
            d: document.getElementById('vr-detik'),
        };
        if (!els.h) return;
        const pad = n => String(n).padStart(2, '0');
        function tick() {
            const diff = end - Date.now();
            if (diff <= 0) {
                Object.values(els).forEach(el => el && (el.textContent = '00'));
                return;
            }
            const secs  = Math.floor(diff / 1000);
            const mins  = Math.floor(secs / 60);
            const hours = Math.floor(mins / 60);
            const days  = Math.floor(hours / 24);
            els.h.textContent = pad(days);
            els.j.textContent = pad(hours % 24);
            els.m.textContent = pad(mins % 60);
            els.d.textContent = pad(secs % 60);
        }
        tick();
        setInterval(tick, 1000);
    })();

    /* ════════════════════════════
       MUSIC  (MP3 + YouTube audio-only)
    ════════════════════════════ */
    var vrAudio = null, vrPlayer = null, vrPlaying = false;
    var vrIsYt  = !!(window.vrYtVideoId && window.vrYtVideoId.length > 0);

    function vrSetBtnState(playing) {
        vrPlaying = playing;
        var btn = document.getElementById('vr-music-btn');
        if (!btn) return;
        btn.textContent = playing ? '♪' : '♩';
        btn.classList.toggle('active', playing);
        btn.title = playing ? 'Hentikan Musik' : 'Putar Musik';
    }

    function vrStartAudio() {
        if (!window.vrMusicUrl) return;
        if (!vrAudio) {
            vrAudio = new Audio(window.vrMusicUrl);
            vrAudio.loop   = true;
            vrAudio.volume = 0.35;
        }
        vrAudio.play().then(function () { vrSetBtnState(true); }).catch(function () {});
    }

    function vrPauseAudio() {
        if (vrAudio) { vrAudio.pause(); }
        vrSetBtnState(false);
    }

    window.vrToggleMusic = function () {
        if (vrIsYt) {
            if (!vrPlayer) return;
            try {
                var st = vrPlayer.getPlayerState();
                if (st === 1) { vrPlayer.pauseVideo(); vrSetBtnState(false); }
                else          { vrPlayer.playVideo();  vrSetBtnState(true);  }
            } catch (e) {}
        } else {
            if (vrPlaying) vrPauseAudio();
            else           vrStartAudio();
        }
    };

    function vrStartMusic() {
        if (vrIsYt) {
            if (vrPlayer) {
                try { vrPlayer.playVideo(); vrSetBtnState(true); } catch (e) {}
            }
        } else if (window.vrMusicUrl) {
            vrStartAudio();
        }
    }

    /* ── YouTube IFrame API ── */
    if (vrIsYt) {
        window.onYouTubeIframeAPIReady = function () {
            var playerEl = document.getElementById('vr-yt-player');
            if (!playerEl) return;
            vrPlayer = new YT.Player('vr-yt-player', {
                videoId: window.vrYtVideoId,
                playerVars: { autoplay: 0, controls: 0, loop: 1, playlist: window.vrYtVideoId, iv_load_policy: 3, modestbranding: 1, playsinline: 1 },
                events: {
                    onStateChange: function (e) {
                        vrSetBtnState(e.data === YT.PlayerState.PLAYING);
                    },
                },
            });
        };
        // Inject YT API script if not already loaded
        if (!window.YT || !window.YT.Player) {
            var ytTag = document.createElement('script');
            ytTag.src = 'https://www.youtube.com/iframe_api';
            document.head.appendChild(ytTag);
        }
    }

    /* ════════════════════════════
       COPY TO CLIPBOARD
    ════════════════════════════ */
    window.vrCopy = function (text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.textContent;
            btn.textContent = 'Tersalin!';
            btn.classList.add('copied');
            setTimeout(() => { btn.textContent = orig; btn.classList.remove('copied'); }, 2000);
        });
    };

    /* ════════════════════════════
       RSVP FORM
    ════════════════════════════ */
    (function initRsvp() {
        const form = document.getElementById('vr-rsvp-form');
        if (!form) return;

        // Guard: only allow ONE submission ever
        let rsvpSubmitted = false;

        const hadir  = $$('[name="vr_hadir"]', form);
        const rowJml = document.getElementById('vr-row-jml');

        hadir.forEach(r => {
            r.addEventListener('change', () => {
                if (rowJml) rowJml.style.display = r.value === 'hadir' ? '' : 'none';
            });
        });

        function showRsvpThanks() {
            form.style.display = 'none';
            const ok = document.getElementById('vr-rsvp-ok');
            if (ok) ok.style.display = 'block';
        }

        form.addEventListener('submit', e => {
            e.preventDefault();
            if (rsvpSubmitted) return;   // block any double-click
            rsvpSubmitted = true;

            const action    = form.dataset.action;
            const hadir_val = $('[name="vr_hadir"]:checked', form)?.value || 'mungkin';
            const payload   = new FormData();
            payload.append('_token',       $('[name="_token"]', form).value);
            payload.append('guest_name',   $('[name="name"]', form).value);
            payload.append('attendance',   hadir_val);
            payload.append('guests_count', $('[name="jumlah"]', form)?.value || 1);
            payload.append('message',      $('[name="pesan"]', form)?.value || '');

            const btn = $('[type=submit]', form);
            btn.disabled    = true;
            btn.textContent = 'Mengirim…';

            fetch(action, { method: 'POST', body: payload })
                .then(r => r.json())
                .then(() => { showRsvpThanks(); })
                .catch(() => { showRsvpThanks(); }); // show thanks even on network error
        });
    })();

    /* Guestbook form is handled by inline script in vip-royal.blade.php */

})();
