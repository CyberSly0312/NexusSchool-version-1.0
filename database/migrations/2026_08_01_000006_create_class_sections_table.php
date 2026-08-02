<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status', 20)->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'school_class_id', 'name']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sections');
    }
};
