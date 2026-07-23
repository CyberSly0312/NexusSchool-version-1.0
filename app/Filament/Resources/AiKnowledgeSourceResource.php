<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Ai\Enums\AiKnowledgeSourceStatus;
use App\Filament\Resources\AiKnowledgeSourceResource\Pages;
use App\Models\AiKnowledgeSource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiKnowledgeSourceResource extends Resource
{
    protected static ?string $model = AiKnowledgeSource::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme IA';

    protected static ?string $navigationLabel = 'Sources de connaissances IA';

    protected static ?string $modelLabel = 'Source de connaissances IA';

    protected static ?string $pluralModelLabel = 'Sources de connaissances IA';

    protected static ?int $navigationSort = 73;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la source de connaissances')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'markdown' => 'Markdown',
                                'pdf_metadata' => 'Métadonnées PDF',
                                'database' => 'Base de données',
                                'text' => 'Texte brut',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])
                    ->columns(1),

                SectionComponent::make('Configuration de la source')
                    ->schema([
                        Forms\Components\TextInput::make('source_path')
                            ->maxLength(500),
                        Forms\Components\TextInput::make('source_type')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('content_type')
                            ->maxLength(100),
                        Forms\Components\KeyValue::make('configuration')
                            ->label('Configuration'),
                    ])
                    ->columns(2),

                SectionComponent::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(AiKnowledgeSourceStatus::labels())
                            ->default('pending'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (AiKnowledgeSourceStatus $state): string => $state->color()),
                TextColumn::make('version')
                    ->sortable(),
                TextColumn::make('chunk_count')
                    ->label('Segments')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('total_tokens')
                    ->label('Jetons')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('indexed_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'markdown' => 'Markdown',
                        'pdf_metadata' => 'Métadonnées PDF',
                        'database' => 'Base de données',
                        'text' => 'Texte brut',
                    ]),
                SelectFilter::make('status')
                    ->options(AiKnowledgeSourceStatus::labels()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiKnowledgeSources::route('/'),
            'create' => Pages\CreateAiKnowledgeSource::route('/create'),
            'edit' => Pages\EditAiKnowledgeSource::route('/{record}/edit'),
        ];
    }
}
