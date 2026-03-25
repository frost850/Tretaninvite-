<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (!Schema::hasColumn('guests', 'phone')) $table->string('phone', 20)->nullable()->after('slug_name');
            if (!Schema::hasColumn('guests', 'email')) $table->string('email')->nullable()->after('phone');
            if (!Schema::hasColumn('guests', 'notes')) $table->text('notes')->nullable()->after('email');
            if (!Schema::hasColumn('guests', 'is_attending')) $table->boolean('is_attending')->nullable()->after('notes')->comment('RSVP: true=hadir, false=tidak');
            if (!Schema::hasColumn('guests', 'replied_at')) $table->timestamp('replied_at')->nullable()->after('is_attending');
            if (!Schema::hasColumn('guests', 'pax')) $table->unsignedTinyInteger('pax')->nullable()->after('replied_at')->comment('Jumlah orang');
            if (!Schema::hasColumn('guests', 'first_opened_at')) $table->timestamp('first_opened_at')->nullable()->after('pax');
            if (!Schema::hasColumn('guests', 'open_count')) $table->unsignedInteger('open_count')->default(0)->after('first_opened_at');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'email', 'notes', 'is_attending', 'replied_at',
                'pax', 'first_opened_at', 'open_count',
            ]);
        });
    }
};
