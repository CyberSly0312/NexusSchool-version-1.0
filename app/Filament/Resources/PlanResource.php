<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme';

    protected static ?string $navigationLabel = 'Formules';

    protected static ?string $modelLabel = 'Formule';

    protected static ?string $pluralModelLabel = 'Formules';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations générales')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3),
                ])->columns(2),

            Section::make('Tarification')
                ->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Prix')
                        ->numeric()
                        ->prefix('FCFA')
                        ->required()
                        ->default(0),
                    Forms\Components\Select::make('billing_period')
                        ->label('Période de facturation')
                        ->options([
                            'monthly' => 'Mensuel',
                            'yearly' => 'Annuel',
                        ])
                        ->required()
                        ->default('monthly'),
                ])->columns(2),

            Section::make('Limites')
                ->schema([
                    Forms\Components\TextInput::make('max_schools')
                        ->label('Max écoles')
                        ->numeric()
                        ->default(1)
                        ->minValue(1),
                    Forms\Components\TextInput::make('max_students')
                        ->label('Max élèves')
                        ->numeric()
                        ->default(50)
                        ->minValue(1),
                    Forms\Components\TextInput::make('max_teachers')
                        ->label('Max enseignants')
                        ->numeric()
                        ->default(10)
                        ->minValue(1),
                    Forms\Components\TextInput::make('max_users')
                        ->label('Max utilisateurs')
                        ->numeric()
                        ->default(20)
                        ->minValue(1),
                    Forms\Components\TextInput::make('storage_limit')
                        ->label('Limite stockage')
                        ->default('1GB')
                        ->maxLength(20),
                ])->columns(3),

            Section::make('Modules inclus')
                ->schema([
                    Forms\Components\Repeater::make('modules')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nom du module')
                                ->required(),
                            Forms\Components\Toggle::make('enabled')
                                ->label('Activé')
                                ->default(true),
                        ])
                        ->columns(2)
                        ->addActionLabel('Ajouter un module')
                        ->defaultItems(0),
                ])->collapsible(),

            Section::make('Paramètres')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 0, ',', ' ') . ' FCFA')
                    ->sortable(),
                TextColumn::make('billing_period')
                    ->label('Période')
                    ->formatStateUsing(fn ($state): string => $state === 'monthly' ? 'Mensuel' : 'Annuel'),
                TextColumn::make('max_students')
                    ->label('Max élèves')
                    ->sortable(),
                TextColumn::make('max_teachers')
                    ->label('Max enseignants')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                TextColumn::make('subscriptions_count')
                    ->label('Abonnements')
                    ->counts('subscriptions')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Statut')
                    ->options([
                        1 => 'Actif',
                        0 => 'Inactif',
                    ]),
                SelectFilter::make('billing_period')
                    ->label('Période')
                    ->options([
                        'monthly' => 'Mensuel',
                        'yearly' => 'Annuel',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
