<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('groom_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Nilai null yang sudah ada akan menjadi string kosong agar tidak error
        \DB::table('weddings')->whereNull('groom_name')->update(['groom_name' => '']);

        Schema::table('weddings', function (Blueprint $table) {
            $table->string('groom_name')->nullable(false)->change();
        });
    }
};
