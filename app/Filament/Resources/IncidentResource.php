<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Incident\Enums\IncidentSeverity;
use App\Domains\Incident\Enums\IncidentStatus;
use App\Filament\Resources\IncidentResource\Pages;
use App\Models\Incident;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Traits\ScopedByTenant;

class IncidentResource extends Resource
{
    use ScopedByTenant;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Incident::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Sécurité';

    protected static ?string $navigationLabel = 'Incidents';

    protected static ?string $modelLabel = 'Incident';

    protected static ?string $pluralModelLabel = 'Incidents';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Détails de l\'incident')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('severity')
                        ->label('Gravité')
                        ->options(collect(IncidentSeverity::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray())
                        ->required()
                        ->default(IncidentSeverity::Low->value),
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(collect(IncidentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray())
                        ->required()
                        ->default(IncidentStatus::Open->value),
                    Forms\Components\DateTimePicker::make('incident_date')
                        ->label('Date de l\'incident')
                        ->native(false),
                ])->columns(2),
            Section::make('Description')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(4),
                    Forms\Components\TextInput::make('location')
                        ->label('Lieu'),
                ])->columns(2),
            Section::make('Suivi')
                ->schema([
                    Forms\Components\Select::make('assigned_to')
                        ->label('Assigné à')
                        ->options(fn () => User::where('school_id', tenant('id'))->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\Textarea::make('actions_taken')
                        ->label('Actions entreprises')
                        ->rows(3),
                    Forms\Components\Textarea::make('resolution_notes')
                        ->label('Notes de résolution')
                        ->rows(3),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('severity')
                    ->label('Gravité')
                    ->badge()
                    ->color(fn (IncidentSeverity $state): string => $state->color()),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (IncidentStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('incident_date')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigné à')
                    ->searchable(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(IncidentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray()),
                Tables\Filters\SelectFilter::make('severity')
                    ->label('Gravité')
                    ->options(collect(IncidentSeverity::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray()),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}
