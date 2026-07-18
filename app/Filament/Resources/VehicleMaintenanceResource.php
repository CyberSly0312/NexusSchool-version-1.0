<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Transport\Enums\MaintenanceStatus;
use App\Domains\Transport\Enums\MaintenanceType;
use App\Filament\Resources\VehicleMaintenanceResource\Pages;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
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

class VehicleMaintenanceResource extends Resource
{
    protected static ?string $model = VehicleMaintenance::class;

    protected static ?string $recordTitleAttribute = 'description';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench';

    protected static string|\UnitEnum|null $navigationGroup = 'Transport';

    protected static ?int $navigationSort = 56;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Vehicle')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(fn () => Vehicle::pluck('registration_number', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                SectionComponent::make('Maintenance Details')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options(MaintenanceType::labels())
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->required(),
                        Forms\Components\TextInput::make('service_provider')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                SectionComponent::make('Dates & Cost')
                    ->schema([
                        Forms\Components\DatePicker::make('service_date')
                            ->required(),
                        Forms\Components\DatePicker::make('next_service_date'),
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('odometer_reading')
                            ->label('Odometer Reading (km)')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2),

                SectionComponent::make('Status & Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(MaintenanceStatus::labels())
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
                TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (MaintenanceType $state): string => MaintenanceType::colors()[$state] ?? 'gray')
                    ->icon(fn (MaintenanceType $state): string => MaintenanceType::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('service_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('cost')
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('next_service_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('service_provider')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (MaintenanceStatus $state): string => MaintenanceStatus::colors()[$state] ?? 'gray')
                    ->icon(fn (MaintenanceStatus $state): string => MaintenanceStatus::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(MaintenanceStatus::labels()),
                SelectFilter::make('type')
                    ->options(MaintenanceType::labels()),
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
            'index' => Pages\ListVehicleMaintenances::route('/'),
            'create' => Pages\CreateVehicleMaintenance::route('/create'),
            'edit' => Pages\EditVehicleMaintenance::route('/{record}/edit'),
        ];
    }
}
