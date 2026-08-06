<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Events;

use App\Domains\AcademicCommunication\Domain\ValueObjects\DocumentNumber;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AcademicDocument $document,
        public readonly ?User $actor = null,
    ) {
    }

    public function number(): string
    {
        return DocumentNumber::fromString($this->document->document_number)->toString();
    }
}
