<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            // Mempelai wanita / yang berulang tahun
            $table->string('bride_name');
            $table->unsignedTinyInteger('bride_age')->nullable();
            $table->string('bride_photo')->nullable();
            $table->string('bride_parent')->nullable();
            $table->string('bride_ig', 100)->nullable();
            $table->string('bride_bank', 100)->nullable();
            $table->string('bride_norek', 100)->nullable();

            // Mempelai pria (null untuk undangan birthday)
            $table->string('groom_name')->nullable();
            $table->string('groom_photo')->nullable();
            $table->string('couple_photo')->nullable();
            $table->string('groom_parent')->nullable();
            $table->string('groom_ig', 100)->nullable();
            $table->string('groom_bank', 100)->nullable();
            $table->string('groom_norek', 100)->nullable();

            // Jadwal acara
            $table->date('event_date')->nullable();
            $table->date('akad_date')->nullable();
            $table->string('akad_time', 50)->nullable();
            $table->string('akad_location')->nullable();
            $table->date('reception_date')->nullable();
            $table->string('reception_time', 50)->nullable();
            $table->string('reception_location')->nullable();

            // Lokasi
            $table->string('location')->nullable();
            $table->string('map_link')->nullable();
            $table->text('map_embed')->nullable();

            // Konten
            $table->string('dresscode', 100)->nullable();
            $table->text('opening_text')->nullable();
            $table->string('music_url')->nullable();

            // Pengaturan
            $table->boolean('is_active')->default(true);
            $table->string('template', 50)->default('classic');
            $table->string('tracking_token', 32)->nullable()->unique();
            $table->boolean('has_gallery')->default(false);
            $table->enum('package', ['trial', 'basic', 'premium'])->default('premium');
            $table->timestamp('trial_expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weddings');
    }
};
