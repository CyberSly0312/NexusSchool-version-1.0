<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assessment_templates', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->string('name');
            $table->unsignedBigInteger('assessment_type_id');
            $table->text('description')->nullable();
            $table->decimal('default_maximum_score', 8, 2)->default(20);
            $table->decimal('default_passing_score', 8, 2)->default(10);
            $table->decimal('default_weight', 5, 2)->default(1);
            $table->json('structure')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('assessment_type_id')->references('id')->on('assessment_types')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_templates');
    }
};
