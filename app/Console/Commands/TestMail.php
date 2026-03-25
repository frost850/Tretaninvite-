<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMail extends Command
{
    protected $signature   = 'app:test-mail {to? : Alamat email tujuan (default: MAIL_USERNAME)}';
    protected $description = 'Kirim test email untuk memverifikasi konfigurasi SMTP';

    public function handle(): void
    {
        $to = $this->argument('to') ?? config('mail.from.address');

        $this->info("Mengirim test email ke: {$to} ...");

        \Illuminate\Support\Facades\Mail::raw(
            'Test email dari TretanInvite Laravel. Konfigurasi SMTP berhasil!',
            fn ($m) => $m->to($to)->subject('Test SMTP TretanInvite ✔')
        );

        $this->info('Email berhasil dikirim! Silakan cek inbox / spam.');
    }
}
