<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Append-only business timeline of a student–guardian relationship.
     * Rows are never updated nor hard-deleted: relationship history is
     * immutable once written (GDPR soft-delete is the only exception).
     */
    public function up(): void
    {
        Schema::create('student_guardian_relationship_history', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->foreignId('student_guardian_id')->constrained('student_guardians')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('guardian_id')->nullable()->constrained('guardians')->nullOnDelete();

            $table->string('event', 50);
            $table->string('description', 255);
            $table->json('payload')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type', 255)->nullable();
            $table->timestamp('occurred_at');

            $table->index(['student_guardian_id', 'occurred_at']);
            $table->index(['school_id', 'student_guardian_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_guardian_relationship_history');
    }
};
