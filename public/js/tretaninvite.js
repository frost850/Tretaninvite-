/**
 * TretanInvite — Shared Design System JS
 * Import: <script src="/js/tretaninvite.js"></script>
 *
 * Exposes: window.TI  (utility functions)
 *          window.heroApp()  (Alpine.js app factory for welcome page)
 */
(function () {
    'use strict';

    /* ─────────────────────────────────────────────
       TI.spawnStars(wrapperId, count)
       Populate a fixed-positioned star layer.
       wrapperId defaults to 'ti-stars'
    ───────────────────────────────────────────── */
    function spawnStars(wrapperId, count) {
        wrapperId = wrapperId || 'ti-stars';
        count     = count     || 100;
        var wrap = document.getElementById(wrapperId);
        if (!wrap) return;
        for (var i = 0; i < count; i++) {
            var s   = document.createElement('div');
            s.className = 'star';
            var sz  = Math.random() * 2 + 0.5;
            var dur = (Math.random() * 4 + 2).toFixed(1);
            var del = (Math.random() * 6).toFixed(1);
            s.style.cssText =
                'width:' + sz + 'px;' +
                'height:' + sz + 'px;' +
                'top:' + (Math.random() * 100) + '%;' +
                'left:' + (Math.random() * 100) + '%;' +
                '--d:' + dur + 's;' +
                '--delay:-' + del + 's;';
            wrap.appendChild(s);
        }
    }

    /* ─────────────────────────────────────────────
       TI.startReveal()
       IntersectionObserver scroll-reveal for .reveal elements.
       Call once after DOM is ready.
    ───────────────────────────────────────────── */
    function startReveal() {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e, idx) {
                if (e.isIntersecting) {
                    setTimeout(function () { e.target.classList.add('visible'); }, idx * 65);
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.10 });
        document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    }

    /* ─────────────────────────────────────────────
       TI.initTilt(selector, rotateMax, liftPx)
       3-D mouse-tilt effect on matched cards.
       rotateMax: max degrees (default 8)
       liftPx: translateY on hover (default 6)
    ───────────────────────────────────────────── */
    function initTilt(selector, rotateMax, liftPx) {
        selector  = selector  || '.tmpl-card';
        rotateMax = rotateMax !== undefined ? rotateMax : 8;
        liftPx    = liftPx    !== undefined ? liftPx    : 6;
        document.querySelectorAll(selector).forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width  - 0.5;
                var y = (e.clientY - r.top)  / r.height - 0.5;
                card.style.transform =
                    'perspective(900px) rotateX(' + (-y * rotateMax) + 'deg) ' +
                    'rotateY(' + (x * rotateMax) + 'deg) translateY(-' + liftPx + 'px)';
            });
            card.addEventListener('mouseleave', function () { card.style.transform = ''; });
        });
    }

    /* ─────────────────────────────────────────────
       TI.initFeatCardTilt()
       Same 3-D tilt for .feat-card (stronger effect).
    ───────────────────────────────────────────── */
    function initFeatCardTilt() {
        document.querySelectorAll('.feat-card').forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width  - 0.5;
                var y = (e.clientY - r.top)  / r.height - 0.5;
                card.style.transform =
                    'perspective(800px) rotateX(' + (-y * 12) + 'deg) ' +
                    'rotateY(' + (x * 12) + 'deg) translateY(-8px)';
            });
            card.addEventListener('mouseleave', function () { card.style.transform = ''; });
        });
    }

    /* ─────────────────────────────────────────────
       TI.initPkgCardTilt()
       Same 3-D tilt for .pkg-card.
    ───────────────────────────────────────────── */
    function initPkgCardTilt() {
        document.querySelectorAll('.pkg-card').forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width  - 0.5;
                var y = (e.clientY - r.top)  / r.height - 0.5;
                card.style.transform =
                    'perspective(900px) rotateX(' + (-y * 8) + 'deg) ' +
                    'rotateY(' + (x * 8) + 'deg) translateY(-12px) scale(1.02)';
            });
            card.addEventListener('mouseleave', function () { card.style.transform = ''; });
        });
    }

    /* ─────────────────────────────────────────────
       TI.initHeroParallax(heroSelector)
       Parallax movement for .card-float elements
       on mousemove over the hero section.
    ───────────────────────────────────────────── */
    function initHeroParallax(heroSelector) {
        var hero = document.querySelector(heroSelector || '.bg-hero');
        if (!hero) return;
        hero.addEventListener('mousemove', function (e) {
            var rect = hero.getBoundingClientRect();
            var dx = (e.clientX - rect.left - rect.width  / 2) / rect.width;
            var dy = (e.clientY - rect.top  - rect.height / 2) / rect.height;
            document.querySelectorAll('.card-float').forEach(function (card) {
                var depth = parseFloat(card.dataset.depth || '10');
                card.style.transform = 'translate(' + (dx * depth) + 'px,' + (dy * depth) + 'px)';
            });
        });
        hero.addEventListener('mouseleave', function () {
            document.querySelectorAll('.card-float').forEach(function (card) {
                card.style.transform = '';
            });
        });
    }

    /* ─────────────────────────────────────────────
       TI.watchDOMForTilt(selector, rotate, lift)
       Watch for new DOM nodes (Alpine tab switches)
       and re-apply tilt to newly added cards.
    ───────────────────────────────────────────── */
    function watchDOMForTilt(selector, rotate, lift) {
        var observer = new MutationObserver(function () {
            initTilt(selector, rotate, lift);
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    /* ─────────────────────────────────────────────
       TI.initPage(options)
       One-call convenience — call in DOMContentLoaded
       or Alpine x-init.  options:
         starsId    : id of star wrapper div (default 'ti-stars')
         starsCount : number of stars (default 100)
         tiltSel    : selector for tmpl-card tilt (default '.tmpl-card')
         tiltRotate : max tilt degrees (default 8)
         tiltLift   : lift px (default 6)
         featTilt   : bool initialise feat-card tilt (default false)
         pkgTilt    : bool initialise pkg-card tilt (default false)
         heroParallax: bool initialise hero parallax (default false)
         watchTilt  : bool watch DOM and re-apply tilt (default false)
    ───────────────────────────────────────────── */
    function initPage(options) {
        options = options || {};
        spawnStars(options.starsId, options.starsCount);
        startReveal();
        if (options.tiltSel !== false) {
            initTilt(options.tiltSel, options.tiltRotate, options.tiltLift);
        }
        if (options.featTilt)    initFeatCardTilt();
        if (options.pkgTilt)     initPkgCardTilt();
        if (options.heroParallax) initHeroParallax(options.heroSelector);
        if (options.watchTilt) {
            watchDOMForTilt(options.tiltSel, options.tiltRotate, options.tiltLift);
        }
    }

    /* ─────────────────────────────────────────────
       heroApp()  — Alpine.js x-data factory
       Used by welcome.blade.php  x-data="heroApp()"
    ───────────────────────────────────────────── */
    function heroApp() {
        return {
            init: function () {
                var self = this;
                spawnStars('stars-container', 130);
                startReveal();
                // small delay so Alpine has rendered
                setTimeout(function () {
                    initFeatCardTilt();
                    initPkgCardTilt();
                    initHeroParallax('.bg-hero');
                }, 80);
            }
        };
    }

    /* ─── Expose globally ─── */
    window.TI = {
        spawnStars:       spawnStars,
        startReveal:      startReveal,
        initTilt:         initTilt,
        initFeatCardTilt: initFeatCardTilt,
        initPkgCardTilt:  initPkgCardTilt,
        initHeroParallax: initHeroParallax,
        watchDOMForTilt:  watchDOMForTilt,
        initPage:         initPage,
    };
    window.heroApp = heroApp;

})();
