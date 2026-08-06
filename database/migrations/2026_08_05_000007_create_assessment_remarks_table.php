<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assessment_remarks', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('assessment_score_id')->nullable();
            $table->text('remark');
            $table->string('remark_type', 30)->default('general');

            $table->unsignedBigInteger('remark_by')->nullable();
            $table->timestamp('remark_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete();
            $table->foreign('assessment_score_id')->references('id')->on('assessment_scores')->cascadeOnDelete();
            $table->foreign('remark_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['assessment_id', 'assessment_score_id']);
            $table->index(['school_id', 'remark_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_remarks');
    }
};
