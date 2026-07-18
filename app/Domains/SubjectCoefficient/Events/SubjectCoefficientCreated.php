<?php

declare(strict_types=1);

namespace App\Domains\SubjectCoefficient\Events;

use App\Models\SubjectCoefficient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubjectCoefficientCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly SubjectCoefficient $subjectCoefficient,
    ) {
    }
}
