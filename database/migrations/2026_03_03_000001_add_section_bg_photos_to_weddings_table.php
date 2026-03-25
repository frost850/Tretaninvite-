<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('bg_mempelai_photo')->nullable();
            $table->string('bg_acara_photo')->nullable();
            $table->string('bg_lokasi_photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn(['bg_mempelai_photo', 'bg_acara_photo', 'bg_lokasi_photo']);
        });
    }
};
