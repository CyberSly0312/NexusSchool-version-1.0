<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Promotes the legacy embedded pivot to a first-class relationship:
     * a `guardian_id` link to the Guardian aggregate, a configurable
     * relationship type and the business role flags. Soft-deleted rows keep
     * the (school_id, guardian_id, student_id) combination reusable, so the
     * unique index includes deleted_at.
     */
    public function up(): void
    {
        Schema::table('student_guardians', function (Blueprint $table) {
            $table->foreignId('relationship_type_id')->nullable()
                ->after('guardian_id')
                ->constrained('relationship_types')->nullOnDelete();

            $table->boolean('is_emergency_contact')->default(false)->after('is_primary');
            $table->boolean('is_authorized_pickup')->default(false)->after('is_emergency_contact');
            $table->boolean('is_financial_responsible')->default(false)->after('is_authorized_pickup');
            $table->boolean('is_notification_recipient')->default(false)->after('is_financial_responsible');
            $table->boolean('is_medical_contact')->default(false)->after('is_notification_recipient');

            $table->unsignedTinyInteger('emergency_contact_priority')->nullable()->after('is_medical_contact');

            $table->date('pickup_valid_from')->nullable()->after('emergency_contact_priority');
            $table->date('pickup_valid_until')->nullable()->after('pickup_valid_from');

            $table->date('relationship_start_date')->nullable()->after('pickup_valid_until');
            $table->date('relationship_end_date')->nullable()->after('relationship_start_date');

            $table->text('notes')->nullable()->after('relationship_end_date');

            $table->unique(['school_id', 'guardian_id', 'student_id', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('student_guardians', function (Blueprint $table) {
            $table->dropUnique(['school_id', 'guardian_id', 'student_id', 'deleted_at']);
            $table->dropConstrainedForeignId('relationship_type_id');
            $table->dropColumn([
                'is_emergency_contact',
                'is_authorized_pickup',
                'is_financial_responsible',
                'is_notification_recipient',
                'is_medical_contact',
                'emergency_contact_priority',
                'pickup_valid_from',
                'pickup_valid_until',
                'relationship_start_date',
                'relationship_end_date',
                'notes',
            ]);
        });
    }
};
