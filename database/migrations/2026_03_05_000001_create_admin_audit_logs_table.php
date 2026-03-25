<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_email', 255)->index();
            $table->string('actor_type', 20)->default('sub_admin'); // super_admin | sub_admin
            $table->string('action', 100)->index();
            $table->string('target_type', 100)->nullable();
            $table->string('target_id', 100)->nullable();
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            // No updated_at — audit logs are append-only
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};
