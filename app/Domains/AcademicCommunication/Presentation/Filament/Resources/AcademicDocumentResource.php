<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources;

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Filament resource for the aggregate root of the Academic Communication
 * Engine (SPR-012). Read-only surface: documents are created through the
 * generation actions and are immutable once published — no create/edit/delete
 * here.
 */
class AcademicDocumentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AcademicDocument::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Documents académiques';

    protected static ?string $modelLabel = 'Document académique';

    protected static ?string $pluralModelLabel = 'Documents académiques';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Document')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('document_number')->label('N° de document')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('title')->label('Titre')
                            ->disabled(),
                        \Filament\Forms\Components\Select::make('document_type')->label('Type')
                            ->options(\App\Domains\AcademicCommunication\Domain\Enums\DocumentType::options())
                            ->disabled(),
                        \Filament\Forms\Components\Select::make('status')->label('Statut')
                            ->options(\App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus::options())
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('document_number')
                    ->label('N° de document')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($record): string => $record->status->color())
                    ->sortable(),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Approbation')
                    ->badge(),
                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Généré le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicDocuments::route('/'),
            'view' => Pages\ViewAcademicDocument::route('/{record}'),
        ];
    }
}
