<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn(['bride_ig', 'groom_ig']);
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('bride_ig', 100)->nullable()->after('bride_parent');
            $table->string('groom_ig', 100)->nullable()->after('groom_parent');
        });
    }
};
