<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('enrollment_history', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id');
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('event');
            $table->string('description', 255);
            $table->json('payload')->nullable();
            $table->string('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->dateTime('occurred_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'enrollment_id']);
            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_history');
    }
};
