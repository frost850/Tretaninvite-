<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hapus pesanan kedaluwarsa setiap 5 menit
Schedule::command('orders:cleanup-expired')->everyFiveMinutes();

// Hapus undangan yang masa aktifnya habis, cek setiap hari jam 02:00
Schedule::command('weddings:cleanup-expired')->dailyAt('02:00');
// Hapus data trial yang sudah expired lebih dari 7 hari, jam 03:00
Schedule::command('trial:prune')->dailyAt('03:00');
// Backup database harian jam 01:00, simpan 7 file terakhir
Schedule::command('db:backup --keep=7')->dailyAt('01:00');
// Hapus permanen item Recycle Bin yang sudah > 30 hari, jam 04:00
Schedule::command('recycle:purge')->dailyAt('04:00');

// Kirim email pengingat expiry 7 hari & 1 hari, cek setiap jam
Schedule::command('weddings:notify-expiry')->hourly();