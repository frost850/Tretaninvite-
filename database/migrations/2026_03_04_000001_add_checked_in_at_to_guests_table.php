<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (!Schema::hasColumn('guests', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('first_opened_at')
                      ->comment('Waktu check-in via scan QR di venue (VIP)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumnIfExists('checked_in_at');
        });
    }
};
