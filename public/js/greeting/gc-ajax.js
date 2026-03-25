/**
 * gc-ajax.js
 * Greeting Card — AJAX: Reactions, Gallery + Lightbox, Wish Form, Wishes List
 *
 * Dependensi:
 *   window.GC_SLUG   — slug halaman (set di blade)
 *   window.GC_DEMO   — boolean, true = preview/demo mode
 *   meta[name="csrf-token"] — CSRF token Laravel
 *
 * Ekspor: window.GcAjax = { boot, loadReactions, loadGallery, loadWishes }
 * boot() dipanggil 1x oleh gc-core.js saat halaman siap.
 * load*() dipanggil oleh gc-core.js setelah kartu dibuka.
 */
(function () {
    'use strict';

    let SLUG = '', DEMO = false, CSRF = '';

    /* ════════════════════════════════════════
       BOOT — inisialisasi dari window vars
    ════════════════════════════════════════ */
    function boot() {
        SLUG = window.GC_SLUG || '';
        DEMO = !!window.GC_DEMO;
        CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

        _initLightboxEvents();
    }

    /* ════════════════════════════════════════
       REACTIONS
    ════════════════════════════════════════ */
    function loadReactions() {
        fetch(`/${SLUG}/gc/reactions`)
            .then(r => r.json())
            .then(data => {
                const reactions   = data.reactions    || {};
                const myReactions = data.my_reactions || [];
                document.querySelectorAll('.gc-react-btn').forEach(btn => {
                    const em = btn.dataset.emoji;
                    const countEl = btn.querySelector('.gc-react-count');
                    if (countEl) countEl.textContent = reactions[em] || 0;
                    if (myReactions.includes(em)) btn.classList.add('gc-reacted');
                });
            })
            .catch(() => {});
    }

    /* Delegasi klik tombol reaksi */
    document.addEventListener('click', e => {
        const btn = e.target.closest('.gc-react-btn');
        if (!btn) return;

        if (DEMO) { _showReactNote('Demo mode — reaksi tidak disimpan 🎉'); return; }

        const emoji   = btn.dataset.emoji;
        const countEl = btn.querySelector('.gc-react-count');
        const active  = btn.classList.contains('gc-reacted');
        const cur     = parseInt(countEl ? countEl.textContent : 0) || 0;

        /* Optimistic UI */
        if (countEl) countEl.textContent = active ? Math.max(0, cur - 1) : cur + 1;
        btn.classList.toggle('gc-reacted');
        btn.animate(
            [
                { transform: 'scale(1)' },
                { transform: 'scale(1.5) rotate(-8deg)' },
                { transform: 'scale(1.2) rotate(5deg)' },
                { transform: 'scale(1)' },
            ],
            { duration: 400, easing: 'ease' }
        );

        fetch(`/${SLUG}/gc/react`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ emoji, action: active ? 'remove' : 'add' }),
        })
            .then(r => r.json())
            .then(d => {
                if (countEl) countEl.textContent = d.count;
                if (!active) _showReactNote('Reaksi terkirim ' + emoji);
            })
            .catch(() => {
                /* Rollback */
                if (countEl) countEl.textContent = cur;
                btn.classList.toggle('gc-reacted');
            });
    });

    function _showReactNote(msg) {
        const el = document.getElementById('gc-react-note');
        if (!el) return;
        el.textContent = msg;
        el.style.opacity = '1';
        clearTimeout(el._timer);
        el._timer = setTimeout(() => { el.style.opacity = '0'; }, 2500);
    }

    /* ════════════════════════════════════════
       GALLERY + LIGHTBOX
    ════════════════════════════════════════ */
    let _lbPhotos = [], _lbIdx = 0;

    function loadGallery() {
        fetch(`/${SLUG}/gc/gallery`)
            .then(r => r.json())
            .then(data => {
                const grid    = document.getElementById('gc-gallery-grid');
                const loading = document.getElementById('gc-gal-loading');
                const section = document.getElementById('gc-gallery-section');
                if (loading) loading.remove();
                if (!data.photos || !data.photos.length) {
                    if (section) section.style.display = 'none';
                    return;
                }
                data.photos.forEach((photo, i) => {
                    const item = document.createElement('div');
                    item.className = 'gc-gallery-item gc-reveal';
                    item.innerHTML =
                        `<img src="${photo.url}" alt="Foto ${i + 1}" loading="lazy">` +
                        `<div class="gc-gallery-overlay"><span class="gc-gal-zoom">🔍</span></div>`;
                    item.addEventListener('click', () => _openLightbox(data.photos, i));
                    grid.appendChild(item);
                });
                if (window._gcRevealObserver) {
                    grid.querySelectorAll('.gc-reveal').forEach(el => window._gcRevealObserver.observe(el));
                }
            })
            .catch(() => {
                const s = document.getElementById('gc-gallery-section');
                if (s) s.style.display = 'none';
            });
    }

    function _openLightbox(photos, idx) {
        _lbPhotos = photos; _lbIdx = idx;
        const lb = document.getElementById('gc-lightbox');
        if (!lb) return;
        lb.style.display = 'flex';
        requestAnimationFrame(() => lb.classList.add('gc-lb--open'));
        _updateLightbox();
        document.body.style.overflow = 'hidden';
    }

    function _updateLightbox() {
        const img = document.getElementById('gc-lb-img');
        const cnt = document.getElementById('gc-lb-counter');
        img.classList.add('gc-lb-img--fade');
        setTimeout(() => {
            img.src = _lbPhotos[_lbIdx].url;
            img.classList.remove('gc-lb-img--fade');
        }, 120);
        if (cnt) cnt.textContent = `${_lbIdx + 1} / ${_lbPhotos.length}`;
    }

    function _closeLightbox() {
        const lb = document.getElementById('gc-lightbox');
        if (!lb) return;
        lb.classList.remove('gc-lb--open');
        setTimeout(() => { lb.style.display = 'none'; }, 300);
        document.body.style.overflow = '';
    }

    /* Public lightbox controls (dipanggil dari onclick HTML) */
    window.gcLbPrev    = () => { _lbIdx = (_lbIdx - 1 + _lbPhotos.length) % _lbPhotos.length; _updateLightbox(); };
    window.gcLbNext    = () => { _lbIdx = (_lbIdx + 1) % _lbPhotos.length; _updateLightbox(); };
    window.gcLbClose   = _closeLightbox;
    window.gcLbBgClick = e => { if (e.target === e.currentTarget) _closeLightbox(); };

    function _initLightboxEvents() {
        /* Keyboard nav */
        document.addEventListener('keydown', e => {
            const lb = document.getElementById('gc-lightbox');
            if (!lb || lb.style.display === 'none') return;
            if (e.key === 'ArrowLeft')  { e.preventDefault(); gcLbPrev(); }
            if (e.key === 'ArrowRight') { e.preventDefault(); gcLbNext(); }
            if (e.key === 'Escape')     { _closeLightbox(); }
        });
        /* Touch swipe */
        const lb = document.getElementById('gc-lightbox');
        if (lb) {
            let _sw = 0;
            lb.addEventListener('touchstart', e => { _sw = e.touches[0].clientX; }, { passive: true });
            lb.addEventListener('touchend',   e => {
                const d = _sw - e.changedTouches[0].clientX;
                if (Math.abs(d) > 50) { d > 0 ? gcLbNext() : gcLbPrev(); }
            });
        }
    }

    function _esc(s) {
        return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    /* ── Public API ── */
    window.GcAjax = { boot, loadReactions, loadGallery };
})();
