<?php

namespace App\Filament\Resources\SubjectCategoryResource\Pages;

use App\Filament\Resources\SubjectCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubjectCategory extends EditRecord
{
    protected static string $resource = SubjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
