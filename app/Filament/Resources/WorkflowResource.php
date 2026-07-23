<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Workflow\Enums\WorkflowStatus;
use App\Filament\Resources\WorkflowResource\Pages;
use App\Models\Workflow;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|\UnitEnum|null $navigationGroup = 'Moteur de workflow';

    protected static ?string $navigationLabel = 'Flux de travail';

    protected static ?string $modelLabel = 'Flux de travail';

    protected static ?string $pluralModelLabel = 'Flux de travail';

    protected static ?int $navigationSort = 80;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails du flux de travail')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('status')
                            ->options(WorkflowStatus::labels())
                            ->default('draft'),
                        Forms\Components\Select::make('category')
                            ->options([
                                'approval' => 'Approbation',
                                'notification' => 'Notification',
                                'automation' => 'Automatisation',
                                'integration' => 'Intégration',
                            ])
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columns(3),

                SectionComponent::make('Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\TextInput::make('priority')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])
                    ->columns(2),

                SectionComponent::make('Paramètres')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('Paramètres du flux')
                            ->reorderable(),
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Métadonnées')
                            ->reorderable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => WorkflowStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('category')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('current_version')
                    ->label('Version')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('executions_count')
                    ->counts('executions')
                    ->label('Exécutions')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(WorkflowStatus::labels()),
                SelectFilter::make('category')
                    ->options([
                        'approval' => 'Approbation',
                        'notification' => 'Notification',
                        'automation' => 'Automatisation',
                        'integration' => 'Intégration',
                    ]),
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Actif',
                        '0' => 'Inactif',
                    ]),
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
            'index' => Pages\ListWorkflows::route('/'),
            'create' => Pages\CreateWorkflow::route('/create'),
            'edit' => Pages\EditWorkflow::route('/{record}/edit'),
        ];
    }
}
