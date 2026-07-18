<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->nullable();

            $table->string('group', 100);
            $table->string('key', 200);
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_readonly')->default(false);
            $table->boolean('is_system')->default(false);
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'group']);
            $table->index(['school_id', 'key']);
            $table->unique(['school_id', 'group', 'key'], 'system_settings_school_group_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
