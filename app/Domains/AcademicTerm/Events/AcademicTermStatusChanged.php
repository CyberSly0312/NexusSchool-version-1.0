<?php

namespace App\Domains\AcademicTerm\Events;

use App\Domains\AcademicTerm\Enums\AcademicTermStatus;
use App\Models\AcademicTerm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicTermStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AcademicTerm $academicTerm,
        public readonly AcademicTermStatus $oldStatus,
        public readonly AcademicTermStatus $newStatus,
    ) {}
}
