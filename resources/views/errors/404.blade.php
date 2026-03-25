<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 &mdash; Halaman Tidak Ditemukan &ndash; {{ config('app.name', 'TretanInvite') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0b0c10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Ambient background blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.18;
            pointer-events: none;
        }
        .blob-1 { width: 420px; height: 420px; background: #7c3aed; top: -120px; left: -80px; }
        .blob-2 { width: 350px; height: 350px; background: #f59e0b; bottom: -80px; right: -60px; opacity: 0.12; }
        .blob-3 { width: 260px; height: 260px; background: #ec4899; top: 40%; left: 60%; opacity: 0.10; }

        /* Stars */
        .stars { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
        .star {
            position: absolute;
            background: #fff;
            border-radius: 50%;
            animation: twinkle var(--d, 3s) infinite alternate;
            opacity: var(--o, 0.5);
        }
        @keyframes twinkle { from { opacity: var(--o, .5); } to { opacity: calc(var(--o, .5) * .2); } }

        /* Card */
        .card {
            position: relative;
            z-index: 10;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 52px 44px;
            max-width: 480px;
            width: 100%;
            margin: 16px;
            text-align: center;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08);
        }

        /* 404 number */
        .num {
            font-size: 7rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #f59e0b, #ef4444, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: -4px;
        }

        /* Icon */
        .icon-wrap {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, rgba(124,58,237,0.25), rgba(245,158,11,0.15));
            border: 1px solid rgba(245,158,11,0.2);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 24px;
            box-shadow: 0 8px 32px rgba(124,58,237,0.2);
        }

        /* Buttons */
        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 13px 24px; border-radius: 12px;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: #000; font-weight: 700; font-size: 0.9rem;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 4px 20px rgba(245,158,11,0.35);
        }
        .btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 6px 28px rgba(245,158,11,0.45); }

        .btn-ghost {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 12px 24px; border-radius: 12px;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.6); font-weight: 600; font-size: 0.9rem;
            text-decoration: none; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.22); }

        /* Tip box */
        .tip {
            background: rgba(245,158,11,0.08);
            border: 1px solid rgba(245,158,11,0.18);
            border-radius: 12px;
            padding: 14px 16px;
            margin-top: 20px;
            text-align: left;
        }
    </style>
</head>
<body>

    {{-- Stars --}}
    <div class="stars" id="stars"></div>

    {{-- Blobs --}}
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    {{-- Card --}}
    <div class="card">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:32px;">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#ef4444);display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:900;color:#fff;">T</div>
            <span style="font-weight:800;font-size:1.05rem;color:#fff;letter-spacing:-0.3px;"><span style="color:#f59e0b;">Tretan</span>Invite</span>
        </div>

        {{-- Icon --}}
        <div class="icon-wrap">&#x1F50D;</div>

        {{-- 404 --}}
        <div class="num">404</div>

        <h1 style="font-size:1.35rem;font-weight:800;color:#f1f5f9;margin-bottom:8px;">Halaman Tidak Ditemukan</h1>
        <p style="font-size:0.9rem;color:rgba(148,163,184,0.9);line-height:1.6;margin-bottom:28px;">
            Link yang kamu buka tidak ada, sudah dipindah,<br>atau link undangan yang kamu masukkan salah.
        </p>

        {{-- Buttons --}}
        <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ url('/') }}" class="btn-primary">
                &#x2190; Kembali ke Beranda
            </a>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}"
               onclick="history.length > 1 ? (history.back(), event.preventDefault()) : null"
               class="btn-ghost">
                Halaman Sebelumnya
            </a>
        </div>

        {{-- Tip --}}
        <div class="tip">
            <p style="font-size:0.78rem;color:rgba(245,158,11,0.85);font-weight:700;margin-bottom:4px;">&#x1F4A1; Tips:</p>
            <p style="font-size:0.8rem;color:rgba(148,163,184,0.8);line-height:1.55;">
                Jika kamu punya undangan digital, pastikan link sudah benar.
                Link biasanya berupa <code style="background:rgba(255,255,255,0.07);padding:1px 5px;border-radius:4px;font-size:0.75rem;color:#a78bfa;">domain.com/nama-pengantin</code>
            </p>
        </div>

        {{-- Footer --}}
        <p style="margin-top:24px;font-size:0.7rem;color:rgba(100,116,139,0.7);">
            &copy; {{ date('Y') }} {{ config('app.name', 'TretanInvite') }} &mdash; by Anni &#x1F49B;<br>
            Dibuat dengan cinta dari Pamekasan, untuk Keluarga Indonesia.
        </p>
    </div>

    <script>
    // Generate stars
    const container = document.getElementById('stars');
    for (let i = 0; i < 90; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        const size = Math.random() * 2 + 0.5;
        s.style.cssText = `
            width:${size}px; height:${size}px;
            top:${Math.random() * 100}%;
            left:${Math.random() * 100}%;
            --o:${(Math.random() * 0.5 + 0.1).toFixed(2)};
            --d:${(Math.random() * 4 + 2).toFixed(1)}s;
            animation-delay:${(Math.random() * 3).toFixed(1)}s;
        `;
        container.appendChild(s);
    }
    </script>
</body>
</html>
