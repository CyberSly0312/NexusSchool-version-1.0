<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_categories', function (Blueprint $table) {
            $table->unique(['school_id', 'display_order'], 'subject_categories_school_display_order_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subject_categories', function (Blueprint $table) {
            $table->dropIndex('subject_categories_school_display_order_unique');
        });
    }
};
