<?php
// Jalankan: php artisan tinker --execute="require 'test-mail.php';"
// atau: php artisan eval test-mail.php

use Illuminate\Support\Facades\Mail;

$testTo = env('MAIL_TEST_TO', 'test@example.com');
Mail::raw('Test email dari TretanInvite Laravel. Konfigurasi SMTP berhasil!', function ($message) use ($testTo) {
    $message->to($testTo)
            ->subject('Test SMTP TretanInvite ✔');
});

echo "Email berhasil dikirim ke {$testTo}\n";
