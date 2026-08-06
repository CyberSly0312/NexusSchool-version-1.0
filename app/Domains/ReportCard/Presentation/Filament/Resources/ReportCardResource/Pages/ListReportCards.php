<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource\Pages;

use App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportCards extends ListRecords
{
    protected static string $resource = ReportCardResource::class;

    protected static ?string $title = 'Bulletins';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
