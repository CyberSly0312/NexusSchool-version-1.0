<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAcademicDocument extends ViewRecord
{
    protected static string $resource = AcademicDocumentResource::class;

    protected static ?string $title = 'Détail du document';

    public function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Infolists\Components\TextEntry::make('document_number')->label('N° de document'),
                \Filament\Infolists\Components\TextEntry::make('title')->label('Titre'),
                \Filament\Infolists\Components\TextEntry::make('document_type')
                    ->label('Type')
                    ->badge(),
                \Filament\Infolists\Components\TextEntry::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($record): string => $record->status->color()),
                \Filament\Infolists\Components\TextEntry::make('content_version')->label('Version'),
            ]);
    }
}
