<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('guardian_id')->nullable()->constrained('guardians')->nullOnDelete();

            // Embedded guardian reference (self-contained; a linked Guardian
            // record may be attached later via guardian_id).
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('relationship', 50);
            $table->boolean('is_primary')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'is_primary']);
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_guardians');
    }
};
