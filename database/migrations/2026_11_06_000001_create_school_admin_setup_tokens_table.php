<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('school_admin_setup_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('school_id');
            $table->foreignId('user_id');
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_admin_setup_tokens');
    }
};
