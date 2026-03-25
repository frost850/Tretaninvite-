document.addEventListener('DOMContentLoaded', function () {
    // Scroll reveal
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('on'); obs.unobserve(e.target); } });
    }, { threshold: 0.08 });
    document.querySelectorAll('.rv').forEach(el => obs.observe(el));

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
        });
    });

    // Countdown
    const ed = new Date(window.weddingData ? window.weddingData.eventDate : '');
    function tick() {
        const d = ed - new Date();
        if (d <= 0) { ['n-hari','n-jam','n-menit','n-detik'].forEach(id => document.getElementById(id) && (document.getElementById(id).textContent = '00')); return; }
        document.getElementById('n-hari').textContent  = String(Math.floor(d/864e5)).padStart(2,'0');
        document.getElementById('n-jam').textContent   = String(Math.floor(d%864e5/36e5)).padStart(2,'0');
        document.getElementById('n-menit').textContent = String(Math.floor(d%36e5/6e4)).padStart(2,'0');
        document.getElementById('n-detik').textContent = String(Math.floor(d%6e4/1e3)).padStart(2,'0');
    }
    tick(); setInterval(tick, 1000);

    // Toggle jumlah tamu
    document.querySelectorAll('input[name=attendance]').forEach(r => {
        r.addEventListener('change', () => {
            const row = document.getElementById('n-row-jml');
            if (row) row.style.display = r.value === 'tidak_hadir' ? 'none' : '';
        });
    });

    // RSVP submit
    const form = document.getElementById('n-rsvp-form');
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            try {
                const r = await fetch(this.action, { method: 'POST', body: new FormData(this), headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (r.ok) {
                    const data = await r.json();
                    form.style.display = 'none';
                    document.getElementById('n-rsvp-ok').style.display = 'block';
                    if (data.rsvp && data.rsvp.message) {
                        const list = document.querySelector('.ucapan-list');
                        if (list) {
                            const item = document.createElement('div');
                            item.className = 'ucapan-item';
                            const att = data.rsvp.attendance === 'hadir' ? '✓ Hadir' : (data.rsvp.attendance === 'tidak_hadir' ? '✗ Tidak Hadir' : '~ Mungkin');
                            item.innerHTML = `<div><span class="ucapan-nama">${escHtml(data.rsvp.guest_name)}</span> <span class="ucapan-stat">· ${att}</span></div><p class="ucapan-msg">${escHtml(data.rsvp.message)}</p>`;
                            const firstItem = list.querySelector('.ucapan-item');
                            if (firstItem) list.insertBefore(item, firstItem); else list.appendChild(item);
                        }
                    }
                } else { this.submit(); }
            } catch { this.submit(); }
        });
    }
});

function escHtml(t) {
    return String(t || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function mnCopy(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Tersalin!'; btn.classList.add('copied');
        setTimeout(() => { btn.textContent = 'Salin Nomor'; btn.classList.remove('copied'); }, 2000);
    });
}
