<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('attendance_record_id');
            $table->string('previous_status', 30);
            $table->string('new_status', 30);
            $table->string('reason_type', 30);
            $table->string('reason_text', 500)->nullable();

            $table->boolean('is_authorized')->default(false);
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('authorized_by')->nullable();
            $table->timestamp('authorized_at')->nullable();

            $table->timestamp('corrected_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('attendance_record_id')->references('id')->on('attendance_session_records')->cascadeOnDelete();
            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('authorized_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['attendance_record_id', 'corrected_at']);
            $table->index(['school_id', 'authorized_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
