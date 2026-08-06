<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Academic framework references (via contracts) ────────
            $table->unsignedBigInteger('academic_calendar_year_id')->nullable();
            $table->unsignedBigInteger('academic_calendar_term_id')->nullable();

            // ── Core data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();

            // ── State ─────────────────────────────────────────────────
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->unsignedInteger('total_entries')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // ── Ownership ─────────────────────────────────────────────
            $table->unsignedBigInteger('created_by')->nullable();

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('academic_calendar_year_id')->references('id')->on('academic_calendar_years')->cascadeOnDelete();
            $table->foreign('academic_calendar_term_id')->references('id')->on('academic_calendar_terms')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['school_id', 'code'])->whereNotNull('code');
            $table->index('status');
            $table->index(['school_id', 'status']);
        });

        // Enforce "only one published timetable per academic period" at the
        // database level where the driver supports partial indexes. On MySQL the
        // invariant is enforced by PublishTimetableAction instead.
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql', 'mariadb'], true)) {
            DB::statement(
                'CREATE UNIQUE INDEX timetables_single_published '
                . 'ON timetables (school_id, academic_calendar_year_id, academic_calendar_term_id) '
                . "WHERE status = 'published'"
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
