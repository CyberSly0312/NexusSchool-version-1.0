<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('calendar_year_id')->constrained('academic_calendar_years')->cascadeOnDelete();
            $table->foreignId('calendar_term_id')->nullable()->constrained('academic_calendar_terms')->nullOnDelete();
            $table->string('title', 150);
            $table->string('type', 30)->default('event');
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->boolean('is_all_day')->default(true);
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'starts_on']);
            $table->index(['school_id', 'calendar_year_id']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendar_events');
    }
};
