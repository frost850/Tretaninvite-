/* ════════════════════════════════════════════
   BIRTHDAY GATSBY — JavaScript
   Template: Art Deco / Great Gatsby
   ════════════════════════════════════════════ */

function escHtml(t) {
  return String(t || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ══ THEME TOGGLE ══ */
function toggleTheme() {
  const html = document.documentElement;
  const current = html.getAttribute('data-theme') || 'dark';
  const next = current === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', next);
  localStorage.setItem('gatsby-theme', next);
  const icon = document.getElementById('theme-icon');
  if (icon) icon.textContent = next === 'dark' ? '☀' : '🌙';
}

/* ══ CURSOR ══ */
const cur = document.getElementById('cur');
const co  = document.getElementById('cur-outer');
document.addEventListener('mousemove', e => {
  const mx = e.clientX, my = e.clientY;
  if (cur) { cur.style.left = mx + 'px'; cur.style.top = my + 'px'; }
  if (co)  { co.style.left  = mx + 'px'; co.style.top  = my + 'px'; }
  const sl = document.getElementById('spotlight');
  if (sl) { sl.style.setProperty('--sx', mx + 'px'); sl.style.setProperty('--sy', my + 'px'); }
  if (Math.random() < .18) {
    const t = document.createElement('div');
    t.className = 'gold-trail';
    t.style.cssText = `left:${mx}px;top:${my}px;animation-duration:${.4 + Math.random() * .3}s`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 700);
  }
});
document.querySelectorAll('button,a,[onclick]').forEach(el => {
  el.addEventListener('mouseenter', () => {
    if (cur) { cur.style.width = '10px'; cur.style.height = '10px'; }
    if (co)  { co.style.width = '40px'; co.style.height = '40px'; co.style.borderColor = 'rgba(201,168,76,.8)'; }
  });
  el.addEventListener('mouseleave', () => {
    if (cur) { cur.style.width = '6px'; cur.style.height = '6px'; }
    if (co)  { co.style.width = '28px'; co.style.height = '28px'; co.style.borderColor = 'rgba(201,168,76,.5)'; }
  });
});

/* ══ FILM STRIPS ══ */
const fs = document.getElementById('film-strips');
if (fs) {
  for (let i = 0; i < 20; i++) {
    const s = document.createElement('div');
    s.className = 'film-strip';
    s.style.cssText = `left:${Math.random() * 100}%;height:${100 + Math.random() * 200}px;opacity:${.1 + Math.random() * .2};animation-duration:${3 + Math.random() * 6}s;animation-delay:${Math.random() * 5}s;`;
    fs.appendChild(s);
  }
}

/* ══ COVER TILT ══ */
const cc = document.getElementById('cc');
document.addEventListener('mousemove', e => {
  const cov = document.getElementById('cover');
  if (cov && !cov.classList.contains('gone')) {
    const cx = window.innerWidth / 2, cy = window.innerHeight / 2;
    const ry = (e.clientX - cx) / cx * 8, rx = -(e.clientY - cy) / cy * 5;
    if (cc) { cc.style.transform = `rotateX(${rx + 3}deg) rotateY(${ry}deg)`; cc.style.animation = 'none'; }
  }
});

/* ══ ENTER ══ */
function enterSoiree() {
  for (let i = 0; i < 80; i++) {
    const g = document.createElement('div');
    g.className = 'gb';
    const ang = Math.random() * Math.PI * 2, dist = 60 + Math.random() * 250;
    const sz = [3, 5, 8][~~(Math.random() * 3)];
    const col = ['var(--gold)', 'var(--gold2)', 'var(--gold3)', 'rgba(255,255,255,.6)'][~~(Math.random() * 4)];
    g.style.cssText = `left:${window.innerWidth / 2}px;top:${window.innerHeight / 2}px;width:${sz}px;height:${sz}px;background:${col};box-shadow:0 0 ${sz * 2}px ${col};--dx:${Math.cos(ang) * dist}px;--dy:${Math.sin(ang) * dist}px;animation-duration:${.7 + Math.random() * .7}s;animation-delay:${Math.random() * .3}s;`;
    document.body.appendChild(g);
    setTimeout(() => g.remove(), 2000);
  }
  const cov = document.getElementById('cover');
  if (!cov) return;
  cov.classList.add('flicker');
  setTimeout(() => {
    cov.classList.add('gone');
    setTimeout(() => {
      const mainEl = document.getElementById('main');
      const navEl  = document.getElementById('nav');
      if (mainEl) mainEl.classList.add('on');
      if (navEl)  navEl.classList.add('on');
      cov.style.display = 'none';
      // Show music button if present
      const mb = document.getElementById('wp-music-btn');
      if (mb) mb.style.display = 'flex';
      // Show theme toggle
      const tt = document.getElementById('theme-toggle');
      if (tt) tt.classList.add('on');
    }, 900);
  }, 300);
}

/* ══ CLICK BURST ══ */
document.addEventListener('click', e => {
  for (let i = 0; i < 10; i++) {
    const g = document.createElement('div');
    g.className = 'gb';
    const ang = Math.random() * Math.PI * 2, dist = 30 + Math.random() * 100;
    const sz = [3, 5][~~(Math.random() * 2)];
    g.style.cssText = `left:${e.clientX}px;top:${e.clientY}px;width:${sz}px;height:${sz}px;--dx:${Math.cos(ang) * dist}px;--dy:${Math.sin(ang) * dist}px;animation-duration:${.4 + Math.random() * .4}s;`;
    document.body.appendChild(g);
    setTimeout(() => g.remove(), 1000);
  }
});

/* ══ PORTRAIT 3D ══ */
const ps = document.getElementById('portraitScene');
const p3 = document.getElementById('portrait3d');
if (ps) {
  ps.addEventListener('mousemove', e => {
    const r = ps.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
    if (p3) { p3.style.transform = `rotateY(${x * 28}deg) rotateX(${-y * 22}deg)`; p3.style.animation = 'none'; p3.style.transition = 'transform .1s'; }
  });
  ps.addEventListener('mouseleave', () => {
    if (p3) { p3.style.transform = ''; p3.style.animation = ''; p3.style.transition = 'transform .7s ease'; }
  });
}

/* ══ COUNTDOWN ══ */
if (window.eventTimestamp) {
  (function () {
    const target = new Date(window.eventTimestamp);
    function tick() {
      let diff = target - new Date();
      if (diff < 0) diff = 0;
      const d = document.getElementById('cd-d'),
            h = document.getElementById('cd-h'),
            m = document.getElementById('cd-m'),
            s = document.getElementById('cd-s');
      if (d) d.textContent = String(Math.floor(diff / 86400000)).padStart(2, '0');
      if (h) h.textContent = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
      if (m) m.textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
      if (s) s.textContent = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
    }
    setInterval(tick, 1000);
    tick();
  })();
}

/* ══ GALLERY 3D ══ */
document.querySelectorAll('.pw').forEach(g => {
  g.addEventListener('mousemove', e => {
    const r = g.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
    g.style.transform = `scale(1.04) translateY(-5px) rotateY(${x * 10}deg) rotateX(${-y * 8}deg)`;
    g.style.transition = 'transform .1s';
  });
  g.addEventListener('mouseleave', () => {
    g.style.transform = '';
    g.style.transition = 'transform .4s cubic-bezier(.34,1.4,.64,1)';
  });
});

/* ══ ATTEND ══ */
function pickA(el) {
  document.querySelectorAll('.att').forEach(a => a.classList.remove('on'));
  el.classList.add('on');
}

/* ══ SEND RSVP ══ */
function doSend() {
  const name = document.getElementById('rsvp-name')?.value?.trim();
  const wa   = document.getElementById('rsvp-wa')?.value?.trim();
  const msg  = document.getElementById('rsvp-msg')?.value?.trim();
  const att  = document.querySelector('.att.on')?.dataset?.val || 'hadir';
  const lbl  = document.getElementById('send-lbl');
  const btn  = document.getElementById('send-btn');
  if (!name) { alert('Nama tidak boleh kosong'); return; }

  if (lbl) lbl.textContent = '◆ Sending... ◆';
  if (btn) btn.disabled = true;

  const body = new FormData();
  body.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
  body.append('guest_name', name);
  body.append('phone', wa || '');
  body.append('attendance', att);
  body.append('message', msg || '');
  if (window.guestToken) body.append('token', window.guestToken);

  fetch('/' + window.weddingSlug + '/rsvp', { method: 'POST', body: body })
    .then(r => r.json())
    .then(() => {
      // Burst effect
      for (let i = 0; i < 50; i++) {
        const g = document.createElement('div');
        g.className = 'gb';
        const ang = Math.random() * Math.PI * 2, dist = 50 + Math.random() * 200;
        g.style.cssText = `left:50vw;top:80vh;width:5px;height:5px;--dx:${Math.cos(ang) * dist}px;--dy:${Math.sin(ang) * dist}px;animation-duration:${.7 + Math.random() * .6}s;animation-delay:${Math.random() * .3}s;`;
        document.body.appendChild(g);
        setTimeout(() => g.remove(), 2000);
      }
      const fields = document.getElementById('rsvp-fields');
      const ok     = document.getElementById('rsvp-ok');
      if (fields) fields.style.display = 'none';
      if (ok)     ok.style.display = 'block';
      // Add wish to list
      if (window.weddingPackage === 'premium' && msg) {
        const wl = document.getElementById('wl');
        if (wl) {
          const w = document.createElement('div');
          w.className = 'wish';
          w.innerHTML = `<div class="w-name">◆ ${escHtml(name)}</div><div class="w-txt">"${escHtml(msg)}"</div>`;
          wl.prepend(w);
        }
      }
    })
    .catch(() => {
      if (lbl) lbl.textContent = '◆ Send Telegram ◆';
      if (btn) btn.disabled = false;
      alert('Gagal mengirim. Silakan coba lagi.');
    });
}

/* ══ SCROLL REVEAL ══ */
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('on'); });
}, { threshold: .08 });
document.querySelectorAll('.rev').forEach(el => obs.observe(el));

/* ══ INIT THEME ICON ══ */
(function () {
  const icon = document.getElementById('theme-icon');
  if (icon) {
    const t = localStorage.getItem('gatsby-theme') || 'dark';
    icon.textContent = t === 'dark' ? '☀' : '🌙';
  }
})();
