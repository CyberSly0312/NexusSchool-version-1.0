<?php

declare(strict_types=1);

namespace App\Domains\SubjectCategory\Events;

use App\Models\SubjectCategory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubjectCategoryUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly SubjectCategory $subjectCategory,
    ) {
    }
}
