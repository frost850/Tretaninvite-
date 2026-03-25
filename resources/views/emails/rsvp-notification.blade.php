<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP Masuk — {{ $wedding->bride_name }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#f8f9fa; margin:0; padding:20px; color:#333; }
        .container { max-width:560px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.1); }
        .header { background:linear-gradient(135deg,#7c3aed,#ec4899); color:#fff; padding:28px 32px; }
        .header h1 { margin:0; font-size:22px; }
        .header p { margin:6px 0 0; opacity:.85; font-size:14px; }
        .body { padding:28px 32px; }
        .badge { display:inline-block; padding:6px 16px; border-radius:999px; font-size:14px; font-weight:700; margin-bottom:20px; }
        .badge-hadir { background:#dcfce7; color:#16a34a; }
        .badge-tidak { background:#fee2e2; color:#dc2626; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        td { padding:8px 0; border-bottom:1px solid #e5e7eb; vertical-align:top; }
        td:first-child { color:#6b7280; width:140px; }
        td:last-child { font-weight:500; }
        .footer { background:#f3f4f6; padding:16px 32px; text-align:center; font-size:12px; color:#9ca3af; }
        .cta { display:inline-block; margin-top:20px; padding:10px 24px; background:#7c3aed; color:#fff; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📩 RSVP Baru Masuk!</h1>
        <p>Undangan: {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}</p>
    </div>
    <div class="body">
        <span class="badge {{ $guest->is_attending ? 'badge-hadir' : 'badge-tidak' }}">
            {{ $guest->is_attending ? '✅ Hadir' : '❌ Tidak Hadir' }}
        </span>
        <table>
            <tr><td>Nama Tamu</td><td>{{ $guest->guest_name }}</td></tr>
            @if($guest->group_name)
            <tr><td>Grup / Keluarga</td><td>{{ $guest->group_name }}</td></tr>
            @endif
            @if($guest->pax)
            <tr><td>Jumlah Hadir</td><td>{{ $guest->pax }} orang</td></tr>
            @endif
            @if($guest->notes)
            <tr><td>Catatan</td><td>{{ $guest->notes }}</td></tr>
            @endif
            <tr><td>Waktu Konfirmasi</td><td>{{ $guest->replied_at?->format('d/m/Y H:i') }} WIB</td></tr>
        </table>
        <a href="{{ route('admin.vip.rsvp-live', $wedding) }}" class="cta">Pantau Live RSVP →</a>
    </div>
    <div class="footer">
        Email ini dikirim otomatis oleh sistem undangan digital &bull; Paket VIP
    </div>
</div>
</body>
</html>
