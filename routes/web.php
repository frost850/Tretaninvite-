<?php

use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnniversaryController;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestImportController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TrialController;
use App\Http\Controllers\TrialGuestController;
use App\Http\Controllers\CustomerVipController;
use App\Http\Controllers\VipController;
use App\Http\Controllers\WeddingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Untuk pelanggan: hanya lihat dan pilih paket (template). Tidak bisa buat undangan atau import tamu.
Route::get('/paket', [PackageController::class, 'index'])->name('packages.index');

// Pemesanan paket oleh pelanggan
Route::get('/pesan', [OrderController::class, 'create'])->name('orders.create');
Route::post('/pesan', [OrderController::class, 'store'])->name('orders.store')->middleware('throttle:10,1');
// URL pakai token acak — bukan ID — agar tidak bisa ditebak
Route::get('/pesan/bayar/{token}', [OrderController::class, 'payment'])->name('orders.payment');
Route::post('/pesan/bayar/{token}/bukti', [OrderController::class, 'uploadProof'])->name('orders.proof')->middleware('throttle:5,1');
Route::get('/pesan/terima-kasih/{token}', [OrderController::class, 'thanks'])->name('orders.thanks');

// Tracking untuk pelanggan: lihat siapa yang sudah buka undangan & RSVP
Route::get('/tracking/{token}', [TrackingController::class, 'show'])->name('tracking.show')->middleware('throttle:30,1');

// Preview contoh template: path publik (bukan admin). Pelanggan & admin sama-sama pakai ini.
Route::get('/preview/{template}', [PreviewController::class, 'show'])->name('preview.template')->where('template', '[a-z0-9\-]+');

// Coba gratis — pelanggan isi & aktifkan undangan sendiri tanpa login
Route::get('/coba/selesai/{slug}', [TrialController::class, 'success'])->name('trial.success');
Route::post('/coba', [TrialController::class, 'store'])->name('trial.store')->middleware('throttle:trial-store');

// Kelola tamu trial — publik, tanpa admin login, akses via slug wedding
// (harus SEBELUM /coba/{template} agar 'tamu' tidak ditangkap sebagai nama template)
Route::get('/coba/tamu/{slug}', [TrialGuestController::class, 'index'])->name('trial.guests.index');
Route::post('/coba/tamu/{slug}', [TrialGuestController::class, 'store'])->name('trial.guests.store')->middleware('throttle:20,1');
Route::delete('/coba/tamu/{slug}/{guest}', [TrialGuestController::class, 'destroy'])->name('trial.guests.destroy')->middleware('throttle:30,1');

Route::get('/coba/{template}', [TrialController::class, 'create'])->name('trial.create');

// Login admin (tanpa middleware auth, tapi ada throttle anti brute-force)
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// 2FA OTP untuk super-admin (setelah password benar, sebelum sesi login penuh)
Route::get('/admin/login/otp', [AdminLoginController::class, 'showOtp'])->name('admin.otp.show')->middleware('throttle:20,1');
Route::post('/admin/login/otp', [AdminLoginController::class, 'verifyOtp'])->name('admin.otp.verify')->middleware('throttle:5,1');
Route::post('/admin/login/otp/resend', [AdminLoginController::class, 'resendOtp'])->name('admin.otp.resend')->middleware('throttle:3,5');

// Area admin: hanya yang sudah login (buat undangan, import tamu, daftar undangan)
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistik', [DashboardController::class, 'statistik'])->name('statistik');
    Route::get('/track-record', [DashboardController::class, 'trackRecord'])->name('track-record');
    Route::redirect('/', '/admin/dashboard');
    Route::get('/invitation', [WeddingController::class, 'index'])->name('weddings.index');
    Route::redirect('/weddings', '/admin/invitation');
    Route::get('/weddings/create', [WeddingController::class, 'selectTemplate'])->name('weddings.create');
    Route::get('/weddings/create/{template}', [WeddingController::class, 'createForm'])->name('weddings.create.form');
    Route::post('/weddings', [WeddingController::class, 'store'])->name('weddings.store');
    Route::get('/weddings/{wedding}/edit', [WeddingController::class, 'edit'])->name('weddings.edit');
    Route::put('/weddings/{wedding}', [WeddingController::class, 'update'])->name('weddings.update');
    Route::delete('/weddings/{wedding}', [WeddingController::class, 'destroy'])->name('weddings.destroy');
    Route::patch('/weddings/{wedding}/extend', [WeddingController::class, 'extendExpiry'])->name('weddings.extend');
    Route::patch('/weddings/{wedding}/force-expire', [WeddingController::class, 'forceExpire'])->name('weddings.force-expire');
    Route::patch('/weddings/{wedding}/toggle-active', [WeddingController::class, 'toggleActive'])->name('weddings.toggle-active');
    Route::get('/weddings/{wedding}/print-invitations', [WeddingController::class, 'printInvitations'])->name('weddings.print');

    // Gallery routes
    Route::post('/weddings/{wedding}/gallery', [WeddingController::class, 'uploadGallery'])->name('weddings.gallery.upload');
    Route::delete('/weddings/{wedding}/gallery/{photo}', [WeddingController::class, 'deleteGalleryPhoto'])->name('weddings.gallery.delete');

    // Profile photos routes
    Route::post('/weddings/{wedding}/profile-photo', [WeddingController::class, 'uploadProfilePhoto'])->name('weddings.profile-photo.upload');
    Route::delete('/weddings/{wedding}/profile-photo/{type}', [WeddingController::class, 'deleteProfilePhoto'])->name('weddings.profile-photo.delete');

    // ─── Birthday (undangan ulang tahun) ────────────────────────────────────
    Route::get('/birthdays/create/{template}', [BirthdayController::class, 'createForm'])->name('birthdays.create.form');
    Route::post('/birthdays', [BirthdayController::class, 'store'])->name('birthdays.store');
    Route::get('/birthdays/{birthday}/edit', [BirthdayController::class, 'edit'])->name('birthdays.edit');
    Route::put('/birthdays/{birthday}', [BirthdayController::class, 'update'])->name('birthdays.update');
    Route::delete('/birthdays/{birthday}', [BirthdayController::class, 'destroy'])->name('birthdays.destroy');

    // ─── Greeting Card (kartu ucapan ulang tahun) ────────────────────────────
    Route::get('/greetings/create/{template}', [GreetingController::class, 'createForm'])->name('greetings.create.form');
    Route::post('/greetings', [GreetingController::class, 'store'])->name('greetings.store');
    Route::get('/greetings/{greeting}/edit', [GreetingController::class, 'edit'])->name('greetings.edit');
    Route::put('/greetings/{greeting}', [GreetingController::class, 'update'])->name('greetings.update');
    Route::delete('/greetings/{greeting}', [GreetingController::class, 'destroy'])->name('greetings.destroy');

    // ─── Anniversary (undangan hari jadi pernikahan) ───────────────────────────
    Route::get('/anniversaries/create/{template}', [AnniversaryController::class, 'createForm'])->name('anniversaries.create.form');
    Route::post('/anniversaries', [AnniversaryController::class, 'store'])->name('anniversaries.store');
    Route::get('/anniversaries/{anniversary}/edit', [AnniversaryController::class, 'edit'])->name('anniversaries.edit');
    Route::put('/anniversaries/{anniversary}', [AnniversaryController::class, 'update'])->name('anniversaries.update');
    Route::delete('/anniversaries/{anniversary}', [AnniversaryController::class, 'destroy'])->name('anniversaries.destroy');

    Route::get('/guests/import', [GuestImportController::class, 'index'])->name('guests.import');
    Route::post('/guests/import', [GuestImportController::class, 'store'])->name('guests.import.store');
    Route::get('/guests/import/template', [GuestImportController::class, 'downloadTemplate'])->name('guests.import.template');

    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('/guests/export', [GuestController::class, 'export'])->name('guests.export');
    Route::get('/guests/create', [GuestController::class, 'create'])->name('guests.create');
    Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');
    Route::get('/guests/{guest}/edit', [GuestController::class, 'edit'])->name('guests.edit');
    Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

    // ─── VIP Features ──────────────────────────────────────────────────────
    Route::prefix('vip')->name('vip.')->group(function () {
        Route::get('/dashboard',            [VipController::class, 'dashboard'])->name('dashboard');
        Route::get('/{wedding}/rsvp-live',  [VipController::class, 'rsvpLive'])->name('rsvp-live');
        Route::get('/{wedding}/rsvp-live/data', [VipController::class, 'rsvpLiveData'])->name('rsvp-live.data');
        Route::get('/qr-codes',             [VipController::class, 'qrCodes'])->name('qr-codes');
        Route::get('/scan',                 [VipController::class, 'scanPage'])->name('scan');
        Route::post('/scan/checkin',        [VipController::class, 'checkIn'])->name('scan.checkin')->middleware('throttle:120,1');
        Route::get('/export',               [VipController::class, 'exportReport'])->name('export');
        Route::get('/guestbook',            [VipController::class, 'guestbook'])->name('guestbook');
        Route::patch('/guestbook/{entry}/toggle', [VipController::class, 'guestbookToggle'])->name('guestbook.toggle');
        Route::delete('/guestbook/{entry}', [VipController::class, 'guestbookDestroy'])->name('guestbook.destroy');
        Route::get('/settings',             [VipController::class, 'settings'])->name('settings');
        Route::put('/{wedding}/settings',   [VipController::class, 'settingsUpdate'])->name('settings.update');
        Route::delete('/{wedding}/settings/password', [VipController::class, 'settingsClearPassword'])->name('settings.clear-password');
    });

    // ─── Admin Management ──────────────────────────────────────────────────
    Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
    Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store')->middleware('throttle:10,1');
    Route::patch('/admins/{user}/reset-otp', [AdminManagementController::class, 'resetOtp'])->name('admins.resetOtp')->middleware('throttle:10,1');
    Route::patch('/admins/{user}/toggle-active', [AdminManagementController::class, 'toggleActive'])->name('admins.toggleActive')->middleware('throttle:20,1');
    Route::delete('/admins/{user}', [AdminManagementController::class, 'destroy'])->name('admins.destroy')->middleware('throttle:10,1');

    // ─── Admin Profile ─────────────────────────────────────────────────────
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile');
    Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

    // Kelola pesanan pelanggan
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::get('/orders/{order}/proof', [OrderController::class, 'proofDownload'])->name('orders.proof');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/link', [OrderController::class, 'linkWedding'])->name('orders.link');
    Route::patch('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::patch('/orders/{order}/reject-payment',  [OrderController::class, 'rejectPayment'])->name('orders.reject-payment');
    Route::patch('/orders/{order}/reset-payment',   [OrderController::class, 'resetPayment'])->name('orders.reset-payment');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/settings/qris', [OrderController::class, 'uploadQris'])->name('settings.qris');

    // ─── Recycle Bin ───────────────────────────────────────────────────────
    Route::get('/recycle',                         [RecycleBinController::class, 'index'])->name('recycle.index');
    Route::patch('/recycle/weddings/{id}/restore', [RecycleBinController::class, 'restoreWedding'])->name('recycle.weddings.restore');
    Route::delete('/recycle/weddings/{id}',        [RecycleBinController::class, 'forceDeleteWedding'])->name('recycle.weddings.force-delete');
    Route::patch('/recycle/orders/{id}/restore',   [RecycleBinController::class, 'restoreOrder'])->name('recycle.orders.restore');
    Route::delete('/recycle/orders/{id}',          [RecycleBinController::class, 'forceDeleteOrder'])->name('recycle.orders.force-delete');
    Route::delete('/recycle',                      [RecycleBinController::class, 'purgeAll'])->name('recycle.purge');

    // ─── Audit Log (super admin only) ─────────────────────────────────────
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log');
    Route::get('/security',  [AuditLogController::class, 'security'])->name('security');
});

// ─── Customer VIP Portal — akses via public_token order (tanpa login) ─
Route::prefix('my')->name('my.')->middleware('throttle:60,1')->group(function () {
    Route::get('/{token}',                            [CustomerVipController::class, 'dashboard'])->name('vip.dashboard');
    Route::get('/{token}/guestbook',                  [CustomerVipController::class, 'guestbook'])->name('vip.guestbook');
    Route::patch('/{token}/guestbook/{entry}/toggle', [CustomerVipController::class, 'guestbookToggle'])->name('vip.guestbook.toggle');
    Route::delete('/{token}/guestbook/{entry}',       [CustomerVipController::class, 'guestbookDestroy'])->name('vip.guestbook.destroy');
    Route::get('/{token}/qr-codes',                   [CustomerVipController::class, 'qrCodes'])->name('vip.qr-codes');
    Route::get('/{token}/scan',                       [CustomerVipController::class, 'scan'])->name('vip.scan');
    Route::post('/{token}/scan/checkin',              [CustomerVipController::class, 'checkIn'])->name('vip.scan.checkin');
    Route::get('/{token}/print-invitations',          [CustomerVipController::class, 'printInvitations'])->name('vip.print');
    // ─── Premium: kelola daftar tamu ────────────────────────────────────
    Route::get('/{token}/tamu',                       [CustomerVipController::class, 'guests'])->name('vip.guests');
    Route::post('/{token}/tamu',                      [CustomerVipController::class, 'guestStore'])->name('vip.guests.store')->middleware('throttle:30,1');
    Route::delete('/{token}/tamu/{guest}',            [CustomerVipController::class, 'guestDestroy'])->name('vip.guests.destroy')->middleware('throttle:30,1');
    Route::patch('/{token}/tamu/{guest}',             [CustomerVipController::class, 'guestUpdate'])->name('vip.guests.update')->middleware('throttle:30,1');
});

// Password unlock untuk undangan dilindungi (VIP)
Route::post('/{slug}/unlock', [InvitationController::class, 'unlock'])->middleware('throttle:10,1')->where('slug', '[a-z0-9\-]+');

// Public guestbook (VIP) — GET: polling JSON, POST: submit
Route::get('/{slug}/guestbook',  [InvitationController::class, 'fetchGuestbook'])->middleware('throttle:60,1')->where('slug', '[a-z0-9\-]+');
Route::post('/{slug}/guestbook', [InvitationController::class, 'storeGuestbook'])->middleware('throttle:5,1')->where('slug', '[a-z0-9\-]+');

// Greeting card AJAX endpoints (harus sebelum catch-all /{slug})
Route::get('/{slug}/gc/reactions', [GreetingController::class, 'getReactions'])->where('slug', '[a-z0-9\-]+');
Route::post('/{slug}/gc/react', [GreetingController::class, 'addReaction'])->where('slug', '[a-z0-9\-]+')->middleware('throttle:gc-react');
Route::post('/{slug}/gc/wish', [GreetingController::class, 'storeWish'])->where('slug', '[a-z0-9\-]+')->middleware('throttle:5,1');
Route::get('/{slug}/gc/wishes', [GreetingController::class, 'getWishes'])->where('slug', '[a-z0-9\-]+');
Route::get('/{slug}/gc/gallery', [GreetingController::class, 'getGallery'])->where('slug', '[a-z0-9\-]+');

// Undangan publik: domain.com/andi-siti?to=farhan
Route::get('/{slug}', [InvitationController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('invitation.show')
    ->middleware('throttle:60,1');

// RSVP dari tamu (submit konfirmasi hadir/tidak) — semua template pakai /{slug}/rsvp
Route::post('/{slug}/rsvp', [InvitationController::class, 'storeRsvp'])->name('rsvp.store')->middleware('throttle:10,1')->where('slug', '[a-z0-9\-]+');
