<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akun Admin – TretanInvite</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Plus Jakarta Sans', Arial, sans-serif;
    background: #070514;
    margin: 0; padding: 40px 16px;
  }

  .wrapper {
    max-width: 560px;
    margin: 0 auto;
  }

  /* ── Brand bar ── */
  .brand {
    text-align: center;
    margin-bottom: 24px;
  }
  .brand-logo {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
  }
  .brand-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 900; color: #fff;
  }
  .brand-name {
    font-size: 20px; font-weight: 800;
    color: #f1f5f9;
    letter-spacing: -0.3px;
  }
  .brand-name span { color: #f59e0b; }

  /* ── Card ── */
  .card {
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    background: #0f0c29;
  }

  /* ── Header ── */
  .header {
    padding: 40px 40px 32px;
    text-align: center;
    background: linear-gradient(160deg, rgba(124,58,237,.25), rgba(219,39,119,.15), rgba(99,102,241,.2));
    border-bottom: 1px solid rgba(255,255,255,0.07);
    position: relative;
    overflow: hidden;
  }
  .header::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 50% 0%, rgba(167,139,250,.2) 0%, transparent 65%);
  }
  .header-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 6px 16px; border-radius: 100px;
    border: 1px solid rgba(245,158,11,.35);
    background: rgba(245,158,11,.1);
    color: #fbbf24;
    font-size: 12px; font-weight: 700;
    letter-spacing: .5px; text-transform: uppercase;
    margin-bottom: 20px;
    position: relative;
  }
  .badge-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #f59e0b;
    animation: pulse 2s infinite;
  }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
  .header-emoji { font-size: 48px; margin-bottom: 12px; display: block; position: relative; }
  .header h1 {
    font-size: 26px; font-weight: 800;
    color: #f1f5f9;
    line-height: 1.3;
    position: relative;
  }
  .header h1 span {
    background: linear-gradient(135deg, #f59e0b, #ef4444, #a78bfa);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  .header-sub {
    margin-top: 8px;
    font-size: 14px; color: rgba(255,255,255,.45);
    position: relative;
  }

  /* ── Body ── */
  .body {
    padding: 36px 40px;
  }
  .greeting {
    font-size: 16px; color: rgba(255,255,255,.75);
    line-height: 1.7; margin-bottom: 20px;
  }
  .greeting strong { color: #e2e8f0; font-weight: 700; }

  /* ── OTP box ── */
  .otp-box {
    border-radius: 18px;
    padding: 28px 20px;
    text-align: center;
    margin: 28px 0;
    background: linear-gradient(160deg, rgba(124,58,237,.12), rgba(99,102,241,.1));
    border: 1px solid rgba(167,139,250,.25);
    position: relative;
    overflow: hidden;
  }
  .otp-box::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 50% 0%, rgba(167,139,250,.15) 0%, transparent 60%);
    pointer-events: none;
  }
  .otp-label-top {
    font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
    color: rgba(167,139,250,.7); margin-bottom: 16px; display: block;
  }
  .otp-code {
    font-size: 48px; font-weight: 800;
    letter-spacing: 14px;
    color: #f1f5f9;
    font-family: 'Courier New', monospace;
    display: block;
    text-shadow: 0 0 30px rgba(167,139,250,.4);
    position: relative;
  }
  .otp-expire {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 16px;
    font-size: 12px; color: rgba(251,191,36,.7);
    background: rgba(245,158,11,.1);
    border: 1px solid rgba(245,158,11,.2);
    padding: 5px 14px; border-radius: 100px;
    position: relative;
  }

  /* ── Warning ── */
  .warning {
    display: flex; align-items: flex-start; gap: 12px;
    background: rgba(245,158,11,.08);
    border: 1px solid rgba(245,158,11,.25);
    border-radius: 14px;
    padding: 16px 18px;
    margin: 24px 0;
  }
  .warning-icon { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
  .warning-text { font-size: 13px; color: rgba(253,230,138,.8); line-height: 1.6; }
  .warning-text strong { color: #fbbf24; }

  /* ── CTA Button ── */
  .btn-wrap { text-align: center; margin: 28px 0 20px; }
  .btn {
    display: inline-block;
    padding: 15px 40px;
    border-radius: 14px;
    font-size: 15px; font-weight: 800;
    color: #fff !important;
    text-decoration: none;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    box-shadow: 0 8px 24px rgba(124,58,237,.35);
    letter-spacing: .2px;
  }

  .divider {
    height: 1px;
    background: rgba(255,255,255,.06);
    margin: 24px 0;
  }
  .url-label { font-size: 12px; color: rgba(255,255,255,.35); margin-bottom: 8px; }
  .url-box {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 12px;
    color: rgba(148,163,184,.7);
    word-break: break-all;
    font-family: 'Courier New', monospace;
  }

  /* ── Footer ── */
  .footer {
    padding: 24px 40px;
    background: rgba(255,255,255,.02);
    border-top: 1px solid rgba(255,255,255,.06);
    text-align: center;
  }
  .footer-brand {
    font-size: 13px; font-weight: 700; color: rgba(255,255,255,.35);
    margin-bottom: 6px;
  }
  .footer-brand span { color: #f59e0b; }
  .footer p {
    font-size: 11px; color: rgba(255,255,255,.2);
    line-height: 1.7;
  }

  /* ── Mobile ── */
  @media (max-width: 600px) {
    body { padding: 20px 12px; }
    .header, .body, .footer { padding-left: 24px; padding-right: 24px; }
    .otp-code { font-size: 38px; letter-spacing: 10px; }
  }
</style>
</head>
<body>
<div class="wrapper">

  {{-- Brand --}}
  <div class="brand">
    <span class="brand-logo">
      <span class="brand-icon">T</span>
      <span class="brand-name"><span>Tretan</span>Invite</span>
    </span>
  </div>

  <div class="card">

    {{-- Header --}}
    <div class="header">
      <div class="header-badge">
        <span class="badge-dot"></span>
        Akun Admin Baru
      </div>
      <span class="header-emoji">🎉</span>
      <h1>Selamat Datang di<br><span>TretanInvite Admin</span></h1>
      <p class="header-sub">Akun Anda telah berhasil dibuat</p>
    </div>

    {{-- Body --}}
    <div class="body">
      <p class="greeting">Halo <strong>{{ $adminName }}</strong>,</p>
      <p class="greeting">
        Akun admin untuk <strong>Dashboard TretanInvite</strong> telah dibuat.
        Gunakan password sementara di bawah ini untuk login pertama kali.
      </p>

      {{-- OTP --}}
      <div class="otp-box">
        <span class="otp-label-top">Password Sementara</span>
        <span class="otp-code">{{ $otp }}</span>
        <span class="otp-expire">⏰ Berlaku 24 jam</span>
      </div>

      {{-- Warning --}}
      <div class="warning">
        <span class="warning-icon">⚠️</span>
        <span class="warning-text">
          Setelah login, Anda <strong>wajib mengganti password</strong> ini dengan password baru yang aman sebelum bisa mengakses dashboard.
        </span>
      </div>

      {{-- CTA --}}
      <div class="btn-wrap">
        <a class="btn" href="{{ $loginUrl }}">🔐 Login ke Dashboard →</a>
      </div>

      <div class="divider"></div>

      <p class="url-label">Atau salin tautan berikut ke browser:</p>
      <div class="url-box">{{ $loginUrl }}</div>
    </div>

    {{-- Footer --}}
    <div class="footer">
      <p class="footer-brand"><span>Tretan</span>Invite &mdash; Undangan Digital</p>
      <p>
        Jika Anda tidak merasa mendaftar, abaikan email ini.<br>
        &copy; {{ date('Y') }} TretanInvite &mdash; Dibuat dengan 💛 dari Pamekasan
      </p>
    </div>

  </div>
</div>
</body>
</html>

