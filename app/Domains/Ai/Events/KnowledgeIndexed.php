<?php

declare(strict_types=1);

namespace App\Domains\Ai\Events;

use App\Models\AiKnowledgeSource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KnowledgeIndexed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AiKnowledgeSource $knowledgeSource,
    ) {
    }
}
