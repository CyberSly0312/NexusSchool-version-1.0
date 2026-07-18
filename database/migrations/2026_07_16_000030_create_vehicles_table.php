<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('registration_number', 255);
            $table->string('vin', 17);
            $table->string('manufacturer', 100);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year');
            $table->string('color', 50)->nullable();
            $table->unsignedSmallInteger('capacity');
            $table->string('fuel_type', 50);
            $table->date('insurance_expiry')->nullable();
            $table->date('inspection_expiry')->nullable();
            $table->string('insurance_document', 255)->nullable();
            $table->string('status', 36)->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('registration_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
