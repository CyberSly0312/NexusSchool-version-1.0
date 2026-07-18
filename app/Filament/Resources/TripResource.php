<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Transport\Enums\TripStatus;
use App\Filament\Resources\TripResource\Pages;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Trip;
use App\Models\Vehicle;
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

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static string|\UnitEnum|null $navigationGroup = 'Transport';

    protected static ?int $navigationSort = 54;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Trip Details')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(fn () => Vehicle::pluck('registration_number', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('driver_id')
                            ->label('Driver')
                            ->options(fn () => Driver::all()->map(fn (Driver $driver) => [
                                'value' => $driver->id,
                                'label' => trim($driver->first_name.' '.$driver->last_name),
                            ])->pluck('label', 'value'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('route_id')
                            ->label('Route')
                            ->options(fn () => Route::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('direction')
                            ->options([
                                'to_school' => 'To School',
                                'from_school' => 'From School',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                SectionComponent::make('Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('academic_date')
                            ->required(),
                        Forms\Components\TextInput::make('departure_time')
                            ->label('Departure Time')
                            ->placeholder('HH:MM'),
                        Forms\Components\TextInput::make('arrival_time')
                            ->label('Arrival Time')
                            ->placeholder('HH:MM'),
                    ])
                    ->columns(3),

                SectionComponent::make('Status & Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(TripStatus::labels())
                            ->required(),
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
                TextColumn::make('academic_date')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('direction')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'to_school' => 'info',
                        'from_school' => 'warning',
                        default => 'gray',
                    })
                    ->label('Direction'),
                TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver')
                    ->label('Driver')
                    ->getStateUsing(fn (Trip $record): string => trim($record->driver->first_name.' '.$record->driver->last_name))
                    ->searchable(),
                TextColumn::make('route.name')
                    ->label('Route')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('departure_time')
                    ->toggleable(),
                TextColumn::make('arrival_time')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (TripStatus $state): string => TripStatus::colors()[$state] ?? 'gray')
                    ->icon(fn (TripStatus $state): string => TripStatus::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TripStatus::labels()),
                SelectFilter::make('direction')
                    ->options([
                        'to_school' => 'To School',
                        'from_school' => 'From School',
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
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
