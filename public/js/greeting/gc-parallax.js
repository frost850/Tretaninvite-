/**
 * gc-parallax.js
 * Greeting Card — Parallax Effects (scroll + mouse)
 *
 * Elemen yang dibutuhkan:
 *   #gc-px-far    — layer parallax jauh (gerak +cepat)
 *   #gc-px-mid    — layer parallax tengah
 *   #gc-hero      — kontainer hero (untuk mouse parallax)
 *   #gc-hero-name, #gc-salutation, #gc-hero-age, #gc-hero-msg (opsional)
 *
 * Ekspor: window.GcParallax = { init }
 * Panggil GcParallax.init() setelah kartu dibuka.
 */
(function () {
    'use strict';

    function init() {
        const farLayer = document.getElementById('gc-px-far');
        const midLayer = document.getElementById('gc-px-mid');
        const heroName = document.getElementById('gc-hero-name');
        const salute   = document.getElementById('gc-salutation');
        const heroAge  = document.getElementById('gc-hero-age');
        const heroMsg  = document.getElementById('gc-hero-msg');
        let raf = null;

        /* ── Scroll parallax ── */
        window.addEventListener('scroll', () => {
            if (!raf) raf = requestAnimationFrame(() => {
                const sy = window.scrollY;
                if (farLayer) farLayer.style.transform = `translateY(${sy * 0.4}px)`;
                if (midLayer) midLayer.style.transform = `translateY(${sy * 0.25}px)`;
                if (heroName) heroName.style.transform = `translateY(${sy * -0.12}px)`;
                if (salute)   salute.style.transform   = `translateY(${sy * -0.18}px)`;
                if (heroAge)  heroAge.style.transform  = `translateY(${sy * 0.06}px)`;
                if (heroMsg)  heroMsg.style.transform  = `translateY(${sy * 0.08}px)`;
                raf = null;
            });
        }, { passive: true });

        /* ── Mouse parallax di hero ── */
        const hero = document.getElementById('gc-hero');
        if (hero) {
            hero.addEventListener('mousemove', e => {
                const mx = e.clientX / window.innerWidth - 0.5;
                const my = e.clientY / hero.offsetHeight - 0.5;
                const sy = window.scrollY;
                if (farLayer) farLayer.style.transform =
                    `translateY(${sy * 0.4}px) translate(${mx * 20}px, ${my * 12}px)`;
                if (midLayer) midLayer.style.transform =
                    `translateY(${sy * 0.25}px) translate(${mx * 10}px, ${my * 6}px)`;
            });
            hero.addEventListener('mouseleave', () => {
                const sy = window.scrollY;
                if (farLayer) farLayer.style.transform = `translateY(${sy * 0.4}px)`;
                if (midLayer) midLayer.style.transform = `translateY(${sy * 0.25}px)`;
            });
        }
    }

    window.GcParallax = { init };
})();
