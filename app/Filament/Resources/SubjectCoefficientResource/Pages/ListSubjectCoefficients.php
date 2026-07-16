<?php

namespace App\Filament\Resources\SubjectCoefficientResource\Pages;

use App\Filament\Resources\SubjectCoefficientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectCoefficients extends ListRecords
{
    protected static string $resource = SubjectCoefficientResource::class;

    protected static ?string $title = 'Subject Coefficients';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
