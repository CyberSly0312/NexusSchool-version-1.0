<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Transport\Enums\RouteStatus;
use App\Filament\Resources\RouteResource\Pages;
use App\Models\Route;
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

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|\UnitEnum|null $navigationGroup = 'Transport';

    protected static ?string $navigationLabel = 'Itinéraires';

    protected static ?string $modelLabel = 'Itinéraire';

    protected static ?string $pluralModelLabel = 'Itinéraires';

    protected static ?int $navigationSort = 52;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Route Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\TextInput::make('distance_km')
                            ->label('Distance (km)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('estimated_duration_minutes')
                            ->label('Estimated Duration (min)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('fare')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2),

                SectionComponent::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(RouteStatus::labels())
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('distance_km')
                    ->label('Distance (km)')
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('estimated_duration_minutes')
                    ->label('Duration (min)')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('fare')
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (RouteStatus $state): string => RouteStatus::colors()[$state] ?? 'gray')
                    ->icon(fn (RouteStatus $state): string => RouteStatus::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(RouteStatus::labels()),
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
            'index' => Pages\ListRoutes::route('/'),
            'create' => Pages\CreateRoute::route('/create'),
            'edit' => Pages\EditRoute::route('/{record}/edit'),
        ];
    }
}
