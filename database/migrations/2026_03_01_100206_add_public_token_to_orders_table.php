<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'public_token')) return;

        Schema::table('orders', function (Blueprint $table) {
            $table->string('public_token', 32)->nullable()->unique()->after('payment_token');
            $table->index('public_token');
        });

        // Backfill existing orders with random tokens
        DB::table('orders')->whereNull('public_token')->get()->each(function ($order) {
            DB::table('orders')
                ->where('id', $order->id)
                ->update(['public_token' => Str::random(32)]);
        });

        // Make non-nullable after backfill
        Schema::table('orders', function (Blueprint $table) {
            $table->string('public_token', 32)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
