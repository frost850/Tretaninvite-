<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('bride_wa', 20)->nullable()->after('bride_ig');
            $table->string('groom_wa', 20)->nullable()->after('groom_ig');
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn(['bride_wa', 'groom_wa']);
        });
    }
};
