<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\FuelLogResource\Pages;
use App\Models\Driver;
use App\Models\FuelLog;
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

class FuelLogResource extends Resource
{
    protected static ?string $model = FuelLog::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Transport';

    protected static ?int $navigationSort = 57;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Vehicle & Driver')
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
                    ])
                    ->columns(2),

                SectionComponent::make('Fuel Details')
                    ->schema([
                        Forms\Components\TextInput::make('liters')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('cost_per_liter')
                            ->label('Cost per Liter')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('total_cost')
                            ->label('Total Cost')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'diesel' => 'Diesel',
                                'petrol' => 'Petrol',
                                'electric' => 'Electric',
                                'hybrid' => 'Hybrid',
                                'cng' => 'CNG',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                SectionComponent::make('Station & Date')
                    ->schema([
                        Forms\Components\TextInput::make('fuel_station')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('odometer_reading')
                            ->label('Odometer Reading (km)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\DatePicker::make('fuel_date')
                            ->required(),
                    ])
                    ->columns(3),

                SectionComponent::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver')
                    ->label('Driver')
                    ->getStateUsing(fn (FuelLog $record): string => trim($record->driver->first_name.' '.$record->driver->last_name))
                    ->searchable(),
                TextColumn::make('fuel_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('liters')
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_cost')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('fuel_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'diesel' => 'warning',
                        'petrol' => 'info',
                        'electric' => 'success',
                        'hybrid' => 'primary',
                        'cng' => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('odometer_reading')
                    ->label('Odometer')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('fuel_type')
                    ->label('Fuel Type')
                    ->options([
                        'diesel' => 'Diesel',
                        'petrol' => 'Petrol',
                        'electric' => 'Electric',
                        'hybrid' => 'Hybrid',
                        'cng' => 'CNG',
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
            'index' => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
            'edit' => Pages\EditFuelLog::route('/{record}/edit'),
        ];
    }
}
