<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Pengingat 2 Hari Sebelum Expired | TretanInvite</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #07060f;
            color: #e2e8f0;
            -webkit-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
            background-color: #07060f;
            padding: 40px 16px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
        }

        /* ── Header logo ── */
        .logo-bar {
            text-align: center;
            padding-bottom: 28px;
        }
        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            font-size: 1.3rem; font-weight: 900; color: #fff;
            margin-right: 10px;
            vertical-align: middle;
        }
        .logo-text {
            font-size: 1.1rem; font-weight: 800;
            color: #fff; vertical-align: middle;
        }
        .logo-text span { color: #f59e0b; }

        /* ── Card ── */
        .card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 20px;
            overflow: hidden;
        }

        /* ── Hero banner ── */
        .hero {
            padding: 40px 36px 32px;
            text-align: center;
            background: linear-gradient(160deg,
                rgba(124,58,237,0.18) 0%,
                rgba(239,68,68,0.10) 60%,
                rgba(7,6,15,0) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .hero-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 99px;
            font-size: 0.72rem; font-weight: 800;
            letter-spacing: 0.06em; text-transform: uppercase;
            margin-bottom: 20px;
        }
        .badge-urgent {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.35);
            color: #fca5a5;
        }
        .badge-warning {
            background: rgba(245,158,11,0.12);
            border: 1px solid rgba(245,158,11,0.3);
            color: #fcd34d;
        }

        .hero-icon {
            font-size: 3.2rem;
            line-height: 1;
            margin-bottom: 16px;
            display: block;
        }

        .hero-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #f1f5f9;
            line-height: 1.25;
            margin-bottom: 10px;
        }
        .hero-title .highlight {
            background: linear-gradient(135deg, #f59e0b, #ef4444, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 0.9rem;
            color: rgba(148,163,184,0.9);
            line-height: 1.6;
        }

        /* ── Body content ── */
        .body-content {
            padding: 32px 36px;
        }

        .greeting {
            font-size: 0.9rem;
            color: rgba(203,213,225,0.85);
            line-height: 1.65;
            margin-bottom: 24px;
        }
        .greeting strong { color: #f1f5f9; }

        /* ── Info card ── */
        .info-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 24px;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 9px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-row:first-child { padding-top: 0; }
        .info-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(100,116,139,0.9);
            min-width: 110px;
            padding-top: 1px;
        }
        .info-value {
            font-size: 0.88rem;
            font-weight: 600;
            color: #e2e8f0;
            flex: 1;
        }

        /* ── Countdown box ── */
        .countdown-box {
            border-radius: 14px;
            padding: 20px 22px;
            text-align: center;
            margin-bottom: 24px;
        }
        .countdown-urgent {
            background: linear-gradient(135deg, rgba(239,68,68,0.12), rgba(220,38,38,0.07));
            border: 1px solid rgba(239,68,68,0.3);
        }
        .countdown-warning {
            background: linear-gradient(135deg, rgba(245,158,11,0.12), rgba(217,119,6,0.07));
            border: 1px solid rgba(245,158,11,0.3);
        }
        .countdown-num {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 4px;
        }
        .num-urgent  { color: #f87171; }
        .num-warning { color: #fbbf24; }
        .countdown-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(148,163,184,0.8);
        }
        .countdown-date {
            font-size: 0.78rem;
            margin-top: 8px;
            color: rgba(100,116,139,0.9);
        }

        /* ── Steps (what to do) ── */
        .steps-label {
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(100,116,139,0.8);
            margin-bottom: 12px;
        }
        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }
        .step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px; height: 26px;
            border-radius: 8px;
            background: rgba(124,58,237,0.2);
            border: 1px solid rgba(167,139,250,0.25);
            font-size: 0.72rem;
            font-weight: 800;
            color: #c4b5fd;
            flex-shrink: 0;
        }
        .step-text {
            font-size: 0.85rem;
            color: rgba(203,213,225,0.85);
            line-height: 1.5;
            padding-top: 3px;
        }
        .step-text a { color: #a78bfa; text-decoration: underline; }

        /* ── CTA button ── */
        .cta-wrap { text-align: center; margin: 28px 0 8px; }
        .cta-btn {
            display: inline-block;
            padding: 14px 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #fff !important;
            font-size: 0.9rem;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 20px rgba(124,58,237,0.4);
        }
        .cta-wa {
            display: inline-block;
            margin-top: 10px;
            padding: 12px 28px;
            border-radius: 12px;
            background: rgba(34,197,94,0.12);
            border: 1px solid rgba(34,197,94,0.3);
            color: #4ade80 !important;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 8px 0 24px;
        }

        /* ── Ignore message ── */
        .ignore-msg {
            font-size: 0.78rem;
            color: rgba(100,116,139,0.7);
            line-height: 1.5;
            text-align: center;
            padding: 0 8px;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            padding-top: 28px;
        }
        .footer p {
            font-size: 0.72rem;
            color: rgba(71,85,105,0.8);
            line-height: 1.7;
        }
        .footer a { color: rgba(100,116,139,0.7); }
    </style>
</head>
<body>
<div class="wrapper">
<div class="container">

    {{-- Logo --}}
    <div class="logo-bar">
        <span class="logo-icon">T</span>
        <span class="logo-text"><span>Tretan</span>Invite</span>
    </div>

    <div class="card">

        {{-- Hero --}}
        <div class="hero">
            <div class="hero-badge badge-warning">🔔 Pengingat Masa Aktif Undangan</div>

            <span class="hero-icon">📅</span>

            @php
                $invitationName = $wedding->bride_name;
                if ($wedding->groom_name) $invitationName .= ' & ' . $wedding->groom_name;
                $jenisLabel = match(true) {
                    str_starts_with($wedding->template ?? '', 'birthday')   => 'Undangan Ulang Tahun',
                    str_starts_with($wedding->template ?? '', 'greeting')   => 'Greeting Card',
                    default                                                  => 'Undangan Pernikahan',
                };
                $paketLabel = match($wedding->package) {
                    'vip'     => '💎 VIP Royal',
                    'premium' => '⭐ Premium',
                    'basic'   => '💙 Basic',
                    default   => '🔓 Trial',
                };
                // Context helpers
                $isGreeting    = str_starts_with($wedding->template ?? '', 'greeting');
                $isTrial       = $wedding->package === 'trial';
                $isBasicPlus   = in_array($wedding->package, ['basic', 'premium', 'vip']);
                $isPremiumPlus = in_array($wedding->package, ['premium', 'vip']);
                $isVip         = $wedding->package === 'vip';
                $hasRsvp       = !$isGreeting;
                $hasGuestExport = $isBasicPlus && $hasRsvp;
                $hasGallery    = $isPremiumPlus;
                $linkLabel     = $isGreeting ? 'kartu ucapan' : 'undangan';

                $invUrl  = url('/' . $wedding->slug);
                $adminWa = config('admin.whatsapp', '');
                $waMsg   = 'Halo Admin TretanInvite, saya ingin memperpanjang masa aktif ' . $linkLabel . ' «' . $invitationName . '» (slug: ' . $wedding->slug . '). Mohon bantuannya 🙏';
                $waUrl   = $adminWa ? 'https://wa.me/' . preg_replace('/\D+/', '', $adminWa) . '?text=' . rawurlencode($waMsg) : null;
            @endphp

            <h1 class="hero-title">
                Masa aktif undangan
                <span class="highlight">«{{ $invitationName }}»</span>
                tersisa <span class="highlight">2 hari!</span>
            </h1>
            <p class="hero-subtitle">
                Segera perpanjang agar tamu masih bisa membuka undangan dan mengirim konfirmasi kehadiran.
            </p>
        </div>

        {{-- Body --}}
        <div class="body-content">

            <p class="greeting">
                Hai, <strong>{{ $customerName }}</strong>! 👋<br>
                Kami ingin mengingatkan bahwa {{ $jenisLabel }} yang Anda pesan melalui
                <strong style="color:#f1f5f9;">TretanInvite</strong> akan segera berakhir masa aktifnya.
            </p>

            {{-- Countdown --}}
            <div class="countdown-box countdown-warning">
                <div class="countdown-num num-warning">2</div>
                <div class="countdown-label">Hari tersisa sebelum expired</div>
                @if($wedding->trial_expires_at)
                <div class="countdown-date">
                    Expired pada: {{ $wedding->trial_expires_at->translatedFormat('l, d F Y \p\u\k\u\l H:i') }} WIB
                </div>
                @endif
            </div>

            {{-- Info undangan --}}
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Jenis</span>
                    <span class="info-value">{{ $jenisLabel }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ $invitationName }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Paket</span>
                    <span class="info-value">{{ $paketLabel }}</span>
                </div>
                @if($wedding->event_date)
                <div class="info-row">
                    <span class="info-label">Tanggal Acara</span>
                    <span class="info-value">{{ $wedding->event_date->translatedFormat('d F Y') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Link {{ ucfirst($linkLabel) }}</span>
                    <span class="info-value">
                        <a href="{{ $invUrl }}" style="color:#a78bfa;text-decoration:underline;word-break:break-all;">{{ $invUrl }}</a>
                    </span>
                </div>
            </div>

            {{-- Terima kasih --}}
            <div style="background:rgba(124,58,237,0.06);border:1px solid rgba(167,139,250,0.15);border-radius:12px;padding:16px 20px;margin-bottom:24px;text-align:center;">
                <p style="font-size:0.88rem;color:rgba(203,213,225,0.85);line-height:1.7;margin:0;">
                    Terima kasih sudah mempercayakan momen spesial Anda kepada<br>
                    <strong style="color:#e2e8f0;">TretanInvite</strong> 💛 Kami senang bisa menjadi bagian dari cerita indah Anda.
                </p>
            </div>

            {{-- Apa yang harus dilakukan --}}
            <p class="steps-label">Apa yang bisa Anda lakukan sekarang?</p>

            <div class="step-item">
                <span class="step-num">1</span>
                <span class="step-text">
                    <strong style="color:#e2e8f0;">Hubungi admin</strong> jika ada yang ingin ditanyakan.
                </span>
            </div>

            @if($waUrl)
            <div class="cta-wrap" style="margin-top:4px;margin-bottom:20px;">
                <a href="{{ $waUrl }}" class="cta-wa">💬 Chat Admin WhatsApp</a>
            </div>
            @endif

            {{-- CTA buka undangan --}}
            <div class="cta-wrap">
                <a href="{{ $invUrl }}" class="cta-btn">
                    🔗 Buka Link {{ ucfirst($linkLabel) }} →
                </a>
            </div>

            <hr class="divider">

            <p class="ignore-msg">
                Email ini dikirim otomatis karena Anda mencantumkan alamat email saat memesan
                melalui <strong>TretanInvite</strong>.<br>
                Jika ini bukan Anda atau Anda tidak ingin menerima notifikasi ini, abaikan saja email ini.
            </p>

        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>&copy; {{ date('Y') }} TretanInvite &mdash; by Anni 💛<br>Dibuat dengan cinta dari Pamekasan, untuk Keluarga Indonesia.</p>
    </div>

</div>
</div>
</body>
</html>
