/* ── CERAH TEMPLATE JS ── */

/* ── CUSTOM CURSOR ── */
(function () {
    var cursor = document.getElementById('ce-cursor');
    var ring   = document.getElementById('ce-cursor-ring');
    if (!cursor || !ring || window.matchMedia('(hover: none)').matches) return;
    var mx = 0, my = 0, rx = 0, ry = 0;
    document.addEventListener('mousemove', function (e) {
        mx = e.clientX; my = e.clientY;
        cursor.style.left = mx + 'px'; cursor.style.top = my + 'px';
    });
    function animRing() {
        rx += (mx - rx) * 0.12; ry += (my - ry) * 0.12;
        ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
        requestAnimationFrame(animRing);
    }
    animRing();
    document.querySelectorAll('button,a,[onclick]').forEach(function (el) {
        el.addEventListener('mouseenter', function () {
            cursor.style.width = '30px'; cursor.style.height = '30px';
            ring.style.width = '60px'; ring.style.height = '60px';
            ring.style.borderColor = 'var(--coral)';
        });
        el.addEventListener('mouseleave', function () {
            cursor.style.width = '20px'; cursor.style.height = '20px';
            ring.style.width = '40px'; ring.style.height = '40px';
            ring.style.borderColor = 'var(--pink)';
        });
    });
})();

/* ── FLOATING BUBBLES ── */
(function () {
    var bg = document.getElementById('ce-bubbles');
    if (!bg) return;
    var colors = ['#ffb3d1','#b3e8f0','#ffd4b8','#c8f0e0','#e8c8f8','#ffd166'];
    for (var i = 0; i < 18; i++) {
        var b = document.createElement('div');
        b.className = 'ce-bubble';
        var s = 40 + Math.random() * 120;
        b.style.cssText = [
            'width:' + s + 'px', 'height:' + s + 'px',
            'left:' + (Math.random() * 100) + '%',
            'bottom:-' + s + 'px',
            'background:' + colors[Math.floor(Math.random() * colors.length)],
            'animation-duration:' + (10 + Math.random() * 14) + 's',
            'animation-delay:' + (Math.random() * 10) + 's'
        ].join(';');
        bg.appendChild(b);
    }
})();

/* ── COVER CARD TILT (mouse only) ── */
(function () {
    var card = document.getElementById('ce-cover-card');
    var cover = document.getElementById('ce-cover');
    if (!card || !cover) return;
    document.addEventListener('mousemove', function (e) {
        if (cover.classList.contains('ce-gone')) return;
        var cx = window.innerWidth / 2, cy = window.innerHeight / 2;
        var rx = (e.clientY - cy) / cy * -6;
        var ry = (e.clientX - cx) / cx * 8;
        card.style.transform = 'rotateX(' + rx + 'deg) rotateY(' + ry + 'deg)';
        card.style.animation = 'none';
    });
})();

/* ── OPEN INVITATION ── */
function ceOpen() {
    ceConfetti();
    var cover = document.getElementById('ce-cover');
    if (cover) cover.classList.add('ce-gone');
    setTimeout(function () {
        var main = document.getElementById('ce-main');
        var nav  = document.getElementById('ce-nav');
        if (main) main.classList.add('ce-on');
        if (nav)  nav.classList.add('ce-on');
        if (cover) cover.style.display = 'none';
        ceInitAll();
    }, 900);
}

/* ── INIT ALL ── */
function ceInitAll() {
    ceReveal();
    ceCountdown();
    ceHeroTilt();
    ceCardTilt();
}

/* ── CONFETTI ── */
function ceConfetti() {
    var shapes = ['🌸','🌺','💕','⭐','🌼','💫','🎊','✨','🌟','🎀'];
    for (var i = 0; i < 50; i++) {
        (function (delay) {
            var c = document.createElement('div');
            c.className = 'ce-confetti';
            c.textContent = shapes[Math.floor(Math.random() * shapes.length)];
            c.style.cssText = [
                'left:' + (Math.random() * 100) + 'vw',
                'top:-20px',
                'font-size:' + (1 + Math.random() * 1.4) + 'rem',
                'animation-duration:' + (2 + Math.random() * 3) + 's',
                'animation-delay:' + delay + 's'
            ].join(';');
            document.body.appendChild(c);
            setTimeout(function () { c.remove(); }, 6000);
        })(Math.random() * 1.5);
    }
}

/* ── SCROLL REVEAL ── */
function ceReveal() {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.ce-rv').forEach(function (el) { el.classList.add('ce-on'); });
        return;
    }
    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('ce-on'); obs.unobserve(e.target); }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.ce-rv').forEach(function (el) { obs.observe(el); });
}

/* ── COUNTDOWN ── */
function ceCountdown() {
    var data = window.weddingData || {};
    var target = new Date(data.eventDate || '');
    if (isNaN(target)) return;
    function tick() {
        var diff = target - new Date();
        if (diff < 0) diff = 0;
        var ids = { 'ce-hari': Math.floor(diff / 86400000), 'ce-jam': Math.floor((diff % 86400000) / 3600000), 'ce-menit': Math.floor((diff % 3600000) / 60000), 'ce-detik': Math.floor((diff % 60000) / 1000) };
        for (var id in ids) {
            var el = document.getElementById(id);
            if (el) el.textContent = String(ids[id]).padStart(2, '0');
        }
    }
    tick();
    setInterval(tick, 1000);
}

/* ── HERO 3D TILT ── */
function ceHeroTilt() {
    var scene = document.getElementById('ce-scene');
    var card  = document.getElementById('ce-3d');
    if (!scene || !card) return;
    scene.addEventListener('mousemove', function (e) {
        var r = scene.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width - 0.5;
        var y = (e.clientY - r.top) / r.height - 0.5;
        card.style.transform = 'rotateY(' + (x * 28) + 'deg) rotateX(' + (-y * 22) + 'deg)';
        card.style.animation = 'none';
    });
    scene.addEventListener('mouseleave', function () {
        card.style.transform = '';
        card.style.animation = '';
    });
}

/* ── PERSON CARD TILT ── */
function ceCardTilt() {
    document.querySelectorAll('.ce-person-card').forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
            var r = card.getBoundingClientRect();
            var x = (e.clientX - r.left) / r.width - 0.5;
            var y = (e.clientY - r.top) / r.height - 0.5;
            card.style.transform = 'translateY(-10px) rotateY(' + (x * 15) + 'deg) rotateX(' + (-y * 12) + 'deg) scale(1.02)';
        });
        card.addEventListener('mouseleave', function () { card.style.transform = ''; });
    });
}

/* ── ATTEND SELECT ── */
function ceAttend(el) {
    document.querySelectorAll('.ce-attend-opt').forEach(function (o) { o.classList.remove('ce-active'); });
    el.classList.add('ce-active');
}

/* ── COPY REKENING ── */
function ceCopy(text, btn) {
    var orig = btn ? btn.textContent : '';
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () { ceCopied(btn, orig); });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta);
        ceCopied(btn, orig);
    }
}
function ceCopied(btn, orig) {
    if (!btn) return;
    btn.textContent = 'Tersalin ✓';
    btn.classList.add('copied');
    setTimeout(function () { btn.textContent = orig; btn.classList.remove('copied'); }, 2200);
}

/* ── RSVP FORM ── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('ce-rsvp-form');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('.ce-submit');
            if (btn) { btn.disabled = true; btn.textContent = 'Mengirim...'; }
            var hadir = document.querySelector('.ce-attend-opt.ce-active');
            var hadirVal = 'mungkin';
            if (hadir) {
                var t = hadir.querySelector('span:last-child');
                var txt = t ? t.textContent.trim().toLowerCase() : '';
                hadirVal = txt === 'hadir' ? 'hadir' : (txt === 'tidak' ? 'tidak_hadir' : 'mungkin');
            }
            var payload = {
                guest_name:   (document.getElementById('ce-nama')  || {}).value || '',
                phone:        (document.getElementById('ce-wa')    || {}).value || '',
                message:      (document.getElementById('ce-pesan') || {}).value || '',
                attendance:   hadirVal,
                guests_count: parseInt((document.getElementById('ce-jml') || {}).value || '1', 10),
                _token: (document.querySelector('meta[name=csrf-token]') || {}).content || ''
            };
            fetch(form.getAttribute('data-action') || '', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': payload._token },
                body: JSON.stringify(payload)
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                var ok = document.getElementById('ce-rsvp-ok');
                if (ok)   ok.style.display = 'block';
                form.style.display = 'none';
                ceConfetti();
                // Append ucapan baru ke list secara realtime
                if (res && res.rsvp && res.rsvp.message) {
                    var list = document.querySelector('.ce-wishes');
                    if (!list) {
                        // Buat section wishes jika belum ada
                        list = document.createElement('div');
                        list.className = 'ce-wishes ce-rv';
                        var title = document.createElement('h3');
                        title.className = 'ce-wishes-title';
                        title.textContent = '\uD83D\uDCAC Ucapan & Doa';
                        list.appendChild(title);
                        var sec = document.querySelector('.ce-rsvp-sec');
                        if (sec) sec.appendChild(list);
                    }
                    var bubble = document.createElement('div');
                    bubble.className = 'ce-wish-bubble';
                    var attIcon = res.rsvp.attendance === 'hadir' ? '\u2705 Hadir' : (res.rsvp.attendance === 'tidak_hadir' ? '\u274C Tidak Hadir' : '\uD83E\uDD14 Mungkin');
                    bubble.innerHTML = '<div class="ce-wish-header"><span class="ce-wish-name">' + escHtml(res.rsvp.guest_name) + '</span><span class="ce-wish-status">' + attIcon + '</span></div><p class="ce-wish-msg">' + escHtml(res.rsvp.message) + '</p>';
                    // Insert setelah title (index 1)
                    var firstBubble = list.querySelector('.ce-wish-bubble');
                    if (firstBubble) list.insertBefore(bubble, firstBubble);
                    else list.appendChild(bubble);
                }
            })
            .catch(function () {
                if (btn) { btn.disabled = false; btn.textContent = 'Kirim Konfirmasi ✨'; }
                alert('Gagal mengirim. Silakan coba lagi.');
            });
        });
    });
})();

function escHtml(t) {
    return String(t || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
