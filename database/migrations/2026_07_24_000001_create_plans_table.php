<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->default('XOF');
            $table->string('billing_period')->default('monthly'); // monthly, yearly
            $table->integer('max_schools')->default(1);
            $table->integer('max_students')->default(50);
            $table->integer('max_teachers')->default(10);
            $table->integer('max_users')->default(20);
            $table->string('storage_limit')->default('1GB');
            $table->json('modules')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
