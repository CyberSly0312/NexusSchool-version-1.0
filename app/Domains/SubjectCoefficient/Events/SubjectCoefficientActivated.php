<?php

namespace App\Domains\SubjectCoefficient\Events;

use App\Models\SubjectCoefficient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubjectCoefficientActivated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly SubjectCoefficient $subjectCoefficient,
    ) {}
}
