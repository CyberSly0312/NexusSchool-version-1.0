<?php

namespace App\Filament\Resources\SubjectCategoryResource\Pages;

use App\Filament\Resources\SubjectCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectCategories extends ListRecords
{
    protected static string $resource = SubjectCategoryResource::class;

    protected static ?string $title = 'Subject Categories';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
