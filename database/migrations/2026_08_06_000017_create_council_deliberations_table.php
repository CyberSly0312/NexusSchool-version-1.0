<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('council_deliberations', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('council_session_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('council_decision_id')->nullable();

            $table->string('member', 255)->nullable();
            $table->string('topic', 255)->default('Général');
            $table->text('summary');
            $table->dateTime('noted_at');

            $table->unsignedBigInteger('recorded_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('council_session_id')->references('id')->on('council_sessions')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
            $table->foreign('council_decision_id')->references('id')->on('council_decisions')->nullOnDelete();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['council_session_id']);
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_deliberations');
    }
};
