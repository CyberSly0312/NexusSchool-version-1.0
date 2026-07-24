<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Analytics\Enums\KpiStatus;
use App\Domains\Analytics\Enums\ScheduleFrequency;
use App\Filament\Resources\KpiResource\Pages;
use App\Models\Kpi;
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

class KpiResource extends Resource
{
    protected static ?string $model = Kpi::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Analytiques';

    protected static ?string $navigationLabel = 'KPI';

    protected static ?string $modelLabel = 'KPI';

    protected static ?string $pluralModelLabel = 'KPI';

    protected static ?int $navigationSort = 72;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            SectionComponent::make('Détails du KPI')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(3),
                Forms\Components\TextInput::make('category')->required()->maxLength(100)->default('general'),
            ]),
            SectionComponent::make('Configuration')->schema([
                Forms\Components\TextInput::make('target_value')->numeric()->nullable(),
                Forms\Components\TextInput::make('unit')->maxLength(50)->nullable(),
                Forms\Components\Select::make('status')
                    ->options(KpiStatus::labels())->required()->default('active'),
                Forms\Components\Select::make('calculation_frequency')
                    ->options(ScheduleFrequency::labels())->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->searchable(),
                TextColumn::make('cached_value')->label('Valeur')->numeric()->sortable(),
                TextColumn::make('target_value')->label('Objectif')->numeric()->toggleable(),
                TextColumn::make('unit')->toggleable(),
                TextColumn::make('status')->badge()
                    ->color(fn (KpiStatus $state): string => KpiStatus::colors()[$state->value] ?? 'gray'),
                TextColumn::make('last_calculated_at')->dateTime('d/m/Y H:i')->sortable()->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable()->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')->options(KpiStatus::labels()),
                SelectFilter::make('category')->options([
                    'enrollment' => 'Inscription',
                    'attendance' => 'Présence',
                    'finance' => 'Finance',
                    'library' => 'Bibliothèque',
                    'transport' => 'Transport',
                    'general' => 'Général',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpis::route('/'),
            'create' => Pages\CreateKpi::route('/create'),
            'edit' => Pages\EditKpi::route('/{record}/edit'),
            'view' => Pages\ViewKpi::route('/{record}'),
        ];
    }
}
