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
            $table->string('school_id', 36);
            $table->string('source_domain', 100);
            $table->string('source_type', 100);
            $table->string('source_id', 100);
            $table->string('source_version', 50)->nullable();
            $table->string('lifecycle_state', 50)->default('registered');
            $table->json('academic_context')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('origin_event', 100)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'source_domain', 'source_type', 'source_id'], 'record_lifecycles_school_source_index');
            $table->index(['school_id', 'lifecycle_state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_lifecycles');
    }
};
