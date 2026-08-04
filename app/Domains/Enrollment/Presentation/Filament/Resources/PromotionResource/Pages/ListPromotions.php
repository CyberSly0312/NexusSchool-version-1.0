<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\PromotionResource\Pages;

use App\Domains\Enrollment\Presentation\Filament\Resources\PromotionResource;
use Filament\Resources\Pages\ListRecords;

class ListPromotions extends ListRecords
{
    protected static string $resource = PromotionResource::class;

    protected static ?string $title = 'Passages & redoublements';
}
