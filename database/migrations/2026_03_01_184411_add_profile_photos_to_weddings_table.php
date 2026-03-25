<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            if (!Schema::hasColumn('weddings', 'bride_photo')) {
                $table->string('bride_photo')->nullable()->after('bride_age');
            }
            if (!Schema::hasColumn('weddings', 'groom_photo')) {
                $table->string('groom_photo')->nullable()->after('groom_name');
            }
            if (!Schema::hasColumn('weddings', 'couple_photo')) {
                $table->string('couple_photo')->nullable()->after('groom_photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn(['bride_photo', 'groom_photo', 'couple_photo']);
        });
    }
};
