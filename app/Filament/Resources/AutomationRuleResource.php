<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Workflow\Enums\TriggerType;
use App\Filament\Resources\AutomationRuleResource\Pages;
use App\Models\AutomationRule;
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

class AutomationRuleResource extends Resource
{
    protected static ?string $model = AutomationRule::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|\UnitEnum|null $navigationGroup = 'Moteur de workflow';

    protected static ?string $navigationLabel = 'Règles d\'automatisation';

    protected static ?string $modelLabel = 'Règle d\'automatisation';

    protected static ?string $pluralModelLabel = 'Règles d\'automatisation';

    protected static ?int $navigationSort = 82;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la règle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('category')
                            ->options([
                                'notification' => 'Notification',
                                'data_sync' => 'Synchronisation des données',
                                'integration' => 'Intégration',
                                'maintenance' => 'Maintenance',
                            ])
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columns(1),

                SectionComponent::make('Configuration du déclencheur')
                    ->schema([
                        Forms\Components\Select::make('trigger_type')
                            ->options(TriggerType::labels())
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('event_name')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('trigger_type') === 'event'),
                        Forms\Components\KeyValue::make('conditions')
                            ->label('Conditions')                            ->reorderable(),
                    ])
                    ->columns(2),

                SectionComponent::make('Actions')
                    ->schema([
                        Forms\Components\Repeater::make('actions')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'notify' => 'Envoyer notification',
                                        'execute_workflow' => 'Exécuter le flux',
                                        'update_record' => 'Mettre à jour',
                                        'log' => 'Journaliser',
                                    ])
                                    ->required(),
                                Forms\Components\KeyValue::make('parameters')
                                    ->label('Paramètres')
                                    ->reorderable(),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Ajouter une action'),
                    ]),

                SectionComponent::make('Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\TextInput::make('priority')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('cooldown_seconds')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('trigger_type')
                    ->badge()
                    ->color(fn (string $state): string => TriggerType::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('category')
                    ->badge()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('execution_count')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('priority')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('last_executed_at')
                    ->dateTime('d/m/Y H:i')
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
                SelectFilter::make('trigger_type')
                    ->options(TriggerType::labels()),
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
            'index' => Pages\ListAutomationRules::route('/'),
            'create' => Pages\CreateAutomationRule::route('/create'),
            'edit' => Pages\EditAutomationRule::route('/{record}/edit'),
        ];
    }
}
