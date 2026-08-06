<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource\Pages;

use App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGradebook extends EditRecord
{
    protected static string $resource = GradebookResource::class;

    protected static ?string $title = 'Modifier le carnet de notes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
