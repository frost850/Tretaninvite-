// Classic Wedding Invitation Theme - JavaScript

document.addEventListener('DOMContentLoaded', function () {

    // ── Generate Floating Petals ──────────────
    const petalsContainer = document.getElementById('petals-container');
    if (petalsContainer) {
        for (let i = 0; i < 9; i++) {
            const petal = document.createElement('div');
            petal.className = 'petal';
            
            const left = Math.floor(Math.random() * 91) + 5; // 5-95
            const duration = Math.floor(Math.random() * 9) + 8; // 8-16
            const delay = Math.floor(Math.random() * 11); // 0-10
            const width = Math.floor(Math.random() * 6) + 8; // 8-13
            const height = Math.floor(Math.random() * 7) + 10; // 10-16
            const bg = i % 2 === 0 ? '#d4a94e' : '#e8cfa0';
            const rotate = Math.floor(Math.random() * 181); // 0-180
            
            petal.style.left = left + 'vw';
            petal.style.animationDuration = duration + 's';
            petal.style.animationDelay = delay + 's';
            petal.style.width = width + 'px';
            petal.style.height = height + 'px';
            petal.style.background = bg;
            petal.style.transform = 'rotate(' + rotate + 'deg)';
            
            petalsContainer.appendChild(petal);
        }
    }

    // ── Scroll reveal ──────────────────────────
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('in');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

    // ── Countdown ──────────────────────────────
    // Event date akan di-inject dari Blade melalui window.weddingData
    if (window.weddingData && window.weddingData.eventDate) {
        const target = new Date(window.weddingData.eventDate + 'T08:00:00');
        
        function tick() {
            const diff = target - new Date();
            if (diff <= 0) {
                ['cd-hari','cd-jam','cd-menit','cd-detik'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = '00';
                });
                return;
            }
            const pad = n => String(n).padStart(2, '0');
            
            const hari = document.getElementById('cd-hari');
            const jam = document.getElementById('cd-jam');
            const menit = document.getElementById('cd-menit');
            const detik = document.getElementById('cd-detik');
            
            if (hari) hari.textContent = pad(Math.floor(diff / 86400000));
            if (jam) jam.textContent = pad(Math.floor((diff % 86400000) / 3600000));
            if (menit) menit.textContent = pad(Math.floor((diff % 3600000) / 60000));
            if (detik) detik.textContent = pad(Math.floor((diff % 60000) / 1000));
        }
        
        tick();
        setInterval(tick, 1000);
    }

    // ── Hide "jumlah tamu" when tidak hadir ────
    document.querySelectorAll('input[name="attendance"]').forEach(r => {
        r.addEventListener('change', () => {
            const rowJumlah = document.getElementById('row-jumlah');
            if (rowJumlah) {
                rowJumlah.style.display = r.value === 'tidak_hadir' ? 'none' : '';
            }
        });
    });

    // ── RSVP form submit ───────────────────────
    const form = document.getElementById('rsvp-form');
    const success = document.getElementById('rsvp-success');

    if (form && success) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const data = new FormData(form);

            // CSRF token akan di-inject melalui window.weddingData
            const csrfToken = window.weddingData && window.weddingData.csrfToken 
                ? window.weddingData.csrfToken 
                : '';

            fetch(form.action, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': csrfToken, 
                    'Accept': 'application/json' 
                },
                body: data
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    form.style.display = 'none';
                    success.classList.add('show');

                    // Append new ucapan if message exists
                    if (res.rsvp?.message) {
                        const ucapanList = document.getElementById('ucapan-list');
                        if (ucapanList) {
                            const item = document.createElement('div');
                            item.className = 'ucapan-item';
                            item.innerHTML = `
                                <div>
                                    <span class="ucapan-nama">${escapeHtml(res.rsvp.guest_name)}</span>
                                    <span class="ucapan-hadir">· ✓ Baru</span>
                                </div>
                                <p class="ucapan-pesan">${escapeHtml(res.rsvp.message)}</p>
                            `;
                            ucapanList.prepend(item);
                        }
                    }
                }
            })
            .catch(() => form.submit()); // fallback to normal submit
        });
    }

});

// ── Copy to clipboard ──────────────────────
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Tersalin! ✓';
        btn.style.background = 'var(--gold)';
        btn.style.color = '#fff';
        setTimeout(() => {
            btn.textContent = orig;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    }).catch(() => {
        // Fallback untuk browser lama
        alert('Nomor rekening: ' + text);
    });
}

// ── Helper: Escape HTML untuk keamanan ────
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
