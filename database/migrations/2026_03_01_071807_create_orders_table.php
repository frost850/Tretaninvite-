<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('payment_token', 64)->nullable()->unique();
            $table->string('public_token', 32)->nullable()->unique();
            $table->index('public_token');

            $table->string('template');
            $table->enum('package', ['basic', 'premium'])->default('basic');
            $table->string('bride_name');
            $table->string('groom_name')->nullable();
            $table->date('event_date')->nullable();
            $table->string('location')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('notes')->nullable();

            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->enum('payment_status', ['belum_bayar', 'menunggu_konfirmasi', 'lunas'])->default('belum_bayar');
            $table->string('payment_proof')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('wedding_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
