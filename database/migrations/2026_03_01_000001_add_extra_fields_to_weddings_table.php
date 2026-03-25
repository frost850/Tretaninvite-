<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            // Mempelai wanita
            if (!Schema::hasColumn('weddings', 'bride_parent')) $table->string('bride_parent')->nullable()->after('bride_name');
            if (!Schema::hasColumn('weddings', 'bride_ig')) $table->string('bride_ig')->nullable()->after('bride_parent');
            if (!Schema::hasColumn('weddings', 'bride_bank')) $table->string('bride_bank')->nullable()->after('bride_ig');
            if (!Schema::hasColumn('weddings', 'bride_norek')) $table->string('bride_norek')->nullable()->after('bride_bank');

            // Mempelai pria
            if (!Schema::hasColumn('weddings', 'groom_parent')) $table->string('groom_parent')->nullable()->after('groom_name');
            if (!Schema::hasColumn('weddings', 'groom_ig')) $table->string('groom_ig')->nullable()->after('groom_parent');
            if (!Schema::hasColumn('weddings', 'groom_bank')) $table->string('groom_bank')->nullable()->after('groom_ig');
            if (!Schema::hasColumn('weddings', 'groom_norek')) $table->string('groom_norek')->nullable()->after('groom_bank');

            // Akad
            if (!Schema::hasColumn('weddings', 'akad_date')) $table->date('akad_date')->nullable()->after('event_date');
            if (!Schema::hasColumn('weddings', 'akad_time')) $table->string('akad_time')->nullable()->after('akad_date');
            if (!Schema::hasColumn('weddings', 'akad_location')) $table->string('akad_location')->nullable()->after('akad_time');

            // Resepsi
            if (!Schema::hasColumn('weddings', 'reception_date')) $table->date('reception_date')->nullable()->after('akad_location');
            if (!Schema::hasColumn('weddings', 'reception_time')) $table->string('reception_time')->nullable()->after('reception_date');
            if (!Schema::hasColumn('weddings', 'reception_location')) $table->string('reception_location')->nullable()->after('reception_time');

            // Lokasi & detail
            if (!Schema::hasColumn('weddings', 'map_embed')) $table->text('map_embed')->nullable()->after('map_link');
            if (!Schema::hasColumn('weddings', 'dresscode')) $table->string('dresscode')->nullable()->after('map_embed');
            if (!Schema::hasColumn('weddings', 'opening_text')) $table->text('opening_text')->nullable()->after('dresscode');
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn([
                'bride_parent', 'bride_ig', 'bride_bank', 'bride_norek',
                'groom_parent', 'groom_ig', 'groom_bank', 'groom_norek',
                'akad_date', 'akad_time', 'akad_location',
                'reception_date', 'reception_time', 'reception_location',
                'map_embed', 'dresscode', 'opening_text',
            ]);
        });
    }
};
