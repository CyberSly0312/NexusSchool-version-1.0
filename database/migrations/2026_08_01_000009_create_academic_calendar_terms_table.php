<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_calendar_terms', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('calendar_year_id')->constrained('academic_calendar_years')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status', 20)->default('draft');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'calendar_year_id', 'name']);
            $table->index('status');
            $table->index(['school_id', 'starts_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendar_terms');
    }
};
