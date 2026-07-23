<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Analytics\Enums\DashboardType;
use App\Filament\Resources\DashboardResource\Pages;
use App\Models\Dashboard;
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

class DashboardResource extends Resource
{
    protected static ?string $model = Dashboard::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Analytiques';

    protected static ?string $navigationLabel = 'Tableaux de bord';

    protected static ?string $modelLabel = 'Tableau de bord';

    protected static ?string $pluralModelLabel = 'Tableaux de bord';

    protected static ?int $navigationSort = 70;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            SectionComponent::make('Détails du tableau de bord')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(3),
            ]),
            SectionComponent::make('Configuration')->schema([
                Forms\Components\Select::make('type')
                    ->options(DashboardType::labels())->required()->default('custom'),
                Forms\Components\Toggle::make('is_default'),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('type')->badge()
                    ->color(fn (DashboardType $state): string => DashboardType::colors()[$state] ?? 'gray'),
                IconColumn::make('is_default')->boolean(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('widgets_count')->counts('widgets')->label('Widgets')->sortable(),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable()->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('type')->options(DashboardType::labels()),
                SelectFilter::make('is_active')->options([1 => 'Actif', 0 => 'Inactif']),
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
            'index' => Pages\ListDashboards::route('/'),
            'create' => Pages\CreateDashboard::route('/create'),
            'edit' => Pages\EditDashboard::route('/{record}/edit'),
            'view' => Pages\ViewDashboard::route('/{record}'),
        ];
    }
}
