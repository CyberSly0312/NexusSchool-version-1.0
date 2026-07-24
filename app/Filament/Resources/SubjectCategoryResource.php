<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\SubjectCategory\Enums\SubjectCategoryStatus;
use App\Filament\Resources\SubjectCategoryResource\Pages;
use App\Models\SubjectCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Traits\ScopedByTenant;

class SubjectCategoryResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = SubjectCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Enseignements';

    protected static ?string $navigationLabel = 'Catégories de matière';

    protected static ?string $modelLabel = 'Catégorie de matière';

    protected static ?string $pluralModelLabel = 'Catégories de matière';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 17;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la catégorie de matière')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Code unique au sein de l\'école'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\TextInput::make('color')
                            ->maxLength(20)
                            ->helperText('Code couleur hexadécimal, ex. #3B82F6'),
                        Forms\Components\TextInput::make('icon')
                            ->maxLength(100)
                            ->helperText('Identifiant d\'icône'),
                    ])->columns(2),
                SectionComponent::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(SubjectCategoryStatus::class)
                            ->default(SubjectCategoryStatus::Active)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->toggleable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('display_order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->toggleable()
                    ->label('Couleur')
                    ->formatStateUsing(fn (?string $state): string => $state ?? '—'),
                Tables\Columns\TextColumn::make('icon')
                    ->toggleable()
                    ->label('Icône')
                    ->formatStateUsing(fn (?string $state): string => $state ?? '—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubjectCategoryStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubjectCategoryStatus::class),
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
            'index' => Pages\ListSubjectCategories::route('/'),
            'create' => Pages\CreateSubjectCategory::route('/create'),
            'edit' => Pages\EditSubjectCategory::route('/{record}/edit'),
        ];
    }
}
