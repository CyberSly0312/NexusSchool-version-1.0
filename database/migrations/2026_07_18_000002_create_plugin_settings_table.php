<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('plugin_id');
            $table->uuid('school_id')->nullable();
            $table->string('key');
            $table->json('value')->nullable();
            $table->string('type')->default('text');
            $table->timestamps();

            $table->foreign('plugin_id')->references('id')->on('plugins')->cascadeOnDelete();
            $table->unique(['plugin_id', 'school_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_settings');
    }
};
