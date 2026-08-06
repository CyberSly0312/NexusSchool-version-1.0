<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('timetable_versions', function (Blueprint $table) {
            // ── Identity ──────────────────────────────────────────────
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            // ── Aggregate reference ───────────────────────────────────
            $table->unsignedBigInteger('timetable_id');

            // ── Snapshot ──────────────────────────────────────────────
            $table->unsignedInteger('version');
            $table->string('status', 20);
            $table->longText('snapshot');
            $table->text('reason')->nullable();

            // ── Ownership ─────────────────────────────────────────────
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('timetable_id');
            $table->unique(['timetable_id', 'version']);
            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('timetable_id')->references('id')->on('timetables')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_versions');
    }
};
