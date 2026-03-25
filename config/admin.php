<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Password
    |--------------------------------------------------------------------------
    | Password untuk masuk ke area admin (buat undangan, import tamu).
    | Set di .env: ADMIN_PASSWORD=your_secret_password
    */
    'password' => env('ADMIN_PASSWORD', ''), // WAJIB set ADMIN_PASSWORD di .env — jika kosong login tidak bisa masuk

    /*
    |--------------------------------------------------------------------------
    | Admin Allowed Emails
    |--------------------------------------------------------------------------
    | Daftar email yang diizinkan masuk ke area admin.
    | Set di .env: ADMIN_EMAILS=email1@example.com,email2@example.com
    | Selain email ini, meskipun password benar, tidak akan bisa login.
    */
    'allowed_emails' => array_filter(array_map('trim', explode(',', env('ADMIN_EMAILS', '')))),

    /*
    |--------------------------------------------------------------------------
    | Admin Email for 2FA (Super-Admin Login Verification)
    |--------------------------------------------------------------------------
    | Email yang menerima kode OTP saat super-admin login.
    | Jika kosong, 2FA dinonaktifkan (tidak direkomendasikan untuk production).
    | Set di .env: ADMIN_EMAIL=your-admin@example.com
    */
    'email' => env('ADMIN_EMAIL', ''),

    /*
    |--------------------------------------------------------------------------
    | Admin WhatsApp Number
    |--------------------------------------------------------------------------
    | Nomor WhatsApp admin untuk menerima notifikasi pesanan baru.
    | Format internasional tanpa tanda +, contoh: 6281234567890
    | Set di .env: ADMIN_WHATSAPP=6281234567890
    */
    'whatsapp' => env('ADMIN_WHATSAPP', '6282139069782'),

    /*
    |--------------------------------------------------------------------------
    | Gambar QRIS untuk Pembayaran
    |--------------------------------------------------------------------------
    | Path gambar QRIS relatif terhadap folder public/.
    | Letakkan file QRIS Anda di: public/images/qris.png
    | Set di .env: QRIS_IMAGE=images/qris.png
    */
    'qris_image' => env('QRIS_IMAGE', 'images/qris.png'),

    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Pembayaran (menit)
    |--------------------------------------------------------------------------
    | Berapa menit pelanggan punya waktu untuk menyelesaikan pembayaran QRIS
    | sebelum pesanan otomatis dihapus.
    | Set di .env: ORDER_EXPIRY_MINUTES=30
    */
    'order_expiry_minutes' => env('ORDER_EXPIRY_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Trial Bypass IPs (Developer / Testing)
    |--------------------------------------------------------------------------
    | IP address yang dibebaskan dari batas "1 trial aktif per perangkat".
    | Cocok untuk developer yang perlu testing berulang kali.
    | Set di .env: TRIAL_BYPASS_IPS=127.0.0.1,::1,192.168.1.10
    | Default: 127.0.0.1 dan ::1 (localhost)
    */
    'trial_bypass_ips' => array_filter(array_map('trim', explode(',', env('TRIAL_BYPASS_IPS', '127.0.0.1,::1')))),
];
