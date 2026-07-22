<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\System\Enums\MaintenanceType;
use App\Filament\Resources\MaintenanceWindowResource\Pages;
use App\Models\MaintenanceWindow;
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

class MaintenanceWindowResource extends Resource
{
    protected static ?string $model = MaintenanceWindow::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration système';

    protected static ?string $navigationLabel = 'Fenêtres de maintenance';

    protected static ?string $modelLabel = 'Fenêtre de maintenance';

    protected static ?string $pluralModelLabel = 'Fenêtres de maintenance';

    protected static ?int $navigationSort = 93;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Maintenance Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('type')
                            ->options(MaintenanceType::labels())
                            ->default('scheduled'),
                    ])
                    ->columns(1),

                SectionComponent::make('Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->required(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->required(),
                        Forms\Components\Toggle::make('is_readonly')
                            ->default(false),
                    ])
                    ->columns(3),

                SectionComponent::make('Affected Services')
                    ->schema([
                        Forms\Components\Repeater::make('affected_services')
                            ->schema([
                                Forms\Components\TextInput::make('service'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Service'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => MaintenanceType::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('status')
                    ->badge(),
                IconColumn::make('is_readonly')
                    ->label('Read-only')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'completed' => 'Completed']),
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
            'index' => Pages\ListMaintenanceWindows::route('/'),
            'create' => Pages\CreateMaintenanceWindow::route('/create'),
            'edit' => Pages\EditMaintenanceWindow::route('/{record}/edit'),
        ];
    }
}
