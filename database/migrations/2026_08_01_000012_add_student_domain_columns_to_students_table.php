<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // ── Academic assignment (SPR-002 dependency) ──────────────
            $table->foreignId('academic_calendar_year_id')
                ->nullable()
                ->after('admission_date')
                ->constrained('academic_calendar_years')
                ->restrictOnDelete();
            $table->foreignId('school_class_id')
                ->nullable()
                ->after('academic_calendar_year_id')
                ->constrained('school_classes')
                ->restrictOnDelete();
            $table->foreignId('class_section_id')
                ->nullable()
                ->after('school_class_id')
                ->constrained('class_sections')
                ->nullOnDelete();

            // ── Identity extensions ───────────────────────────────────
            $table->string('birth_certificate_reference', 100)
                ->nullable()
                ->after('national_id');

            // ── Structured address (legacy residential_address kept) ──
            $table->string('address_street', 255)->nullable();
            $table->string('address_city', 100)->nullable();
            $table->string('address_state', 100)->nullable();
            $table->string('address_postal_code', 20)->nullable();
            $table->string('address_country', 100)->nullable();

            // ── Emergency contact ─────────────────────────────────────
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable();

            // ── Lifecycle (audit trail of state transitions) ──────────
            $table->string('transfer_status', 20)->default('none')->after('status');
            $table->timestamp('transferred_at')->nullable();
            $table->string('transfer_from_school', 200)->nullable();
            $table->string('transfer_to_school', 200)->nullable();
            $table->date('graduated_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->date('suspended_until')->nullable();
            $table->string('suspension_reason', 255)->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->string('withdrawal_reason', 255)->nullable();
            $table->timestamp('reactivated_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // ── Constraints ───────────────────────────────────────────
            $table->unique(['school_id', 'email']);
            $table->index(['school_id', 'academic_calendar_year_id']);
            $table->index(['school_id', 'school_class_id']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['school_id', 'email']);
            $table->dropIndex(['school_id', 'academic_calendar_year_id']);
            $table->dropIndex(['school_id', 'school_class_id']);

            $table->dropForeign(['class_section_id']);
            $table->dropForeign(['school_class_id']);
            $table->dropForeign(['academic_calendar_year_id']);

            $table->dropColumn([
                'academic_calendar_year_id',
                'school_class_id',
                'class_section_id',
                'birth_certificate_reference',
                'address_street',
                'address_city',
                'address_state',
                'address_postal_code',
                'address_country',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'transfer_status',
                'transferred_at',
                'transfer_from_school',
                'transfer_to_school',
                'graduated_at',
                'suspended_at',
                'suspended_until',
                'suspension_reason',
                'withdrawn_at',
                'withdrawal_reason',
                'reactivated_at',
                'archived_at',
            ]);
        });
    }
};
