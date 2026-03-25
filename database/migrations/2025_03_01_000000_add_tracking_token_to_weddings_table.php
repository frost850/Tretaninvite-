<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('weddings', 'tracking_token')) {
            Schema::table('weddings', function (Blueprint $table) {
                $table->string('tracking_token', 32)->nullable()->unique()->after('template');
                $table->index('tracking_token');
            });

            // Generate tracking tokens for existing weddings
            DB::table('weddings')->whereNull('tracking_token')->get()->each(function ($wedding) {
                DB::table('weddings')
                    ->where('id', $wedding->id)
                    ->update(['tracking_token' => Str::random(32)]);
            });

            // Make tracking_token non-nullable
            Schema::table('weddings', function (Blueprint $table) {
                $table->string('tracking_token', 32)->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropIndex(['tracking_token']);
            $table->dropColumn('tracking_token');
        });
    }
};
