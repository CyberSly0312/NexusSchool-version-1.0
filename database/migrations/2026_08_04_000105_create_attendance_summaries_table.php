<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('enrollment_id');
            $table->string('period_type', 20);
            $table->date('period_start');
            $table->date('period_end');

            $table->unsignedInteger('sessions_count')->default(0);
            $table->unsignedInteger('attended_count')->default(0);
            $table->unsignedInteger('present_count')->default(0);
            $table->unsignedInteger('late_count')->default(0);
            $table->unsignedInteger('excused_count')->default(0);
            $table->unsignedInteger('unexcused_count')->default(0);
            $table->unsignedInteger('absent_count')->default(0);

            $table->float('attendance_percentage')->default(0);
            $table->float('late_percentage')->default(0);
            $table->unsignedInteger('consecutive_absences')->default(0);
            $table->unsignedInteger('consecutive_lates')->default(0);
            $table->float('risk_score')->default(0);
            $table->float('trend')->default(0);
            $table->json('breakdown')->nullable();

            $table->timestamp('calculated_at');
            $table->unsignedBigInteger('calculated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('calculated_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['enrollment_id', 'period_type', 'period_start', 'period_end'], 'attendance_summaries_period_unique');
            $table->index(['school_id', 'period_type', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_summaries');
    }
};
