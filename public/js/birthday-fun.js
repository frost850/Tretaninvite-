/* ═══════════════════════════════════════════════════════════
   BIRTHDAY FUN - Interactive JavaScript
   ════════════════════════════════════════════════════════════ */

// Birthday data from Laravel (set this in blade template)
window.birthdayData = window.birthdayData || {
    name: '',
    age: 0,
    date: '',
    timestamp: 0
};

// ══════════════════════════════════════════════════════════
//  CUSTOM CURSOR
// ══════════════════════════════════════════════════════════
function initCursor() {
    if (window.innerWidth <= 768) return; // Skip on mobile

    const cursor = document.querySelector('.bf-cursor');
    const trail = document.querySelector('.bf-cursor-trail');

    if (!cursor || !trail) return;

    let mouseX = 0, mouseY = 0;
    let trailX = 0, trailY = 0;

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        cursor.style.left = mouseX + 'px';
        cursor.style.top = mouseY + 'px';
    });

    function animateTrail() {
        trailX += (mouseX - trailX) * 0.1;
        trailY += (mouseY - trailY) * 0.1;
        trail.style.left = trailX + 'px';
        trail.style.top = trailY + 'px';
        requestAnimationFrame(animateTrail);
    }
    animateTrail();

    // Grow cursor on hover
    const interactives = document.querySelectorAll('a, button, .bf-cover, .bf-attend-opt');
    interactives.forEach(el => {
        el.addEventListener('mouseenter', () => {
            cursor.style.transform = 'translate(-50%, -50%) scale(1.5)';
            trail.style.transform = 'translate(-50%, -50%) scale(1.3)';
        });
        el.addEventListener('mouseleave', () => {
            cursor.style.transform = 'translate(-50%, -50%) scale(1)';
            trail.style.transform = 'translate(-50%, -50%) scale(1)';
        });
    });
}

// ══════════════════════════════════════════════════════════
//  CONFETTI ANIMATION
// ══════════════════════════════════════════════════════════
function createConfetti() {
    const container = document.querySelector('.bf-confetti');
    if (!container) return;

    const colors = ['#ff6b9d', '#ffd93d', '#6bcf7f', '#4facfe', '#a98aff'];
    const shapes = ['circle', 'square'];

    for (let i = 0; i < 50; i++) {
        const confetti = document.createElement('div');
        const shape = shapes[Math.floor(Math.random() * shapes.length)];
        const color = colors[Math.floor(Math.random() * colors.length)];
        const size = Math.random() * 10 + 5;
        const left = Math.random() * 100;
        const duration = Math.random() * 3 + 2;
        const delay = Math.random() * 2;

        confetti.style.position = 'absolute';
        confetti.style.width = size + 'px';
        confetti.style.height = size + 'px';
        confetti.style.backgroundColor = color;
        confetti.style.left = left + '%';
        confetti.style.top = '-20px';
        confetti.style.borderRadius = shape === 'circle' ? '50%' : '0';
        confetti.style.opacity = '0.7';
        confetti.style.animation = `bfConfettiFall ${duration}s linear ${delay}s infinite`;

        container.appendChild(confetti);
    }

    // Add CSS animation
    if (!document.getElementById('bf-confetti-anim')) {
        const style = document.createElement('style');
        style.id = 'bf-confetti-anim';
        style.textContent = `
            @keyframes bfConfettiFall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// ══════════════════════════════════════════════════════════
//  BALLOON ANIMATION
// ══════════════════════════════════════════════════════════
function createBalloons() {
    const container = document.querySelector('.bf-balloons');
    if (!container) return;

    const balloons = ['🎈', '🎀', '🎁'];

    for (let i = 0; i < 15; i++) {
        const balloon = document.createElement('div');
        const emoji = balloons[Math.floor(Math.random() * balloons.length)];
        const left = Math.random() * 100;
        const duration = Math.random() * 5 + 5;
        const delay = Math.random() * 3;
        const size = Math.random() * 20 + 30;

        balloon.textContent = emoji;
        balloon.style.position = 'absolute';
        balloon.style.fontSize = size + 'px';
        balloon.style.left = left + '%';
        balloon.style.bottom = '-50px';
        balloon.style.animation = `bfBalloonRise ${duration}s ease-in ${delay}s infinite`;

        container.appendChild(balloon);
    }

    // Add CSS animation
    if (!document.getElementById('bf-balloon-anim')) {
        const style = document.createElement('style');
        style.id = 'bf-balloon-anim';
        style.textContent = `
            @keyframes bfBalloonRise {
                to {
                    transform: translateY(-110vh) rotate(20deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// ══════════════════════════════════════════════════════════
//  COVER CARD OPEN
// ══════════════════════════════════════════════════════════
function bfOpen() {
    const cover = document.querySelector('.bf-cover');
    const main = document.querySelector('.bf-main');
    const nav = document.querySelector('.bf-nav');

    if (cover) {
        cover.classList.add('bf-hidden');
        setTimeout(() => {
            cover.style.display = 'none';
        }, 500);
    }

    if (main) {
        main.classList.add('bf-visible');
    }

    if (nav) {
        setTimeout(() => {
            nav.classList.add('bf-visible');
        }, 800);
    }

    // Start animations
    initReveal();
    startCountdown();
}

// ══════════════════════════════════════════════════════════
//  REVEAL ON SCROLL
// ══════════════════════════════════════════════════════════
function initReveal() {
    const reveals = document.querySelectorAll('.bf-rv');

    function checkReveal() {
        reveals.forEach(el => {
            const rect = el.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight * 0.85;
            if (isVisible) {
                el.classList.add('bf-show');
            }
        });
    }

    window.addEventListener('scroll', checkReveal);
    checkReveal(); // Initial check
}

// ══════════════════════════════════════════════════════════
//  COUNTDOWN TIMER
// ══════════════════════════════════════════════════════════
function startCountdown() {
    const targetTimestamp = window.birthdayData.timestamp;
    if (!targetTimestamp) return;

    const daysEl = document.getElementById('bf-hari');
    const hoursEl = document.getElementById('bf-jam');
    const minsEl = document.getElementById('bf-menit');
    const secsEl = document.getElementById('bf-detik');

    function update() {
        const now = Date.now();
        const diff = targetTimestamp - now;

        if (diff <= 0) {
            if (daysEl) daysEl.textContent = '0';
            if (hoursEl) hoursEl.textContent = '0';
            if (minsEl) minsEl.textContent = '0';
            if (secsEl) secsEl.textContent = '0';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);

        if (daysEl) daysEl.textContent = days;
        if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
        if (minsEl) minsEl.textContent = mins.toString().padStart(2, '0');
        if (secsEl) secsEl.textContent = secs.toString().padStart(2, '0');
    }

    update();
    setInterval(update, 1000);
}

// ══════════════════════════════════════════════════════════
//  RSVP FORM
// ══════════════════════════════════════════════════════════
function bfAttend(status) {
    const opts = document.querySelectorAll('.bf-attend-opt');
    opts.forEach(opt => opt.classList.remove('bf-active'));

    const selected = document.querySelector(`.bf-attend-opt[data-val="${status}"]`);
    if (selected) {
        selected.classList.add('bf-active');
    }

    // Map to value expected by backend
    const hiddenInput = document.getElementById('bf-attendance');
    if (hiddenInput) {
        hiddenInput.value = status; // 'hadir' | 'mungkin' | 'tidak_hadir'
    }
}

// Initialize RSVP form submission
function initRSVPForm() {
    const form = document.getElementById('bf-rsvp-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const action = form.dataset.action;

        try {
            const response = await fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                form.style.display = 'none';
                const okMsg = document.getElementById('bf-rsvp-ok');
                if (okMsg) okMsg.style.display = 'block';

                const msg  = formData.get('message')  || '';
                const name = formData.get('guest_name') || 'Tamu';

                if (window.weddingPackage === 'premium') {
                    // ── Premium: inject ucapan ke wish list langsung ──
                    if (msg.trim()) {
                        let wishList = document.querySelector('.bf-wishes');
                        if (!wishList) {
                            wishList = document.createElement('div');
                            wishList.className = 'bf-wishes bf-rv';
                            wishList.innerHTML = '<h3 class="bf-wishes-title">💬 Ucapan</h3>';
                            const rsvpSec = document.getElementById('rsvp');
                            if (rsvpSec) rsvpSec.appendChild(wishList);
                        }
                        const bubble = document.createElement('div');
                        bubble.className = 'bf-wish-bubble';
                        bubble.style.cssText = 'animation:fadeInUp .45s ease both;';
                        bubble.innerHTML = `
                            <div class="bf-wish-header">
                                <span class="bf-wish-name">${name}</span>
                                <span class="bf-wish-status">✅</span>
                            </div>
                            <p class="bf-wish-msg">${msg}</p>`;
                        const title = wishList.querySelector('.bf-wishes-title');
                        if (title) title.after(bubble);
                        else wishList.insertBefore(bubble, wishList.firstChild);
                        setTimeout(() => wishList.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 400);
                    }
                } else {
                    // ── Basic: toast singkat konfirmasi ucapan pribadi ──
                    if (msg.trim()) {
                        const toast = document.createElement('div');
                        toast.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:rgba(20,10,40,.85);color:#fff;padding:12px 22px;border-radius:50px;font-size:.8rem;z-index:9999;backdrop-filter:blur(10px);transition:opacity .4s;max-width:290px;text-align:center;border:1px solid rgba(255,255,255,.12);';
                        toast.textContent = '🎉 Ucapanmu sudah terkirim! Terima kasih';
                        document.body.appendChild(toast);
                        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 3500);
                    }
                }
            } else {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        } catch (error) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}

// ══════════════════════════════════════════════════════════
//  SMOOTH SCROLL
// ══════════════════════════════════════════════════════════
function initSmoothScroll() {
    const links = document.querySelectorAll('.bf-nav a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                const offset = 80; // Account for fixed nav
                const targetPos = target.getBoundingClientRect().top + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: targetPos,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// ══════════════════════════════════════════════════════════
//  COPY HELPER
// ══════════════════════════════════════════════════════════
function bfCopy(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            alert('📋 Tersalin!');
        });
    } else {
        // Fallback for older browsers
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        alert('📋 Tersalin!');
    }
}

// ══════════════════════════════════════════════════════════
//  3D CARD TILT EFFECT
// ══════════════════════════════════════════════════════════
function init3DCard() {
    const card = document.querySelector('.bf-card-3d');
    if (!card) return;

    const scene = card.closest('.bf-scene');
    if (!scene) return;

    scene.addEventListener('mousemove', (e) => {
        const rect = scene.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;

        card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });

    scene.addEventListener('mouseleave', () => {
        card.style.transform = 'rotateX(0) rotateY(0)';
    });
}

// ══════════════════════════════════════════════════════════
//  NAVIGATION HIDE/SHOW ON SCROLL
// ══════════════════════════════════════════════════════════
function initNavScroll() {
    const nav = document.querySelector('.bf-nav');
    if (!nav) return;

    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll <= 100) {
            nav.style.transform = 'translateX(-50%) translateY(0)';
        } else if (currentScroll > lastScroll && currentScroll > 200) {
            // Scrolling down
            nav.style.transform = 'translateX(-50%) translateY(-100px)';
        } else {
            // Scrolling up
            nav.style.transform = 'translateX(-50%) translateY(0)';
        }

        lastScroll = currentScroll;
    });
}

// ══════════════════════════════════════════════════════════
//  GALLERY LIGHTBOX (Simple)
// ══════════════════════════════════════════════════════════
function initGallery() {
    const items = document.querySelectorAll('.bf-gallery-item img');
    
    items.forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', () => {
            // Create simple lightbox
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.9);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            `;

            const imgClone = document.createElement('img');
            imgClone.src = img.src;
            imgClone.style.cssText = `
                max-width: 90%;
                max-height: 90vh;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            `;

            overlay.appendChild(imgClone);
            document.body.appendChild(overlay);

            overlay.addEventListener('click', () => {
                document.body.removeChild(overlay);
            });
        });
    });
}

// ══════════════════════════════════════════════════════════
//  INITIALIZE EVERYTHING
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    console.log('🎉 Birthday Fun Template Loaded!');
    
    // Initialize components
    createConfetti();
    createBalloons();
    initCursor();
    initSmoothScroll();
    init3DCard();
    initNavScroll();
    initGallery();
    initRSVPForm();

    // Note: bfOpen() is called manually when user clicks cover
    // Note: startCountdown() is called after cover opens
    // Note: initReveal() is called after cover opens
});

// ══════════════════════════════════════════════════════════
//  EXPORT FUNCTIONS FOR BLADE TEMPLATE
// ══════════════════════════════════════════════════════════
window.bfOpen = bfOpen;
window.bfAttend = bfAttend;
window.bfCopy = bfCopy;
