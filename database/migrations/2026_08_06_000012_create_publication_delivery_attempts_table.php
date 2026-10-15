<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('publication_delivery_attempts', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('publication_record_id');
            $table->string('status', 30)->default('queued');
            $table->text('error')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('publication_record_id')->references('id')->on('publication_records')->cascadeOnDelete();

            $table->index(['school_id', 'publication_record_id', 'status'], 'pda_school_record_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_delivery_attempts');
    }
};
