<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL: ubah ENUM untuk menambah nilai 'ditolak'
        DB::statement("ALTER TABLE `orders` MODIFY `payment_status` ENUM('belum_bayar','menunggu_konfirmasi','lunas','ditolak') NOT NULL DEFAULT 'belum_bayar'");
    }

    public function down(): void
    {
        // Hapus nilai 'ditolak' — row dengan status 'ditolak' harus di-handle manual sebelum rollback
        DB::statement("ALTER TABLE `orders` MODIFY `payment_status` ENUM('belum_bayar','menunggu_konfirmasi','lunas') NOT NULL DEFAULT 'belum_bayar'");
    }
};
