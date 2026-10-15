<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('attendance_alerts', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->unsignedBigInteger('attendance_session_id')->nullable();

            $table->string('alert_type', 40);
            $table->string('severity', 20)->default('low');
            $table->float('threshold')->nullable();
            $table->float('observed_value')->nullable();
            $table->json('payload')->nullable();

            $table->dateTime('detected_at');
            $table->string('status', 20)->default('open');
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('attendance_session_id')->references('id')->on('attendance_sessions')->cascadeOnDelete();
            $table->foreign('acknowledged_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'enrollment_id', 'detected_at']);
            $table->index(['school_id', 'alert_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_alerts');
    }
};
