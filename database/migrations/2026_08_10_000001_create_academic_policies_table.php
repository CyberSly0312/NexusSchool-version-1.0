<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('academic_policies', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->string('key', 100);
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->string('value_type', 20);
            $table->json('value');
            $table->string('scope', 30)->default('school');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_calendar_years')->nullOnDelete();
            $table->foreign('academic_subject_id')->references('id')->on('academic_subjects')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['school_id', 'key', 'scope'], 'academic_policies_school_key_scope_unique');
            $table->index(['school_id', 'is_active']);
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['school_id', 'academic_subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_policies');
    }
};
