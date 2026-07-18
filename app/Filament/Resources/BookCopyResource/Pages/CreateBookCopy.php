<?php

declare(strict_types=1);

namespace App\Filament\Resources\BookCopyResource\Pages;

use App\Filament\Resources\BookCopyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookCopy extends CreateRecord
{
    protected static string $resource = BookCopyResource::class;
}
