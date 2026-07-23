<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RouteStopResource\Pages;
use App\Models\Route;
use App\Models\RouteStop;
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

class RouteStopResource extends Resource
{
    protected static ?string $model = RouteStop::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Transport';

    protected static ?string $navigationLabel = 'Arrêts';

    protected static ?string $modelLabel = 'Arrêt';

    protected static ?string $pluralModelLabel = 'Arrêts';

    protected static ?int $navigationSort = 53;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Informations de l\'itinéraire et de l\'arrêt')
                    ->schema([
                        Forms\Components\Select::make('route_id')
                            ->label('Itinéraire')
                            ->options(fn () => Route::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('stop_order')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->columns(3),

                SectionComponent::make('Emplacement')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                SectionComponent::make('Horaire et notes')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_time_minutes')
                            ->label('Temps estimé (min)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('route.name')
                    ->label('Itinéraire')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stop_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estimated_time_minutes')
                    ->label('Temps (min)')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('address')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('route_id')
                    ->label('Itinéraire')
                    ->options(fn () => Route::pluck('name', 'id')),
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
            'index' => Pages\ListRouteStops::route('/'),
            'create' => Pages\CreateRouteStop::route('/create'),
            'edit' => Pages\EditRouteStop::route('/{record}/edit'),
        ];
    }
}
