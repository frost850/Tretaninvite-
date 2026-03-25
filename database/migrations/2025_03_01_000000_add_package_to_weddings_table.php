<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            // Kolom galeri
            if (!Schema::hasColumn('weddings', 'has_gallery')) {
                $table->boolean('has_gallery')->default(false)->after('tracking_token');
            }
            // Paket & masa aktif
            if (!Schema::hasColumn('weddings', 'package')) {
                $table->enum('package', ['trial', 'basic', 'premium'])->default('premium')->after('has_gallery');
            }
            if (!Schema::hasColumn('weddings', 'trial_expires_at')) {
                $table->timestamp('trial_expires_at')->nullable()->after('package');
            }
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn(['has_gallery', 'package', 'trial_expires_at']);
        });
    }
};
