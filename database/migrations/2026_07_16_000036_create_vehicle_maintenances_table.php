<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('type', 36);
            $table->text('description');
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('service_provider', 255)->nullable();
            $table->date('service_date');
            $table->date('next_service_date')->nullable();
            $table->unsignedInteger('odometer_reading')->nullable();
            $table->string('receipt_document', 255)->nullable();
            $table->string('status', 36)->default('scheduled');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('vehicle_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
};
