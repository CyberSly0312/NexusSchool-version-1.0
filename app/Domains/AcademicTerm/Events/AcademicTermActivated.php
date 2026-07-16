<?php

namespace App\Domains\AcademicTerm\Events;

use App\Models\AcademicTerm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicTermActivated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AcademicTerm $academicTerm,
    ) {}
}
