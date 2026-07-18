<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Transport\Enums\VehicleStatus;
use App\Filament\Resources\VehicleResource\Pages;
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

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $recordTitleAttribute = 'registration_number';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Transport';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Vehicle Information')
                    ->schema([
                        Forms\Components\TextInput::make('registration_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('vin')
                            ->label('VIN')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manufacturer')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->maxLength(4),
                        Forms\Components\TextInput::make('color')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(2),

                SectionComponent::make('Fuel & Compliance')
                    ->schema([
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'diesel' => 'Diesel',
                                'petrol' => 'Petrol',
                                'electric' => 'Electric',
                                'hybrid' => 'Hybrid',
                                'cng' => 'CNG',
                            ]),
                        Forms\Components\DatePicker::make('insurance_expiry'),
                        Forms\Components\DatePicker::make('inspection_expiry'),
                    ])
                    ->columns(3),

                SectionComponent::make('Status & Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(VehicleStatus::labels())
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
                TextColumn::make('registration_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('manufacturer')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('model')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('year')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('capacity')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (VehicleStatus $state): string => VehicleStatus::colors()[$state] ?? 'gray')
                    ->icon(fn (VehicleStatus $state): string => VehicleStatus::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(VehicleStatus::labels()),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
