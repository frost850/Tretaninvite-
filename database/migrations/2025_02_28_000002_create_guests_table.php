<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained()->cascadeOnDelete();
            $table->string('guest_name');
            $table->string('group_name', 100)->nullable()->comment('Grup/keluarga tamu');
            $table->string('slug_name')->nullable()->comment('Kode unik untuk ?to=');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_attending')->nullable()->comment('RSVP: true=hadir, false=tidak');
            $table->timestamp('replied_at')->nullable();
            $table->unsignedTinyInteger('pax')->nullable()->comment('Jumlah orang');
            $table->timestamp('first_opened_at')->nullable();
            $table->unsignedInteger('open_count')->default(0);
            $table->timestamps();
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->index(['wedding_id', 'slug_name']);
            $table->index(['wedding_id', 'guest_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
