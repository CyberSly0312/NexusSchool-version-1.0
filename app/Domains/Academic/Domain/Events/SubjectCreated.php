<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Events;

use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubjectCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicSubject $subject,
        public readonly ?User $actor = null,
    ) {
    }
}
