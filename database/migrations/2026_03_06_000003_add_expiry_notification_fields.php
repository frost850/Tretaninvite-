<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // customer_email di orders — opsional, untuk notifikasi expiry
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email', 255)->nullable()->after('customer_phone');
            }
        });

        // tracking pengiriman notifikasi di weddings
        Schema::table('weddings', function (Blueprint $table) {
            if (! Schema::hasColumn('weddings', 'expiry_notified_7d_at')) {
                $table->timestamp('expiry_notified_7d_at')->nullable()->after('trial_expires_at');
            }
            if (! Schema::hasColumn('weddings', 'expiry_notified_1d_at')) {
                $table->timestamp('expiry_notified_1d_at')->nullable()->after('expiry_notified_7d_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn(['expiry_notified_7d_at', 'expiry_notified_1d_at']);
        });
    }
};
