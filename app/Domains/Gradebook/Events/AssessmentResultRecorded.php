<?php

namespace App\Domains\Gradebook\Events;

use App\Models\AssessmentResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentResultRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AssessmentResult $result,
    ) {}
}
