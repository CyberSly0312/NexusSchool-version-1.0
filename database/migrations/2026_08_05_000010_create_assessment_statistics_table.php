<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assessment_statistics', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->unsignedBigInteger('assessment_id');
            $table->unsignedInteger('total_scores')->default(0);
            $table->decimal('average', 8, 2)->default(0);
            $table->decimal('minimum', 8, 2)->default(0);
            $table->decimal('maximum', 8, 2)->default(0);
            $table->decimal('median', 8, 2)->default(0);
            $table->decimal('pass_rate', 8, 2)->default(0);
            $table->decimal('completion_rate', 8, 2)->default(0);

            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete();
            $table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'assessment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_statistics');
    }
};
