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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email');
            $table->string('otp_token')->nullable()->after('role');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_token');
            $table->boolean('must_change_password')->default(false)->after('otp_expires_at');
            $table->boolean('is_active')->default(true)->after('must_change_password');
            $table->string('added_by')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('added_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'otp_token', 'otp_expires_at', 'must_change_password', 'is_active', 'added_by', 'last_login_at']);
        });
    }
};
