/* ═══════════════════════════════════════════════════
   DARK ROMANCE 3D — Interactive JS
   Features : Canvas ember particles · Mouse 3D parallax
              Card tilt · Curtain fold · Countdown flip
              Gyroscope · Ripple · Scroll reveal · RSVP
═══════════════════════════════════════════════════ */
(function () {
    'use strict';

    /* ── References ─────────────────────────────── */
    const curtain   = document.getElementById('dr-curtain');
    const openBtn   = document.getElementById('dr-open-btn');
    const mainEl    = document.getElementById('dr-main');
    const petalsEl  = document.getElementById('dr-petals');
    const canvas    = document.getElementById('dr-canvas');
    const navEl     = document.querySelector('.dr-nav');

    const weddingData = window.weddingData || {};
    const eventDate   = weddingData.eventDate ? new Date(weddingData.eventDate + 'T00:00:00') : null;

    /* ═══════════════════════════════════════════
       1. CURSOR TRAIL DOT
    ═══════════════════════════════════════════ */
    const cursorDot = document.createElement('div');
    cursorDot.className = 'dr-cursor-dot';
    document.body.appendChild(cursorDot);

    let cursorVisible = false;
    document.addEventListener('mousemove', function (e) {
        cursorDot.style.left = e.clientX + 'px';
        cursorDot.style.top  = e.clientY + 'px';
        if (!cursorVisible) { cursorDot.style.opacity = '1'; cursorVisible = true; }
    });
    document.addEventListener('mouseleave', function () {
        cursorDot.style.opacity = '0'; cursorVisible = false;
    });

    /* ═══════════════════════════════════════════
       2. CANVAS EMBER PARTICLE SYSTEM
    ═══════════════════════════════════════════ */
    function initCanvas() {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let W, H, particles = [];

        function resize() {
            W = canvas.width  = window.innerWidth;
            H = canvas.height = window.innerHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        function rand(min, max) { return Math.random() * (max - min) + min; }

        function Ember() {
            this.reset = function () {
                this.x     = rand(0, W);
                this.y     = rand(H * .2, H * 1.1);
                this.vx    = rand(-0.28, 0.28);
                this.vy    = rand(-1.1, -0.35);  /* float upward */
                this.r     = rand(1.1, 2.8);
                this.alpha = rand(.12, .55);
                this.fade  = rand(.003, .009);
                this.pulse = rand(0, Math.PI * 2);
                this.wobble= rand(.01, .025);
                /* colour: deep crimson → rose → pale */
                const hue  = rand(340, 355);
                const sat  = rand(55, 85);
                const lgt  = rand(35, 65);
                this.color = `hsl(${hue},${sat}%,${lgt}%)`;
            };
            this.reset();
            /* stagger */
            this.y = rand(0, H);
        }

        Ember.prototype.update = function () {
            this.pulse += this.wobble;
            this.x += this.vx + Math.sin(this.pulse) * .18;
            this.y += this.vy;
            this.alpha -= this.fade;
            if (this.alpha <= 0 || this.y < -20) this.reset();
        };

        Ember.prototype.draw = function (ctx) {
            ctx.save();
            ctx.globalAlpha = Math.max(0, this.alpha);
            ctx.fillStyle   = this.color;
            ctx.shadowColor = this.color;
            ctx.shadowBlur  = this.r * 3.5;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        };

        /* Active ember count — scale with screen size */
        const COUNT = Math.min(90, Math.round(W * H / 14000));
        for (let i = 0; i < COUNT; i++) { particles.push(new Ember()); }

        function loop() {
            ctx.clearRect(0, 0, W, H);
            particles.forEach(function (p) { p.update(); p.draw(ctx); });
            requestAnimationFrame(loop);
        }
        loop();
    }
    initCanvas();

    /* ═══════════════════════════════════════════
       3. ROSE PETAL RAIN (DOM-based)
    ═══════════════════════════════════════════ */
    function initPetals() {
        if (!petalsEl) return;
        const PETAL_COUNT = 14;
        const svgs = [
            '<svg viewBox="0 0 20 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1 C14 6 18 12 18 18 C18 24 14 27 10 27 C6 27 2 24 2 18 C2 12 6 6 10 1Z" fill="rgba(139,20,40,0.55)"/></svg>',
            '<svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="11" cy="11" rx="9" ry="5.5" fill="rgba(181,96,112,0.45)" transform="rotate(-20 11 11)"/></svg>',
            '<svg viewBox="0 0 18 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 1 C13 7 15 14 13 20 C11 25 7 26 4 22 C1 18 3 10 9 1Z" fill="rgba(212,154,166,0.4)"/></svg>',
        ];

        for (let i = 0; i < PETAL_COUNT; i++) {
            const p = document.createElement('div');
            p.className = 'dr-petal';
            const size  = 10 + Math.random() * 16;
            const left  = Math.random() * 100;
            const dur   = 10 + Math.random() * 12;
            const delay = -Math.random() * 20;
            const rotX  = Math.random() * 360;
            const transX= (Math.random() - .5) * 120;
            const transZ= (Math.random() - .5) * 80;
            const svgEl = document.createElement('div');
            svgEl.style.cssText = 'width:100%;height:100%;';
            svgEl.innerHTML = svgs[i % svgs.length];

            p.style.cssText = [
                'width:' + size + 'px',
                'height:' + (size * 1.35) + 'px',
                'left:' + left + '%',
                '--pd:' + dur + 's',
                '--pdel:' + delay + 's',
                '--pr:' + rotX + 'deg',
                '--px:' + transX + 'px',
                '--pz:' + transZ + 'px',
            ].join(';');

            p.appendChild(svgEl);
            petalsEl.appendChild(p);
        }
    }
    initPetals();

    /* ═══════════════════════════════════════════
       4. CURTAIN — 3D FOLD REVEAL
    ═══════════════════════════════════════════ */
    function openInvitation() {
        if (!curtain) return;
        curtain.classList.add('hidden');
        if (mainEl) mainEl.removeAttribute('style');
        setTimeout(function () { curtain.style.display = 'none'; }, 950);
        if (navEl) setTimeout(function () { navEl.classList.add('visible'); }, 1200);
    }

    /* Expose globally for onclick in Blade */
    window.openInvitation = openInvitation;

    if (openBtn) {
        openBtn.addEventListener('click', function (e) {
            /* ripple burst on button */
            addRipple(openBtn, e, true);
            setTimeout(openInvitation, 180);
        });
    }

    /* ═══════════════════════════════════════════
       5. GLOBAL RIPPLE ON CLICK
    ═══════════════════════════════════════════ */
    function addRipple(target, e, local) {
        const r   = document.createElement('div');
        r.className = 'dr-ripple';
        const rect  = target.getBoundingClientRect();
        const cx    = local ? (e.clientX - rect.left) : (rect.width  / 2);
        const cy    = local ? (e.clientY - rect.top)  : (rect.height / 2);
        r.style.left = cx + 'px';
        r.style.top  = cy + 'px';
        target.appendChild(r);
        r.addEventListener('animationend', function () { r.remove(); });
    }

    document.addEventListener('click', function (e) {
        /* Only on interactive / clickable elements */
        const tgt = e.target.closest('.dr-open-btn, .dr-copy-btn, .dr-btn-outline, .dr-submit-btn, .dr-hadir-toggle label, .dr-acara-card');
        if (tgt && !tgt.classList.contains('dr-open-btn')) {
            addRipple(tgt, e, true);
        }
    });

    /* ═══════════════════════════════════════════
       6. NAV HIDE / SHOW ON SCROLL
    ═══════════════════════════════════════════ */
    var lastScrollY = 0;
    window.addEventListener('scroll', function () {
        var y = window.scrollY;
        if (!navEl) return;
        if (y > 80) {
            navEl.classList.toggle('visible', y < lastScrollY);
        } else {
            navEl.classList.remove('visible');
        }
        lastScrollY = y;
    }, { passive: true });

    /* ═══════════════════════════════════════════
       7. SCROLL REVEAL — 3D
    ═══════════════════════════════════════════ */
    function initReveal() {
        var els = document.querySelectorAll('.reveal');
        if (!window.IntersectionObserver) {
            els.forEach(function (el) { el.classList.add('in'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) {
                    en.target.classList.add('in');
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        els.forEach(function (el) { io.observe(el); });
    }
    initReveal();

    /* ═══════════════════════════════════════════
       8. HERO PARALLAX (mouse → translateZ depth)
    ═══════════════════════════════════════════ */
    function initHeroParallax() {
        var hero = document.querySelector('.dr-hero');
        if (!hero) return;

        var layers = [
            { el: hero.querySelector('.dr-hero-bismillah'), dX: 8,  dY: 5,  dZ: 12 },
            { el: hero.querySelector('.dr-hero-eyebrow'),   dX: 10, dY: 7,  dZ: 20 },
            { el: hero.querySelector('.dr-hero-kepada'),    dX: 6,  dY: 4,  dZ: 15 },
            { el: hero.querySelector('.dr-hero-names'),     dX: 18, dY: 12, dZ: 35 },
            { el: hero.querySelector('.dr-hero-date-wrap'), dX: 10, dY: 7,  dZ: 20 },
            { el: hero.querySelector('.dr-hero-cta'),       dX: 6,  dY: 4,  dZ: 10 },
        ].filter(function (l) { return !!l.el; });

        var mX = 0, mY = 0, curX = 0, curY = 0;

        document.addEventListener('mousemove', function (e) {
            mX = (e.clientX / window.innerWidth  - .5) * 2;
            mY = (e.clientY / window.innerHeight - .5) * 2;
        }, { passive: true });

        function tick() {
            curX += (mX - curX) * .065;
            curY += (mY - curY) * .065;
            layers.forEach(function (l) {
                var tx = -curX * l.dX;
                var ty = -curY * l.dY;
                var tz = (1 - Math.abs(curX) * .5 - Math.abs(curY) * .5) * l.dZ;
                l.el.style.transform = 'translate3d(' + tx + 'px,' + ty + 'px,' + tz + 'px)';
            });
            requestAnimationFrame(tick);
        }
        tick();
    }
    initHeroParallax();

    /* ═══════════════════════════════════════════
       9. 3D CARD TILT (photo / acara / gift cards)
    ═══════════════════════════════════════════ */
    function initCardTilt() {
        var MAX_ROT  = 12;
        var EASE     = 0.10;

        /* Cards with tilt-card-inner wrapper */
        document.querySelectorAll('.dr-tilt-card').forEach(function (wrapper) {
            var inner = wrapper.querySelector('.dr-tilt-card-inner');
            if (!inner) return;
            var tX = 0, tY = 0, cX = 0, cY = 0, raf;

            wrapper.addEventListener('mousemove', function (e) {
                var rect = wrapper.getBoundingClientRect();
                var pX = ((e.clientX - rect.left) / rect.width  - .5) * 2;
                var pY = ((e.clientY - rect.top)  / rect.height - .5) * 2;
                tX =  pY * MAX_ROT;
                tY = -pX * MAX_ROT;
            }, { passive: true });

            wrapper.addEventListener('mouseleave', function () {
                tX = 0; tY = 0;
            });

            function tick() {
                cX += (tX - cX) * EASE;
                cY += (tY - cY) * EASE;
                if (Math.abs(tX - cX) > .01 || Math.abs(tY - cY) > .01) {
                    inner.style.transform = 'perspective(700px) rotateX(' + cX + 'deg) rotateY(' + cY + 'deg)';
                    raf = requestAnimationFrame(tick);
                } else {
                    inner.style.transform = 'perspective(700px) rotateX(' + cX + 'deg) rotateY(' + cY + 'deg)';
                }
            }

            wrapper.addEventListener('mouseenter', function () {
                raf && cancelAnimationFrame(raf);
                raf = requestAnimationFrame(tick);
            });
        });

        /* Acara + gift cards (simple hover tilt fallback) */
        document.querySelectorAll('.dr-acara-card, .dr-gift-card').forEach(function (card) {
            var tX = 0, tY = 0, cX = 0, cY = 0, raf;
            var MXROT = 8;

            card.addEventListener('mousemove', function (e) {
                var rect = card.getBoundingClientRect();
                var pX = ((e.clientX - rect.left) / rect.width  - .5) * 2;
                var pY = ((e.clientY - rect.top)  / rect.height - .5) * 2;
                tX =  pY * MXROT;
                tY = -pX * MXROT;
            }, { passive: true });

            card.addEventListener('mouseleave', function () { tX = 0; tY = 0; });

            function loop() {
                cX += (tX - cX) * .1;
                cY += (tY - cY) * .1;
                card.style.transform = 'perspective(700px) rotateX(' + cX + 'deg) rotateY(' + cY + 'deg) translateY(-6px)';
                if (tX !== 0 || tY !== 0 || Math.abs(cX) > .05 || Math.abs(cY) > .05) {
                    raf = requestAnimationFrame(loop);
                } else {
                    card.style.transform = '';
                }
            }

            card.addEventListener('mouseenter', function () {
                raf && cancelAnimationFrame(raf);
                raf = requestAnimationFrame(loop);
            });
        });
    }
    initCardTilt();

    /* ═══════════════════════════════════════════
       10. GYROSCOPE TILT (mobile)
    ═══════════════════════════════════════════ */
    function initGyroscope() {
        if (!window.DeviceOrientationEvent) return;
        var heroNames = document.querySelector('.dr-hero-names');
        if (!heroNames) return;

        window.addEventListener('deviceorientation', function (e) {
            if (e.beta === null || e.gamma === null) return;
            var gX = Math.min(Math.max(e.gamma / 20, -1), 1); /* left-right */
            var gY = Math.min(Math.max(e.beta  / 30, -1), 1); /* front-back */
            heroNames.style.transform = 'translate3d(' + (gX * 10) + 'px,' + (gY * 6) + 'px, 20px)';
        }, { passive: true });
    }
    initGyroscope();

    /* ═══════════════════════════════════════════
       11. COUNTDOWN — 3D FLIP DIGITS
    ═══════════════════════════════════════════ */
    var cdHari   = document.getElementById('dr-cd-hari');
    var cdJam    = document.getElementById('dr-cd-jam');
    var cdMenit  = document.getElementById('dr-cd-menit');
    var cdDetik  = document.getElementById('dr-cd-detik');

    var prevVals = { h: null, j: null, m: null, d: null };

    function flipNum(el, newVal) {
        if (!el) return;
        var str = String(newVal).padStart(2, '0');
        if (el.textContent === str) return;
        el.style.animation = 'none';
        el.offsetHeight; /* reflow */
        el.style.animation = 'cd-flip .55s ease both';
        el.textContent = str;
    }

    function updateCountdown() {
        if (!eventDate) return;
        var now  = new Date();
        var diff = eventDate - now;
        if (diff <= 0) {
            [cdHari, cdJam, cdMenit, cdDetik].forEach(function (el) {
                if (el) el.textContent = '00';
            });
            return;
        }
        var hari  = Math.floor(diff / 864e5);
        var jam   = Math.floor((diff % 864e5) / 36e5);
        var menit = Math.floor((diff % 36e5)  / 6e4);
        var detik = Math.floor((diff % 6e4)   / 1e3);

        flipNum(cdHari,  hari);
        flipNum(cdJam,   jam);
        flipNum(cdMenit, menit);
        flipNum(cdDetik, detik);
    }

    if (eventDate) {
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    /* ═══════════════════════════════════════════
       12. COPY TEXT (Amplop Digital)
    ═══════════════════════════════════════════ */
    window.drCopyText = function (text, btn) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var orig = btn ? btn.textContent : '';
            if (btn) {
                btn.textContent = 'Tersalin ✓';
                btn.style.borderColor  = 'rgba(139,20,40,.65)';
                btn.style.color        = 'var(--dr-blush)';
                btn.style.boxShadow    = '0 0 18px rgba(139,20,40,.25)';
                setTimeout(function () {
                    btn.textContent = orig;
                    btn.style.borderColor  = '';
                    btn.style.color        = '';
                    btn.style.boxShadow    = '';
                }, 2400);
            }
        }).catch(function () {
            /* Fallback: execCommand */
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            ta.remove();
            if (btn) { btn.textContent = 'Tersalin ✓'; setTimeout(function () { btn.textContent = 'Salin'; }, 2400); }
        });
    };

    /* ═══════════════════════════════════════════
       13. RSVP FORM AJAX
    ═══════════════════════════════════════════ */
    var rsvpForm    = document.getElementById('dr-rsvp-form');
    var rsvpSuccess = document.getElementById('dr-rsvp-success');
    var rowJumlah   = document.getElementById('dr-row-jumlah');

    /* Toggle jumlah visibility based on hadir */
    var hadirInputs = document.querySelectorAll('input[name="hadir"]');
    hadirInputs.forEach(function (inp) {
        inp.addEventListener('change', function () {
            if (rowJumlah) {
                rowJumlah.style.display = (inp.value === '1') ? '' : 'none';
            }
        });
    });

    if (rsvpForm) {
        rsvpForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn  = rsvpForm.querySelector('.dr-submit-btn');
            var data = new FormData(rsvpForm);

            if (btn) { btn.disabled = true; btn.textContent = 'Mengirim…'; }

            fetch(rsvpForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN' : (weddingData.csrfToken || ''),
                    'Accept'       : 'application/json',
                },
                body: data,
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.success || res.message) {
                    rsvpForm.style.display   = 'none';
                    if (rsvpSuccess) {
                        rsvpSuccess.classList.add('show');
                        if (res.message) {
                            var msg = rsvpSuccess.querySelector('.dr-rsvp-msg');
                            if (msg) msg.textContent = res.message;
                        }
                    }
                    /* Append new ucapan to wall if returned */
                    if (res.rsvp) {
                        var list = document.querySelector('.dr-ucapan-list');
                        if (list) {
                            var item = document.createElement('div');
                            item.className = 'dr-ucapan-item';
                            item.innerHTML = '<span class="dr-ucapan-nama">' +
                                escHtml(res.rsvp.guest_name) + '</span>' +
                                '<span class="dr-ucapan-pesan">' +
                                escHtml(res.rsvp.message) + '</span>';
                            list.prepend(item);
                            item.style.animation = 'riseIn3d .7s ease both';
                        }
                    }
                } else {
                    if (btn) {
                        btn.disabled    = false;
                        btn.textContent = 'Kirim';
                    }
                    alert(res.error || 'Terjadi kesalahan, silahkan coba lagi.');
                }
            })
            .catch(function () {
                if (btn) { btn.disabled = false; btn.textContent = 'Kirim'; }
                alert('Terjadi kesalahan koneksi.');
            });
        });
    }

    function escHtml(s) {
        return String(s)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;');
    }

    /* ═══════════════════════════════════════════
       14. PREVIEW AUTO-OPEN
    ═══════════════════════════════════════════ */
    /* In preview mode (isPreview flag injected), auto-open after 1s */
    if (window.weddingData && window.weddingData.isPreview) {
        setTimeout(openInvitation, 1000);
    }

    /* ═══════════════════════════════════════════
       15. GALLERY LIGHTBOX (basic)
    ═══════════════════════════════════════════ */
    document.querySelectorAll('.dr-gallery-item').forEach(function (item) {
        var img = item.querySelector('img');
        if (!img) return;
        item.style.cursor = 'zoom-in';
        item.addEventListener('click', function () {
            var overlay = document.createElement('div');
            overlay.style.cssText = [
                'position:fixed', 'inset:0', 'z-index:9990',
                'background:rgba(8,2,4,.92)',
                'display:flex', 'align-items:center', 'justify-content:center',
                'cursor:zoom-out',
                'animation:fadeIn .3s ease',
                'backdrop-filter:blur(10px)',
            ].join(';');
            var bigImg  = document.createElement('img');
            bigImg.src  = img.src;
            bigImg.style.cssText = [
                'max-width:90vw', 'max-height:88vh',
                'object-fit:contain',
                'border:1px solid rgba(139,20,40,.35)',
                'box-shadow:0 0 80px rgba(139,20,40,.25),0 40px 80px rgba(0,0,0,.7)',
                'animation:riseIn3d .5s ease both',
            ].join(';');
            overlay.appendChild(bigImg);
            overlay.addEventListener('click', function () {
                overlay.style.animation = 'fadeIn .2s ease reverse';
                setTimeout(function () { overlay.remove(); }, 220);
            });
            document.body.appendChild(overlay);
        });
    });

    /* ═══════════════════════════════════════════
       16. DOM-READY GUARD (for any late elements)
    ═══════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {
        /* Ensure all #dr-main children visible if curtain already dismissed */
        if (curtain && curtain.classList.contains('hidden') && mainEl) {
            mainEl.removeAttribute('style');
        }
    });

}());

