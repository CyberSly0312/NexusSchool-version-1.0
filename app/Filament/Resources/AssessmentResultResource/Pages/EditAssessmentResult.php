<?php

namespace App\Filament\Resources\AssessmentResultResource\Pages;

use App\Domains\Gradebook\Services\GradebookService;
use App\Filament\Resources\AssessmentResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAssessmentResult extends EditRecord
{
    protected static string $resource = AssessmentResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(GradebookService::class)->update($record->id, $data);
    }
}
