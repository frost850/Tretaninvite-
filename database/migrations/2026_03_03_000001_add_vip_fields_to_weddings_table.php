<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            // VIP: Video embed (YouTube/Vimeo URL)
            $table->string('video_url', 500)->nullable()->after('music_url');

            // VIP: Custom cover/hero photo (berbeda dari couple_photo)
            $table->string('cover_photo', 500)->nullable()->after('video_url');

            // VIP: Password protection — null = tidak dilindungi
            $table->string('vip_password', 255)->nullable()->after('cover_photo');

            // VIP: Guestbook digital (tamu tulis ucapan di halaman undangan)
            $table->boolean('guestbook_enabled')->default(false)->after('vip_password');

            // VIP: Email notifikasi RSVP — null = tidak aktif
            $table->string('notify_email', 255)->nullable()->after('guestbook_enabled');

            // VIP: Extra acara (JSON array: [{label, date, time, location}])
            $table->json('extra_events')->nullable()->after('notify_email');
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn([
                'video_url', 'cover_photo', 'vip_password',
                'guestbook_enabled', 'notify_email', 'extra_events',
            ]);
        });
    }
};
