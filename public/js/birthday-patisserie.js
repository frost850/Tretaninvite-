// ══════════════════════════════════════════════════════════════
//  🎂 BIRTHDAY PATISSERIE — Interactive Scripts
//  Custom cursor, petal effects, 3D cards, countdown, RSVP
// ══════════════════════════════════════════════════════════════

(function() {
  'use strict';

  function escHtml(t) {
    return String(t || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ══ CURSOR ══
  const cur = document.getElementById('cur');
  const curP = document.getElementById('cur-petal');
  let mx = 0, my = 0;
  const petalEmojis = ['🌸', '🌺', '🌷', '✨', '🍃', '💮'];

  document.addEventListener('mousemove', e => {
    mx = e.clientX;
    my = e.clientY;
    cur.style.left = mx + 'px';
    cur.style.top = my + 'px';
    curP.style.left = mx + 'px';
    curP.style.top = my + 'px';

    // trail petals
    if (Math.random() < 0.12) {
      const t = document.createElement('div');
      t.className = 'trail-petal';
      const ang = Math.random() * Math.PI * 2;
      const dist = 20 + Math.random() * 40;
      t.style.cssText = `left:${mx}px;top:${my}px;--dx:${Math.cos(ang) * dist}px;--dy:${Math.sin(ang) * dist}px;--dr:${(Math.random() - 0.5) * 180}deg;animation-duration:${0.6 + Math.random() * 0.5}s`;
      t.textContent = petalEmojis[~~(Math.random() * petalEmojis.length)];
      document.body.appendChild(t);
      setTimeout(() => t.remove(), 1100);
    }
  });

  // Cursor hover effects
  document.querySelectorAll('button,a,[onclick]').forEach(el => {
    el.addEventListener('mouseenter', () => {
      cur.style.width = '14px';
      cur.style.height = '14px';
      cur.style.background = 'var(--gold)';
      curP.style.borderColor = 'rgba(201,168,76,.5)';
    });
    el.addEventListener('mouseleave', () => {
      cur.style.width = '10px';
      cur.style.height = '10px';
      cur.style.background = 'var(--rose)';
      curP.style.borderColor = 'rgba(244,167,185,.4)';
    });
  });

  // ══ COVER TILT ══
  const cc = document.getElementById('cc');
  document.addEventListener('mousemove', e => {
    if (!document.getElementById('cover').classList.contains('gone')) {
      const cx = window.innerWidth / 2;
      const cy = window.innerHeight / 2;
      const ry = (e.clientX - cx) / cx * 8;
      const rx = -(e.clientY - cy) / cy * 5;
      cc.style.transform = `rotateX(${rx + 2}deg) rotateY(${ry}deg)`;
      cc.style.animation = 'none';
    }
  });

  // ══ OPEN INVITATION ══
  window.openInvitation = function() {
    firePetals(60, window.innerWidth / 2, window.innerHeight / 2);
    document.getElementById('cover').classList.add('gone');
    setTimeout(() => {
      document.getElementById('main').classList.add('on');
      document.getElementById('nav').classList.add('on');
      document.getElementById('cover').style.display = 'none';
    }, 900);
  };

  // ══ PETAL BURST ══
  function firePetals(n, x, y) {
    for (let i = 0; i < n; i++) {
      const p = document.createElement('div');
      p.className = 'pb';
      const ang = Math.random() * Math.PI * 2;
      const dist = 50 + Math.random() * 200;
      p.style.cssText = `left:${x}px;top:${y}px;font-size:${0.8 + Math.random() * 1.2}rem;--dx:${Math.cos(ang) * dist}px;--dy:${Math.sin(ang) * dist}px;--dr:${(Math.random() - 0.5) * 540}deg;animation-duration:${0.8 + Math.random() * 0.8}s;animation-delay:${Math.random() * 0.3}s`;
      p.textContent = petalEmojis[~~(Math.random() * petalEmojis.length)];
      document.body.appendChild(p);
      setTimeout(() => p.remove(), 2000);
    }
  }

  // ══ CLICK PETALS ══
  document.addEventListener('click', e => {
    firePetals(8, e.clientX, e.clientY);
  });

  // ══ CAKE 3D ══
  const cs = document.getElementById('cakeScene');
  const c3 = document.getElementById('cake3d');
  if (cs && c3) {
    cs.addEventListener('mousemove', e => {
      const r = cs.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      c3.style.transform = `rotateY(${x * 28}deg) rotateX(${-y * 22}deg)`;
      c3.style.animation = 'none';
      c3.style.transition = 'transform .15s';
    });
    cs.addEventListener('mouseleave', () => {
      c3.style.transform = '';
      c3.style.animation = '';
      c3.style.transition = 'transform .6s ease';
    });
  }

  // ══ INFO CARD 3D ══
  document.querySelectorAll('.info-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      card.style.transform = `translateX(4px) rotateY(${x * 8}deg) rotateX(${-y * 5}deg) scale(1.01)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });

  // ══ MENU CARD 3D ══
  document.querySelectorAll('.menu-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      card.style.transform = `translateY(-8px) rotateY(${x * 10}deg) rotateX(${-y * 8}deg)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });

  // ══ GALLERY 3D ══
  document.querySelectorAll('.gf').forEach(g => {
    g.addEventListener('mousemove', e => {
      const r = g.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      g.style.transform = `scale(1.04) translateY(-5px) rotateY(${x * 10}deg) rotateX(${-y * 8}deg)`;
      g.style.transition = 'transform .1s';
    });
    g.addEventListener('mouseleave', () => {
      g.style.transform = '';
      g.style.transition = 'transform .4s cubic-bezier(.34,1.4,.64,1)';
    });
  });

  // ══ COUNTDOWN ══
  function tick() {
    if (!window.eventTimestamp) return;
    
    const t = new Date(window.eventTimestamp);
    let diff = t - new Date();
    if (diff < 0) diff = 0;

    const dEl = document.getElementById('cd-d');
    const hEl = document.getElementById('cd-h');
    const mEl = document.getElementById('cd-m');
    const sEl = document.getElementById('cd-s');

    if (dEl) dEl.textContent = String(Math.floor(diff / 86400000)).padStart(2, '0');
    if (hEl) hEl.textContent = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
    if (mEl) mEl.textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
    if (sEl) sEl.textContent = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
  }
  if (window.eventTimestamp) {
    setInterval(tick, 1000);
    tick();
  }

  // ══ ATTEND SELECTION ══
  window.pickA = function(el) {
    document.querySelectorAll('.att').forEach(a => a.classList.remove('on'));
    el.classList.add('on');
    const attendanceInput = document.querySelector('input[name="attendance"]');
    if (attendanceInput) {
      attendanceInput.value = el.getAttribute('data-value');
    }
  };

  // ══ RSVP FORM SUBMISSION ══
  window.handleRsvp = function(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    
    const data = {
      guest_name: formData.get('name'),
      phone: formData.get('phone'),
      attendance: formData.get('attendance'),
      guests_count: formData.get('guests'),
      message: formData.get('message'),
    };

    // Visual feedback
    const btn = form.querySelector('.send-btn');
    const originalText = btn.textContent;
    btn.textContent = 'Mengirim... 🌸';
    btn.disabled = true;

    // Submit RSVP
    fetch(`/${window.weddingSlug}/rsvp`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify(data)
    })
    .then(async response => {
      const text = await response.text();
      try { return JSON.parse(text); }
      catch(e) { throw new Error('Server ' + response.status + ': ' + text.substring(0, 150)); }
    })
    .then(result => {
      if (result.success) {
        // Sembunyikan form, tampilkan thank-you permanen
        form.style.display = 'none';
        const okDiv = document.getElementById('rsvp-ok');
        if (okDiv) okDiv.style.display = 'block';

        firePetals(80, window.innerWidth / 2, window.innerHeight * 0.5);

        if (window.weddingPackage === 'premium') {
          // ── Premium: inject ucapan langsung ke wish list ──
          const wl = document.getElementById('wl');
          if (wl && data.message && data.message.trim()) {
            // Hapus placeholder "Jadilah yang pertama" jika ada
            const items = wl.querySelectorAll('.wish');
            if (items.length === 1 && items[0].querySelector('.w-name')?.textContent.includes('Jadilah')) {
              items[0].remove();
            }
            const w = document.createElement('div');
            w.className = 'wish'; // CSS .wish sudah ada animation wishIn + opacity:0→1
            w.innerHTML = `<div class="w-name">✦ ${escHtml(data.guest_name)}</div><div class="w-txt">"${escHtml(data.message)}"</div>`;
            wl.insertBefore(w, wl.firstChild);
            // Pastikan wish-list terlihat (kalau display:none karena kosong sebelumnya)
            wl.style.display = '';
            setTimeout(() => wl.scrollIntoView({ behavior: 'smooth', block: 'start' }), 300);
          }
        } else {
          // ── Basic: toast singkat konfirmasi pribadi ──
          if (data.message && data.message.trim()) {
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.75);color:#fff;padding:12px 20px;border-radius:50px;font-size:.8rem;z-index:9999;backdrop-filter:blur(8px);transition:opacity .4s;max-width:280px;text-align:center;';
            toast.textContent = '✦ Ucapanmu sudah terkirim! Terima kasih 🌸';
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 3500);
          }
        }
      } else {
        throw new Error(result.message || 'Failed to submit RSVP');
      }
    })
    .catch(error => {
      console.error('RSVP error:', error);
      btn.textContent = 'Gagal mengirim 😢';
      btn.style.background = 'linear-gradient(135deg,#e88888,#d87070)';
      btn.disabled = false;
      setTimeout(() => {
        btn.textContent = originalText;
        btn.style.background = '';
      }, 4000);
    });

    return false;
  };

  // ══ SCROLL REVEAL ══
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) e.target.classList.add('on');
    });
  }, { threshold: 0.08 });
  
  document.querySelectorAll('.rev').forEach(el => obs.observe(el));

  // ══ SMOOTH SCROLL ══
  document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

// ══ PHOTO BACKGROUND SLIDESHOW (Premium / VIP) ══
  (function initPhotoBg() {
    var photos = window.bgPhotos;
    if (!photos || !photos.length) return;

    document.body.classList.add('has-photo-bg');

    // ── Cover: cinematic blurred photo ──
    var coverBg = document.getElementById('cover-photo-bg');
    if (coverBg && photos[0]) {
      coverBg.style.backgroundImage = "url('" + photos[0] + "')";
      var img0 = new Image();
      img0.onload = function() { coverBg.classList.add('loaded'); };
      img0.src = photos[0];
    }

    // ── Fullscreen reel ──
    var reel = document.getElementById('photo-bg-reel');
    if (!reel) return;

    var kbAnims = ['kenBurnsA', 'kenBurnsB', 'kenBurnsC', 'kenBurnsD'];
    var slides = photos.map(function(url, i) {
      var div = document.createElement('div');
      div.className = 'pbr-slide';
      div.style.backgroundImage = "url('" + url + "')";
      div.style.animationName    = kbAnims[i % 4];
      div.style.animationDuration = '9s';
      div.style.animationTimingFunction = 'ease-out';
      div.style.animationFillMode = 'forwards';
      reel.appendChild(div);
      return div;
    });

    // Progress dots
    var dotsEl = document.getElementById('pbr-dots');
    if (dotsEl) {
      slides.forEach(function(_, i) {
        var d = document.createElement('span');
        if (i === 0) d.className = 'on';
        dotsEl.appendChild(d);
      });
    }

    var curIdx = 0;
    slides[0].classList.add('active');

    function showSlide(idx) {
      if (idx === curIdx) return;
      var prev = slides[curIdx];
      curIdx = idx;
      var next = slides[curIdx];

      // Restart Ken Burns on new slide
      next.style.animation = 'none';
      void next.offsetWidth; // reflow
      next.style.animation  = '';
      next.style.animationName          = kbAnims[curIdx % 4];
      next.style.animationDuration      = '9s';
      next.style.animationTimingFunction = 'ease-out';
      next.style.animationFillMode      = 'forwards';

      prev.classList.remove('active');
      prev.classList.add('prev');
      next.classList.add('active');

      if (dotsEl) {
        dotsEl.querySelectorAll('span').forEach(function(d, i) {
          d.classList.toggle('on', i === curIdx);
        });
      }
      setTimeout(function() { prev.classList.remove('prev'); }, 2500);
    }

    // Auto-advance every 7 s
    if (photos.length > 1) {
      setInterval(function() {
        showSlide((curIdx + 1) % slides.length);
      }, 7000);
    }

    // Scroll-triggered: each section shows its assigned photo
    var secObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var idx = parseInt(entry.target.getAttribute('data-ph'), 10) % photos.length;
          showSlide(idx);
        }
      });
    }, { threshold: 0.45 });

    document.querySelectorAll('section[data-ph]').forEach(function(sec) {
      secObserver.observe(sec);
    });
  })();

  // ══ INITIALIZATION ══
  console.log('🎂 Birthday Patisserie initialized');
  
})();
