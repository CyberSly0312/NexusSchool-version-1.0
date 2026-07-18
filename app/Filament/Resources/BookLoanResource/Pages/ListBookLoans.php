<?php

declare(strict_types=1);

namespace App\Filament\Resources\BookLoanResource\Pages;

use App\Filament\Resources\BookLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookLoans extends ListRecords
{
    protected static string $resource = BookLoanResource::class;

    protected static ?string $title = 'Book Loans';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
