<?php

declare(strict_types=1);

namespace App\Filament\Resources\BookLoanResource\Pages;

use App\Filament\Resources\BookLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookLoan extends CreateRecord
{
    protected static string $resource = BookLoanResource::class;
}
