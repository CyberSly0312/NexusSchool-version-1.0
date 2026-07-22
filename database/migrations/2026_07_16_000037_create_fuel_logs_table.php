<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignUuid('driver_id')->nullable()->constrained('drivers')->cascadeOnDelete();
            $table->decimal('liters', 8, 2);
            $table->decimal('cost_per_liter', 8, 2);
            $table->decimal('total_cost', 10, 2);
            $table->string('fuel_type', 50);
            $table->string('fuel_station', 255)->nullable();
            $table->unsignedInteger('odometer_reading');
            $table->date('fuel_date');
            $table->string('receipt_document', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('vehicle_id');
            $table->index('fuel_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
