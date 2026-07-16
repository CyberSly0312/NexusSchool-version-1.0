<?php

use App\Domains\Timetable\Enums\SessionType;
use App\Domains\Timetable\Enums\TimetableEntryStatus;
use App\Domains\Timetable\Enums\DayOfWeek;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('school_id');
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('academic_term_id')->nullable()->constrained();
            $table->foreignId('academic_class_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('classroom_id')->constrained();
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('period_number')->nullable();
            $table->string('session_type');
            $table->string('status');
            $table->string('version')->nullable();
            $table->boolean('is_override')->default(false);
            $table->string('override_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();

            $table->index('school_id');
            $table->index('academic_year_id');
            $table->index('academic_class_id');
            $table->index('user_id');
            $table->index('subject_id');
            $table->index('classroom_id');
            $table->index('day_of_week');
            $table->index('status');
            $table->index(['user_id', 'day_of_week']);
            $table->index(['classroom_id', 'day_of_week']);
            $table->index(['academic_class_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
