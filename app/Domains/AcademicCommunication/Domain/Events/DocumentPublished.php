<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Events;

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicDocument $document,
        public readonly AcademicDocumentVersion $version,
        public readonly ?User $actor = null,
    ) {
    }
}
