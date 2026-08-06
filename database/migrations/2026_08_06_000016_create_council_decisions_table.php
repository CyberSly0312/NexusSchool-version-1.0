<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('council_decisions', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('council_session_id');
            $table->unsignedBigInteger('student_id')->nullable();

            $table->string('decision_type', 30);
            $table->string('category', 30);
            $table->string('code', 64);
            $table->string('label', 255);
            $table->text('summary')->nullable();
            $table->text('recommendation')->nullable();
            $table->date('decision_date');

            $table->boolean('is_override')->default(false);
            $table->string('automatic_decision_type', 30)->nullable();
            $table->string('automatic_decision_label', 255)->nullable();
            $table->text('justification')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('council_session_id')->references('id')->on('council_sessions')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['school_id', 'code'], 'council_decisions_code_unique');
            $table->index(['council_session_id']);
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_decisions');
    }
};
