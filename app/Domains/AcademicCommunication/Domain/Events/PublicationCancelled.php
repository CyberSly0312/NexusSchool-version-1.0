<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Events;

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PublicationCancelled
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicDocument $document,
        public readonly PublicationRecord $record,
        public readonly ?string $reason = null,
        public readonly ?User $actor = null,
    ) {
    }
}
