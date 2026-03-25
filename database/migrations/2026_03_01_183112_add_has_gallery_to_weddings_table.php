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
            if (!Schema::hasColumn('weddings', 'has_gallery')) {
                $table->boolean('has_gallery')->default(false)->after('template');
            }
            if (!Schema::hasColumn('weddings', 'bride_age')) {
                $table->integer('bride_age')->nullable()->after('bride_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn(['has_gallery', 'bride_age']);
        });
    }
};
