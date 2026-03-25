<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            // Hapus kolom 7-hari (tidak dipakai lagi)
            if (Schema::hasColumn('weddings', 'expiry_notified_7d_at')) {
                $table->dropColumn('expiry_notified_7d_at');
            }
            // Rename 1-hari → 2-hari
            if (Schema::hasColumn('weddings', 'expiry_notified_1d_at')
                && ! Schema::hasColumn('weddings', 'expiry_notified_2d_at')) {
                $table->renameColumn('expiry_notified_1d_at', 'expiry_notified_2d_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            if (Schema::hasColumn('weddings', 'expiry_notified_2d_at')
                && ! Schema::hasColumn('weddings', 'expiry_notified_1d_at')) {
                $table->renameColumn('expiry_notified_2d_at', 'expiry_notified_1d_at');
            }
            if (! Schema::hasColumn('weddings', 'expiry_notified_7d_at')) {
                $table->timestamp('expiry_notified_7d_at')->nullable()->after('expiry_notified_1d_at');
            }
        });
    }
};
