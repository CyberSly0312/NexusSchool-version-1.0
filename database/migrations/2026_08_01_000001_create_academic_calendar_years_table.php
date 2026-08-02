<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_calendar_years', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            // ── Core Data ─────────────────────────────────────────────
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();

            // ── Status ────────────────────────────────────────────────
            $table->string('status', 20)->default('draft');

            // ── Timestamps ────────────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Constraints ───────────────────────────────────────────
            $table->unique(['school_id', 'name']);
            $table->index('status');
            $table->index(['school_id', 'start_date']);
        });

        // Enforce "only one active year per school" at the database level where the
        // underlying driver supports partial (filtered) indexes. On MySQL (which does
        // not), the invariant is enforced by the CalendarService instead.
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql', 'mariadb'], true)) {
            DB::statement(
                "CREATE UNIQUE INDEX academic_calendar_years_single_active ON academic_calendar_years (school_id) WHERE status = 'active'"
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendar_years');
    }
};
