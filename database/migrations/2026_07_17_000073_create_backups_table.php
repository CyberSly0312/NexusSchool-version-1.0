<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->nullable();

            $table->string('name', 200);
            $table->string('type', 30)->default('manual');
            $table->string('status', 30)->default('pending');
            $table->string('disk', 50)->default('local');
            $table->string('path')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum', 64)->nullable();

            $table->json('tables_included')->nullable();
            $table->text('notes')->nullable();
            $table->text('error')->nullable();

            $table->string('created_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
