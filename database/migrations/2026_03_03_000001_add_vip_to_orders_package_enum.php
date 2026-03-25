<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the ENUM to include 'vip'
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `package` ENUM('basic','premium','vip') NOT NULL DEFAULT 'basic'");
    }

    public function down(): void
    {
        // Revert — existing 'vip' rows will be set to default before downgrade
        DB::statement("UPDATE `orders` SET `package` = 'basic' WHERE `package` = 'vip'");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `package` ENUM('basic','premium') NOT NULL DEFAULT 'basic'");
    }
};
