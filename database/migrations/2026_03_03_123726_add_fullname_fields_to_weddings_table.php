<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            // Nama lengkap (formal) mempelai — tampil di undangan VIP Royal
            if (!Schema::hasColumn('weddings', 'bride_fullname'))
                $table->string('bride_fullname', 150)->nullable()->after('bride_name');
            if (!Schema::hasColumn('weddings', 'bride_father'))
                $table->string('bride_father', 100)->nullable()->after('bride_fullname');
            if (!Schema::hasColumn('weddings', 'bride_mother'))
                $table->string('bride_mother', 100)->nullable()->after('bride_father');

            if (!Schema::hasColumn('weddings', 'groom_fullname'))
                $table->string('groom_fullname', 150)->nullable()->after('groom_name');
            if (!Schema::hasColumn('weddings', 'groom_father'))
                $table->string('groom_father', 100)->nullable()->after('groom_fullname');
            if (!Schema::hasColumn('weddings', 'groom_mother'))
                $table->string('groom_mother', 100)->nullable()->after('groom_father');
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn([
                'bride_fullname', 'bride_father', 'bride_mother',
                'groom_fullname', 'groom_father', 'groom_mother',
            ]);
        });
    }
};
