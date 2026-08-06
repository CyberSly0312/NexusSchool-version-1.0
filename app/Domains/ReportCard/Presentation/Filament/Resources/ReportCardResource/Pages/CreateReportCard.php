<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource\Pages;

use App\Domains\ReportCard\Application\DTOs\CreateReportCardData;
use App\Domains\ReportCard\Application\ReportCardService;
use App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateReportCard extends CreateRecord
{
    protected static string $resource = ReportCardResource::class;

    protected static ?string $title = 'Nouveau bulletin';

    protected function handleRecordCreation(array $data): Model
    {
        $data['school_id'] = tenant()?->id;

        return app(ReportCardService::class)->createReportCard(
            CreateReportCardData::fromArray($data),
            auth()->user(),
        );
    }
}
