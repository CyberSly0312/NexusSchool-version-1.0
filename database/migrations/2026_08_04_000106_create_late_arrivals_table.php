<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('late_arrivals', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('attendance_record_id');
            $table->unsignedSmallInteger('minutes');
            $table->dateTime('arrived_at');
            $table->unsignedBigInteger('recorded_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('attendance_record_id')->references('id')->on('attendance_session_records')->cascadeOnDelete();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['attendance_record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('late_arrivals');
    }
};
