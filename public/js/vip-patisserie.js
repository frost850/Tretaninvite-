/**
 * VIP Patisserie — Wedding Template JS
 * Pastel Floral · Artisan · Great Vibes Aesthetic
 * Prefix: vp (VIP Patisserie)
 */
(function () {
  'use strict';

  /* ── Petal canvas ─────────────────────────────────── */
  (function initPetals() {
    var canvas = document.getElementById('vp-petals');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var W, H;
    var PETAL_CHARS = ['🌸', '🌷', '🌺', '🌼', '✿'];
    var count = window.innerWidth < 600 ? 16 : 28;
    var petals = [];

    function resize() {
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    for (var i = 0; i < count; i++) {
      petals.push({
        x: Math.random() * 1200,
        y: Math.random() * -800,
        char: PETAL_CHARS[Math.floor(Math.random() * PETAL_CHARS.length)],
        size: Math.random() * 14 + 8,
        speedX: (Math.random() - .5) * .5,
        speedY: Math.random() * .8 + .25,
        rot: Math.random() * Math.PI * 2,
        rotSpeed: (Math.random() - .5) * .015,
        alpha: Math.random() * .35 + .08,
      });
    }

    function loop() {
      ctx.clearRect(0, 0, W, H);
      for (var j = 0; j < petals.length; j++) {
        var p = petals[j];
        p.x += p.speedX;
        p.y += p.speedY;
        p.rot += p.rotSpeed;
        if (p.y > H + 30) { p.y = -30; p.x = Math.random() * W; }
        ctx.save();
        ctx.globalAlpha = p.alpha;
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rot);
        ctx.font = p.size + 'px serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(p.char, 0, 0);
        ctx.restore();
      }
      requestAnimationFrame(loop);
    }
    loop();
  })();

  /* ── Scroll Reveal ────────────────────────────────── */
  (function initScrollReveal() {
    var els = document.querySelectorAll('.vp-rv');
    if (!els.length) return;
    if (typeof IntersectionObserver === 'undefined') {
      els.forEach(function (el) { el.classList.add('in'); });
      return;
    }
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -48px 0px' });
    els.forEach(function (el) { obs.observe(el); });
  })();

  /* ── Navbar ───────────────────────────────────────── */
  (function initNav() {
    var nav = document.getElementById('vp-nav');
    if (!nav) return;
    var links = nav.querySelectorAll('a[href^="#"]');
    var sections = [];
    links.forEach(function (a) {
      var id = a.getAttribute('href').slice(1);
      var sec = document.getElementById(id);
      if (sec) sections.push({ el: sec, link: a });
    });

    function onScroll() {
      var sy = window.scrollY;
      nav.classList.toggle('shown', sy > 80);
      sections.forEach(function (s) {
        var rect = s.el.getBoundingClientRect();
        s.link.classList.toggle('act', rect.top <= 160 && rect.bottom > 100);
      });
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    links.forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        var id = a.getAttribute('href').slice(1);
        var target = document.getElementById(id);
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  })();

  /* ── Countdown ────────────────────────────────────── */
  (function initCountdown() {
    var hEl = document.getElementById('vp-hari');
    var jEl = document.getElementById('vp-jam');
    var mEl = document.getElementById('vp-menit');
    var dEl = document.getElementById('vp-detik');
    if (!hEl) return;

    var target = window.vpEventDate ? new Date(window.vpEventDate) : null;
    if (!target || isNaN(target)) {
      [hEl, jEl, mEl, dEl].forEach(function (el) { if (el) el.textContent = '--'; });
      return;
    }

    function pad(n) { return n < 10 ? '0' + n : n; }
    function tick() {
      var diff = target - Date.now();
      if (diff <= 0) {
        [hEl, jEl, mEl, dEl].forEach(function (el) { if (el) el.textContent = '00'; });
        return;
      }
      var days  = Math.floor(diff / 86400000);
      var hours = Math.floor((diff % 86400000) / 3600000);
      var mins  = Math.floor((diff % 3600000) / 60000);
      var secs  = Math.floor((diff % 60000) / 1000);
      hEl.textContent = days;
      jEl.textContent = pad(hours);
      mEl.textContent = pad(mins);
      dEl.textContent = pad(secs);
    }
    tick();
    setInterval(tick, 1000);
  })();

  /* ── Music Player ─────────────────────────────────── */
  var vpPlayer = null;
  var vpIsYt   = !!(window.vpYtVideoId && window.vpYtVideoId.length > 0);
  var vpAudio  = null;
  var vpPlaying = false;
  var vpBtn;

  function vpSetBtnState(playing) {
    vpPlaying = playing;
    if (!vpBtn) return;
    vpBtn.textContent = playing ? '♪' : '♩';
    vpBtn.classList.toggle('active', playing);
    vpBtn.title = playing ? 'Hentikan Musik' : 'Putar Musik';
  }

  function vpStartAudio() {
    if (!window.vpMusicUrl) return;
    if (!vpAudio) {
      vpAudio = new Audio(window.vpMusicUrl);
      vpAudio.loop = true;
      vpAudio.volume = .7;
    }
    vpAudio.play().then(function () { vpSetBtnState(true); }).catch(function () {});
  }

  function vpPauseAudio() {
    if (vpAudio) { vpAudio.pause(); }
    vpSetBtnState(false);
  }

  window.vpToggleMusic = function () {
    if (vpIsYt) {
      if (!vpPlayer) return;
      try {
        var st = vpPlayer.getPlayerState();
        if (st === 1) { vpPlayer.pauseVideo(); vpSetBtnState(false); }
        else          { vpPlayer.playVideo();  vpSetBtnState(true);  }
      } catch (e) {}
    } else {
      if (vpPlaying) vpPauseAudio();
      else           vpStartAudio();
    }
  };

  function vpStartMusic() {
    if (vpIsYt) {
      if (vpPlayer) {
        try { vpPlayer.playVideo(); vpSetBtnState(true); } catch (e) {}
      }
    } else if (window.vpMusicUrl) {
      vpStartAudio();
    }
  }

  // YouTube IFrame API
  if (vpIsYt) {
    window.onYouTubeIframeAPIReady = function () {
      var playerEl = document.getElementById('vp-yt-player');
      if (!playerEl) return;
      vpPlayer = new YT.Player('vp-yt-player', {
        videoId: window.vpYtVideoId,
        playerVars: { autoplay: 0, controls: 0, loop: 1, playlist: window.vpYtVideoId, iv_load_policy: 3, modestbranding: 1, playsinline: 1 },
        events: {
          onStateChange: function (e) {
            vpSetBtnState(e.data === YT.PlayerState.PLAYING);
          },
        },
      });
    };
    // Handle race condition: blade loads iframe_api async+defer which may fire
    // before this defer script runs, leaving onYouTubeIframeAPIReady uncalled.
    if (window.YT && window.YT.Player) {
      window.onYouTubeIframeAPIReady(); // YT already fully loaded — init directly
    } else if (!window.YT) {
      // Inject script (external async tag in blade is a parallel attempt — both are safe)
      var ytTag = document.createElement('script');
      ytTag.src = 'https://www.youtube.com/iframe_api';
      document.head.appendChild(ytTag);
    }
    // else: window.YT exists but YT.Player not ready yet — external script still loading,
    // onYouTubeIframeAPIReady will be called when it finishes.
  }

  /* ── Open Invitation ──────────────────────────────── */
  window.vpOpenInvitation = function () {
    var cover = document.getElementById('vp-cover');
    var main  = document.getElementById('vp-main');
    if (!cover || !main) return;
    cover.classList.add('open');
    main.classList.add('visible');
    setTimeout(vpStartMusic, 700);
    window.scrollTo(0, 0);
  };

  /* ── Music button init ────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    vpBtn = document.getElementById('vp-music-btn');
    if (vpBtn) {
      var hasMusicSrc = !!(window.vpMusicUrl || (window.vpYtVideoId && vpIsYt));
      if (hasMusicSrc || window.vpDemoMusicOnly) {
        vpBtn.style.display = 'flex';
        if (window.vpDemoMusicOnly) vpBtn.style.opacity = '.45';
        vpBtn.textContent = '♩';
        vpBtn.addEventListener('click', window.vpToggleMusic);
      }
    }
  });

  /* ── Theme Toggle ─────────────────────────────────── */
  (function initTheme() {
    var KEY   = 'vp-theme';
    var btn   = document.getElementById('vp-theme-btn');
    var saved = localStorage.getItem(KEY);

    function applyTheme(dark) {
      document.body.classList.toggle('vp-dark', dark);
      if (btn) btn.textContent = dark ? '☀️' : '🌙';
    }

    applyTheme(saved === 'dark');
    if (btn) {
      btn.addEventListener('click', function () {
        var isDark = !document.body.classList.contains('vp-dark');
        applyTheme(isDark);
        localStorage.setItem(KEY, isDark ? 'dark' : 'light');
      });
    }
  })();

  /* ── Copy norek ───────────────────────────────────── */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-copy]');
    if (!btn) return;
    var text = btn.dataset.copy;
    if (!text) return;
    navigator.clipboard.writeText(text).then(function () {
      var orig = btn.textContent;
      btn.textContent = '✓ Tersalin!';
      btn.classList.add('copied');
      setTimeout(function () { btn.textContent = orig; btn.classList.remove('copied'); }, 2200);
    }).catch(function () {
      var ta = document.createElement('textarea');
      ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
      document.body.appendChild(ta); ta.select();
      try { document.execCommand('copy'); } catch (er) {}
      document.body.removeChild(ta);
      var orig = btn.textContent;
      btn.textContent = '✓ Tersalin!';
      btn.classList.add('copied');
      setTimeout(function () { btn.textContent = orig; btn.classList.remove('copied'); }, 2200);
    });
  });

  /* ── Guestbook avatar letter ──────────────────────── */
  (function initGbAvatar() {
    var nameInput = document.getElementById('vp-gb-name');
    var avEl      = document.getElementById('vp-gb-av');
    if (!nameInput || !avEl) return;
    nameInput.addEventListener('input', function () {
      var v = nameInput.value.trim();
      avEl.textContent = v ? v[0].toUpperCase() : '?';
    });
  })();

  /* ── Guestbook char counter ───────────────────────── */
  (function initGbChar() {
    var msg  = document.getElementById('vp-gb-msg');
    var cEl  = document.getElementById('vp-gb-char');
    if (!msg || !cEl) return;
    var MAX = 220;
    msg.maxLength = MAX;
    function upd() { cEl.textContent = msg.value.length + '/' + MAX; }
    msg.addEventListener('input', upd);
    upd();
  })();

  /* ── RSVP Form ────────────────────────────────────── */
  (function initRsvp() {
    var form = document.getElementById('vp-rsvp-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn  = form.querySelector('.vp-rsvp-btn');
      var okEl = document.getElementById('vp-rsvp-ok');
      var csrf = (form.querySelector('input[name=_token]') || {}).value || '';
      var data = new FormData(form);

      if (btn) { btn.disabled = true; btn.textContent = '...'; }

      fetch(form.dataset.action || form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: data,
      })
        .then(function (r) { return r.json(); })
        .then(function (json) {
          if (json && (json.success || json.message)) {
            form.style.display = 'none';
            if (okEl) {
              var nameVal   = data.get('guest_name')  || data.get('name')  || '';
              var hadir     = data.get('attendance')   || '';
              var pax       = data.get('pax')          || data.get('guest_count') || '1';
              var sumEl = document.getElementById('vp-rsvp-summary');
              if (sumEl) sumEl.innerHTML =
                '<span style="color:var(--rose-d)">' + (nameVal || 'Tamu') + '</span>' +
                ' &mdash; ' + hadir + ' (' + pax + ' orang)';
              okEl.style.display = 'block';
            }
          } else {
            alert(json && json.message ? json.message : 'Terjadi kesalahan.');
            if (btn) { btn.disabled = false; btn.textContent = 'Kirim RSVP'; }
          }
        })
        .catch(function () {
          alert('Gagal terhubung ke server.');
          if (btn) { btn.disabled = false; btn.textContent = 'Kirim RSVP'; }
        });
    });
  })();

})();
