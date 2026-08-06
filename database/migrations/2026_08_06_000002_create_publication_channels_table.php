<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('publication_channels', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique()->nullable();
            $table->string('school_id', 36);

            $table->string('key', 40);
            $table->string('label')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->json('config')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique(['school_id', 'key']);
            $table->index(['school_id', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_channels');
    }
};
