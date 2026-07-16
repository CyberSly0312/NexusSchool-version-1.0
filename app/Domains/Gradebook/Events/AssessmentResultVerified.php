<?php

namespace App\Domains\Gradebook\Events;

use App\Models\AssessmentResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentResultVerified
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AssessmentResult $result,
    ) {}
}
