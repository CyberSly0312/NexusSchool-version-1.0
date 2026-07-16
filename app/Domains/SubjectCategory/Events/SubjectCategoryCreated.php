<?php

namespace App\Domains\SubjectCategory\Events;

use App\Models\SubjectCategory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubjectCategoryCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly SubjectCategory $subjectCategory,
    ) {}
}
