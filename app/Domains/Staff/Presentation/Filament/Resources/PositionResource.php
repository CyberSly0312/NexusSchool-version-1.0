<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources;

use App\Domains\Staff\Domain\Enums\PositionCategory;
use App\Domains\Staff\Infrastructure\Models\Position;
use App\Domains\Staff\Presentation\Filament\Resources\PositionResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PositionResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Position::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Ressources humaines';

    protected static ?string $navigationLabel = 'Postes';

    protected static ?string $modelLabel = 'Poste';

    protected static ?string $pluralModelLabel = 'Postes';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Poste')
                    ->schema([
                        Forms\Components\TextInput::make('code')->label('Code')->required()->maxLength(50),
                        Forms\Components\TextInput::make('title')->label('Intitulé')->required()->maxLength(150),
                        Forms\Components\Select::make('category')->label('Catégorie')
                            ->options(PositionCategory::class)->required(),
                        Forms\Components\Textarea::make('description')->label('Description')->rows(3),
                        Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
                        Forms\Components\TextInput::make('sort_order')->label('Ordre')->numeric(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Intitulé')->searchable(),
                Tables\Columns\TextColumn::make('category')->label('Catégorie')
                    ->badge()->color('info'),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->label('Catégorie')->options(PositionCategory::class),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record = null): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
