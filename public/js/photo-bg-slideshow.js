/* Photo Background Slideshow — Universal standalone script
   Reads window.bgPhotos (array of URLs) and creates a Ken Burns
   fullscreen reel with optional per-section scroll triggering via
   data-ph="N" attributes on <section> elements.
   Requires: #photo-bg-reel and #pbr-dots elements in the DOM.
   Optional: #cover-photo-bg element in the cover/hero area.
*/
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
    div.style.backgroundImage     = "url('" + url + "')";
    div.style.animationName       = kbAnims[i % 4];
    div.style.animationDuration   = '9s';
    div.style.animationTimingFunction = 'ease-out';
    div.style.animationFillMode   = 'forwards';
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
    curIdx   = idx;
    var next = slides[curIdx];

    // Restart Ken Burns on new slide
    next.style.animation = 'none';
    void next.offsetWidth; // reflow
    next.style.animation               = '';
    next.style.animationName           = kbAnims[curIdx % 4];
    next.style.animationDuration       = '9s';
    next.style.animationTimingFunction = 'ease-out';
    next.style.animationFillMode       = 'forwards';

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

  // Auto-advance every 7 s (when no section triggers)
  if (photos.length > 1) {
    setInterval(function() {
      showSlide((curIdx + 1) % slides.length);
    }, 7000);
  }

  // Scroll-triggered: each section with data-ph="N" shows photo N
  if (typeof IntersectionObserver !== 'undefined') {
    var secObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var idx = parseInt(entry.target.getAttribute('data-ph'), 10) % photos.length;
          showSlide(idx);
        }
      });
    }, { threshold: 0.45 });

    document.querySelectorAll('[data-ph]').forEach(function(sec) {
      secObserver.observe(sec);
    });
  }
})();
