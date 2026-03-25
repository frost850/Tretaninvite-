<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE weddings MODIFY COLUMN package ENUM('trial','basic','premium','vip') NOT NULL DEFAULT 'basic'");
    }

    public function down(): void
    {
        // Downgrade: revert vip rows to basic first to avoid data loss
        DB::statement("UPDATE weddings SET package = 'basic' WHERE package = 'vip'");
        DB::statement("ALTER TABLE weddings MODIFY COLUMN package ENUM('trial','basic','premium') NOT NULL DEFAULT 'basic'");
    }
};
