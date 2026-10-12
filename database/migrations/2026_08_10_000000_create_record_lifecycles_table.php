<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('record_lifecycles', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('school_id');
            $table->string('source_domain');
            $table->string('source_type');
            $table->string('source_id');
            $table->string('source_version')->nullable();
            $table->string('lifecycle_state')->default('registered');
            $table->json('academic_context')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('origin_event')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'source_domain', 'source_type', 'source_id']);
            $table->index(['school_id', 'lifecycle_state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_lifecycles');
    }
};
