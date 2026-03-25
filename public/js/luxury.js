/* ── LUXURY TEMPLATE JS ── */

/* ── ENVELOPE OPEN ── */
function openInvitation() {
    var cover = document.getElementById('lx-cover');
    if (!cover) return;
    cover.classList.add('lx-opened');
    setTimeout(function () { cover.style.display = 'none'; }, 900);
    var main = document.getElementById('lx-main');
    if (main) main.classList.add('lx-visible');
    var nav = document.getElementById('lx-nav');
    if (nav) nav.classList.add('lx-visible');
    startPetals();
    initAll();
}

/* ── FLOATING PETALS ── */
function startPetals() {
    var container = document.getElementById('lx-petals');
    if (!container) return;
    var emojis = ['✦', '✧', '⋆', '˚', '✿', '❋'];
    for (var i = 0; i < 18; i++) { spawnPetal(container, emojis, i * 480); }
}
function spawnPetal(container, emojis, delay) {
    var el = document.createElement('span');
    el.classList.add('lx-petal');
    el.textContent = emojis[Math.floor(Math.random() * emojis.length)];
    var duration = 8 + Math.random() * 12;
    el.style.cssText = [
        'left:' + (Math.random() * 100) + 'vw',
        'font-size:' + (.6 + Math.random() * .9) + 'rem',
        'animation-duration:' + duration + 's',
        'animation-delay:' + (delay / 1000) + 's',
        'color:rgba(201,168,76,' + (.25 + Math.random() * .4) + ')'
    ].join(';');
    container.appendChild(el);
}

/* ── INIT ALL (after open) ── */
function initAll() {
    initReveal();
    initCountdown();
    initHeroTilt();
    initCardTilt();
    initRsvp();
}

/* ── SCROLL REVEAL ── */
function initReveal() {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.lx-rv').forEach(function (el) { el.classList.add('lx-on'); });
        return;
    }
    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('lx-on'); obs.unobserve(e.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.lx-rv').forEach(function (el) { obs.observe(el); });
}

/* ── COUNTDOWN ── */
function initCountdown() {
    var data = window.weddingData || {};
    var eventDate = new Date(data.eventDate || '');
    if (isNaN(eventDate)) return;

    var ids = { hari: 'lx-hari', jam: 'lx-jam', menit: 'lx-menit', detik: 'lx-detik' };
    function tick() {
        var now  = new Date();
        var diff = eventDate - now;
        if (diff <= 0) {
            ['hari','jam','menit','detik'].forEach(function (k) {
                var el = document.getElementById(ids[k]);
                if (el) el.textContent = '00';
            });
            return;
        }
        var s = Math.floor(diff / 1000);
        var m = Math.floor(s / 60); s %= 60;
        var h = Math.floor(m / 60); m %= 60;
        var d = Math.floor(h / 24); h %= 24;
        var vals = { hari: d, jam: h, menit: m, detik: s };
        for (var k in vals) {
            var el = document.getElementById(ids[k]);
            if (el) el.textContent = String(vals[k]).padStart(2, '0');
        }
    }
    tick();
    setInterval(tick, 1000);
}

/* ── HERO 3D TILT ── */
function initHeroTilt() {
    var wrap = document.querySelector('.lx-photo-frame');
    var inner = document.querySelector('.lx-photo-wrap');
    if (!wrap || !inner) return;
    wrap.addEventListener('mousemove', function (e) {
        var r = wrap.getBoundingClientRect();
        var cx = r.left + r.width / 2, cy = r.top + r.height / 2;
        var rx = (e.clientY - cy) / (r.height / 2) * -12;
        var ry = (e.clientX - cx) / (r.width  / 2) * 12;
        inner.style.transform = 'rotateX(' + rx + 'deg) rotateY(' + ry + 'deg) translateY(-8px)';
        inner.style.animation  = 'none';
        inner.style.transition = 'transform .1s ease';
    });
    wrap.addEventListener('mouseleave', function () {
        inner.style.transform  = '';
        inner.style.animation  = 'lxFloat3d 6s ease-in-out infinite';
        inner.style.transition = 'transform .6s ease';
    });
}

/* ── CARD 3D TILT ── */
function initCardTilt() {
    document.querySelectorAll('.lx-card').forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
            var r = card.getBoundingClientRect();
            var cx = r.left + r.width / 2, cy = r.top + r.height / 2;
            var rx = (e.clientY - cy) / (r.height / 2) * -10;
            var ry = (e.clientX - cx) / (r.width  / 2) * 10;
            card.style.transform = 'rotateX(' + rx + 'deg) rotateY(' + ry + 'deg) translateY(-8px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
        });
    });
}

/* ── toggleMusic is provided by music-player.js ── */

/* ── COPY REKENING ── */
function lxCopy(text, btnEl) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () { setCopied(btnEl); });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text; document.body.appendChild(ta);
        ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
        setCopied(btnEl);
    }
}
function setCopied(btn) {
    if (!btn) return;
    var orig = btn.textContent;
    btn.textContent = 'Tersalin ✓';
    btn.classList.add('copied');
    setTimeout(function () { btn.textContent = orig; btn.classList.remove('copied'); }, 2200);
}

/* ── RSVP FORM ── */
function initRsvp() {
    var form = document.getElementById('lx-rsvp-form');
    if (!form) return;
    var jadirRadios = document.querySelectorAll('input[name="lx_hadir"]');
    var rowJml      = document.getElementById('lx-row-jml');
    jadirRadios.forEach(function (r) {
        r.addEventListener('change', function () {
            if (rowJml) rowJml.style.display = (r.value === 'hadir') ? 'block' : 'none';
        });
    });
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('.lx-submit');
        if (btn) { btn.disabled = true; btn.textContent = 'Mengirim...'; }
        var hadir = document.querySelector('input[name="lx_hadir"]:checked');
        var payload = {
            guest_name:   (document.getElementById('lx-nama')  || {}).value || '',
            message:      (document.getElementById('lx-pesan') || {}).value || '',
            attendance:   hadir ? hadir.value : 'mungkin',
            guests_count: parseInt((document.getElementById('lx-jml') || {}).value || '1', 10),
            _token:       (document.querySelector('meta[name=csrf-token]') || {}).content || ''
        };
        var action = form.getAttribute('data-action') || '';
        fetch(action, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            var ok = document.getElementById('lx-rsvp-ok');
            if (ok)   { ok.style.display = 'block'; }
            if (form) { form.style.display = 'none'; }
            // Append ucapan baru ke list secara realtime
            if (res && res.rsvp && res.rsvp.message) {
                var list = document.querySelector('.lx-ucapan-list');
                if (!list) {
                    list = document.createElement('div');
                    list.className = 'lx-ucapan-list lx-rv';
                    list.style.marginTop = '56px';
                    var sec = document.querySelector('.lx-rsvp-inner');
                    if (sec) sec.appendChild(list);
                }
                var item = document.createElement('div');
                item.className = 'lx-ucapan-item';
                var attIcon = res.rsvp.attendance === 'hadir' ? '\u2713 Hadir' : (res.rsvp.attendance === 'tidak_hadir' ? '\u2717 Tidak Hadir' : '~ Mungkin');
                item.innerHTML = '<span class="lx-ucapan-nama">' + escHtml(res.rsvp.guest_name) + '</span> <span class="lx-ucapan-stat">' + attIcon + '</span><p class="lx-ucapan-msg">' + escHtml(res.rsvp.message) + '</p>';
                var firstItem = list.querySelector('.lx-ucapan-item');
                if (firstItem) list.insertBefore(item, firstItem);
                else list.appendChild(item);
            }
        })
        .catch(function () {
            if (btn) { btn.disabled = false; btn.textContent = 'Kirim Ucapan'; }
            alert('Gagal mengirim. Silakan coba lagi.');
        });
    });
}

function escHtml(t) {
    return String(t || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
