<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubjectCoefficientResource\Pages;

use App\Filament\Resources\SubjectCoefficientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubjectCoefficient extends EditRecord
{
    protected static string $resource = SubjectCoefficientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
