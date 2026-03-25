<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi Login Admin</title>
</head>
<body style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;background:#f8f9fa;margin:0;padding:24px;">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
        <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:28px 32px;">
            <h1 style="margin:0;color:#fff;font-size:22px;font-weight:800;">✨ TretanInvite</h1>
            <p style="margin:4px 0 0;color:rgba(255,255,255,0.85);font-size:13px;">Admin Security</p>
        </div>
        <div style="padding:32px;">
            <h2 style="margin:0 0 12px;color:#1e293b;font-size:18px;">Verifikasi Login Super Admin</h2>
            <p style="color:#64748b;font-size:14px;margin:0 0 24px;">
                Seseorang mencoba login sebagai super-admin dari IP: <strong>{{ $ipAddress }}</strong>.<br>
                Gunakan kode di bawah untuk melanjutkan masuk:
            </p>
            <div style="background:#f1f5f9;border-radius:10px;padding:20px;text-align:center;margin-bottom:24px;">
                <span style="font-size:38px;font-weight:900;letter-spacing:0.35em;color:#0f172a;font-family:monospace;">{{ $otp }}</span>
            </div>
            <p style="color:#94a3b8;font-size:12px;margin:0 0 8px;">
                Kode berlaku selama <strong>10 menit</strong>.
            </p>
            <p style="color:#94a3b8;font-size:12px;margin:0;">
                Jika Anda tidak mencoba login, segera ganti password admin dan periksa keamanan server Anda.
            </p>
        </div>
    </div>
</body>
</html>
