<?php

declare(strict_types=1);

namespace App\Domains\Ai\Events;

use App\Models\AiPromptTemplate;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PromptTemplateUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly AiPromptTemplate $template,
        public readonly int $previousVersion,
    ) {
    }
}
