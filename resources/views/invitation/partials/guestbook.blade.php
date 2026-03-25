{{--
    Shared Live Guestbook — tampil untuk premium & VIP (selain vip-patisserie & vip-royal yang punya versi sendiri).
    Variabel yang dibutuhkan dari view parent:
        $wedding           — Wedding model
        $guest             — Guest|null
        $guestbookEntries  — Collection<Guestbook>
        $isDemo            — bool (opsional, default false)
--}}
@php
    $isDemo     = $isDemo ?? false;
    $gbEntries  = $guestbookEntries ?? collect();
    $canShowGb  = !$isDemo && ($wedding->isPremium() || ($wedding->isVip() && ($wedding->guestbook_enabled ?? false)));
@endphp
@if($canShowGb)
<style>
/* ── Shared Guestbook Styles ── */
.gb-sec {
    padding: 72px 24px;
    text-align: center;
    position: relative;
    max-width: 640px;
    margin: 0 auto;
}
.gb-pill {
    display: inline-block;
    font-size: .6rem;
    letter-spacing: .3em;
    text-transform: uppercase;
    padding: 6px 18px;
    border-radius: 999px;
    margin-bottom: 20px;
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.7);
    border: 1px solid rgba(255,255,255,.12);
}
.gb-title {
    font-size: clamp(1.6rem, 5vw, 2.4rem);
    font-weight: 700;
    color: #fff;
    margin: 0 0 10px;
    line-height: 1.2;
}
.gb-sub {
    font-size: .85rem;
    color: rgba(255,255,255,.5);
    max-width: 420px;
    margin: 0 auto 36px;
    line-height: 1.8;
}
.gb-counter {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 32px;
    color: rgba(255,255,255,.5);
    font-size: .78rem;
}
.gb-counter-num {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}
.gb-form {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 20px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    text-align: left;
    max-width: 520px;
    margin: 0 auto 16px;
}
.gb-name-row {
    display: flex;
    align-items: center;
    gap: 12px;
}
.gb-av {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255,255,255,.2), rgba(255,255,255,.08));
    border: 1px solid rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; font-weight: 700; color: rgba(255,255,255,.9);
    flex-shrink: 0;
}
.gb-form input[type="text"],
.gb-form textarea {
    flex: 1;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 12px;
    padding: 10px 14px;
    color: #fff;
    font-size: .85rem;
    outline: none;
    font-family: inherit;
    transition: border-color .2s;
    width: 100%;
    box-sizing: border-box;
}
.gb-form textarea { resize: none; }
.gb-form input[type="text"]:focus,
.gb-form textarea:focus {
    border-color: rgba(255,255,255,.35);
}
.gb-char {
    font-size: .7rem;
    color: rgba(255,255,255,.3);
    text-align: right;
    display: block;
    margin-top: -8px;
}
.gb-submit {
    align-self: center;
    padding: 11px 32px;
    background: linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,.08));
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 999px;
    color: #fff;
    font-size: .7rem;
    letter-spacing: .2em;
    text-transform: uppercase;
    cursor: pointer;
    font-family: inherit;
    font-weight: 600;
    transition: all .25s;
}
.gb-submit:hover { background: rgba(255,255,255,.22); transform: translateY(-1px); }
.gb-submit:disabled { opacity: .5; cursor: not-allowed; transform: none; }
.gb-done {
    text-align: center;
    font-size: .9rem;
    color: rgba(255,255,255,.75);
    padding: 20px;
    display: none;
    max-width: 520px;
    margin: 0 auto;
}
.gb-live-hdr {
    display: flex; align-items: center; justify-content: center;
    gap: 8px;
    font-size: .6rem;
    letter-spacing: .3em;
    text-transform: uppercase;
    color: rgba(255,255,255,.35);
    margin: 40px auto 20px;
    max-width: 520px;
}
.gb-live-dot {
    display: inline-block;
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 6px #4ade80;
    animation: gbPulse .9s ease-in-out infinite;
}
@keyframes gbPulse { 0%,100%{opacity:1} 50%{opacity:.35} }
.gb-new-badge {
    display: none;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 999px;
    padding: 2px 10px;
    font-size: .65rem;
    color: rgba(255,255,255,.7);
    cursor: pointer;
    text-transform: none;
    letter-spacing: 0;
}
.gb-list {
    max-width: 520px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.gb-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    padding: 14px 16px;
    text-align: left;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .4s, transform .4s;
}
.gb-item.gb-in { opacity: 1; transform: translateY(0); }
.gb-item-av {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,.06));
    border: 1px solid rgba(255,255,255,.12);
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 700; color: rgba(255,255,255,.85);
}
.gb-item-body { flex: 1; min-width: 0; }
.gb-item-name { font-size: .8rem; font-weight: 700; color: rgba(255,255,255,.9); margin-bottom: 4px; }
.gb-item-msg { font-size: .82rem; color: rgba(255,255,255,.6); line-height: 1.6; word-break: break-word; }
.gb-item-time { font-size: .7rem; color: rgba(255,255,255,.3); margin-top: 6px; }
.gb-empty {
    font-size: .85rem;
    color: rgba(255,255,255,.35);
    padding: 32px;
    border: 1px dashed rgba(255,255,255,.1);
    border-radius: 14px;
    max-width: 520px;
    margin: 0 auto;
}
</style>

<section class="gb-sec" id="ucapan" style="width:100%;">
    <div>
        <span class="gb-pill">💌 Titip Ucapan</span>
    </div>
    <h2 class="gb-title">Ucapan &amp; Doa</h2>
    <p class="gb-sub">Tulis ucapan terbaik Anda — semua tamu yang hadir dapat membacanya bersama</p>

    <div class="gb-counter">
        <span class="gb-counter-num" id="gb-count">{{ $gbEntries->count() }}</span>
        <span>ucapan telah dikirim</span>
    </div>

    <form id="gb-form" class="gb-form"
          data-action="{{ url('/' . $wedding->slug . '/guestbook') }}">
        @csrf
        <div class="gb-name-row">
            <div class="gb-av" id="gb-av">{{ strtoupper(mb_substr($guest->guest_name ?? 'T', 0, 1)) }}</div>
            <input type="text" name="name" id="gb-name" placeholder="Nama Anda" maxlength="100" required
                   value="{{ $guest->guest_name ?? '' }}"{{ !empty($guest->guest_name) ? ' readonly' : '' }}>
        </div>
        <div>
            <textarea name="message" id="gb-msg" rows="3" placeholder="Tulis ucapan &amp; doa terbaik Anda…" maxlength="220" required></textarea>
            <span class="gb-char" id="gb-char">0/220</span>
        </div>
        <button type="submit" class="gb-submit">Kirim Ucapan 💌</button>
    </form>

    <div id="gb-done" class="gb-done">
        💌 Terima kasih, {{ $guest->guest_name ?? 'Anda' }}! Ucapan telah kami terima.
    </div>

    <div class="gb-live-hdr">
        <span class="gb-live-dot"></span>
        Live &middot; Ucapan Para Tamu
        <span id="gb-new-badge" class="gb-new-badge"></span>
    </div>

    <div class="gb-list" id="gb-list">
        @forelse($gbEntries->take(50) as $entry)
        @php $init = strtoupper(mb_substr($entry->name, 0, 1)); @endphp
        <div class="gb-item gb-in" data-id="{{ $entry->id }}">
            <div class="gb-item-av">{{ $init }}</div>
            <div class="gb-item-body">
                <div class="gb-item-name">{{ $entry->name }}</div>
                <div class="gb-item-msg">{{ $entry->message }}</div>
                <div class="gb-item-time">{{ $entry->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="gb-empty" id="gb-empty">Jadilah yang pertama mengucapkan selamat 💌</div>
        @endforelse
    </div>
</section>

<script>
(function(){
    var gbUrl  = '{{ url("/" . $wedding->slug . "/guestbook") }}';
    var gbLastId = {{ $gbEntries->isNotEmpty() ? $gbEntries->max('id') : 0 }};
    var gbTotal  = {{ $gbEntries->count() }};
    var gbSent   = false;

    function setCount(n){ var el=document.getElementById('gb-count'); if(el) el.textContent=n; }

    function renderEntry(e, prepend){
        var list = document.getElementById('gb-list');
        if(!list) return;
        var emEl = document.getElementById('gb-empty');
        if(emEl) emEl.remove();
        var init = (e.name||'?').charAt(0).toUpperCase();
        var diff = e.created_at ? 'baru saja' : '';
        var div = document.createElement('div');
        div.className = 'gb-item';
        div.dataset.id = e.id;
        div.innerHTML =
            '<div class="gb-item-av">'+init+'</div>'+
            '<div class="gb-item-body">'+
                '<div class="gb-item-name">'+escHtml(e.name)+'</div>'+
                '<div class="gb-item-msg">'+escHtml(e.message)+'</div>'+
                '<div class="gb-item-time">'+diff+'</div>'+
            '</div>';
        if(prepend && list.firstChild) list.insertBefore(div, list.firstChild);
        else list.appendChild(div);
        requestAnimationFrame(function(){ requestAnimationFrame(function(){ div.classList.add('gb-in'); }); });
    }

    function escHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    /* char counter */
    var msgEl = document.getElementById('gb-msg');
    var charEl = document.getElementById('gb-char');
    if(msgEl && charEl){
        msgEl.addEventListener('input', function(){
            charEl.textContent = msgEl.value.length + '/220';
        });
    }

    /* avatar initial */
    var nameEl = document.getElementById('gb-name');
    var avEl   = document.getElementById('gb-av');
    if(nameEl && avEl && !nameEl.readOnly){
        nameEl.addEventListener('input', function(){
            avEl.textContent = (nameEl.value||'T').charAt(0).toUpperCase();
        });
    }

    /* form submit */
    var form = document.getElementById('gb-form');
    var done = document.getElementById('gb-done');
    if(form){
        form.addEventListener('submit', function(e){
            e.preventDefault();
            if(gbSent) return;
            gbSent = true;
            var btn = form.querySelector('.gb-submit');
            if(btn){ btn.disabled=true; btn.textContent='Mengirim…'; }
            var fd = new FormData(form);
            fetch(form.dataset.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': fd.get('_token') },
                body: fd,
            })
            .then(function(r){ if(!r.ok) throw new Error(); return r.json(); })
            .then(function(data){
                form.style.display = 'none';
                if(done) done.style.display = 'block';
                if(data.entry){
                    renderEntry(data.entry, true);
                    gbLastId = Math.max(gbLastId, data.entry.id);
                    gbTotal++;
                    setCount(gbTotal);
                }
            })
            .catch(function(){
                form.style.display = 'none';
                if(done) done.style.display = 'block';
            });
        });
    }

    /* polling */
    var gbNewCount = 0;
    function gbPoll(){
        fetch(gbUrl + '?after=' + gbLastId, { headers: { 'Accept': 'application/json' } })
        .then(function(r){ return r.ok ? r.json() : []; })
        .then(function(entries){
            if(!Array.isArray(entries) || !entries.length) return;
            entries.sort(function(a,b){ return a.id - b.id; });
            entries.forEach(function(entry){
                renderEntry(entry, true);
                gbLastId = Math.max(gbLastId, entry.id);
                gbTotal++;
            });
            setCount(gbTotal);
            if(!gbSent){
                gbNewCount += entries.length;
                var badge = document.getElementById('gb-new-badge');
                if(badge){
                    badge.textContent = '+' + gbNewCount + ' ucapan baru';
                    badge.style.display = 'inline-block';
                    badge.onclick = function(){
                        badge.style.display = 'none'; gbNewCount = 0;
                        document.getElementById('gb-list').scrollIntoView({ behavior: 'smooth' });
                    };
                }
            }
        })
        .catch(function(){});
    }
    setTimeout(function(){ gbPoll(); setInterval(gbPoll, 8000); }, 10000);
})();
</script>
@endif
