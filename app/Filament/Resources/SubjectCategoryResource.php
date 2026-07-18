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

class SubjectCategoryResource extends Resource
{
    protected static ?string $model = SubjectCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Academics';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 17;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Subject Category Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Unique code within the school'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\TextInput::make('color')
                            ->maxLength(20)
                            ->helperText('Hex color code, e.g. #3B82F6'),
                        Forms\Components\TextInput::make('icon')
                            ->maxLength(100)
                            ->helperText('Icon identifier'),
                    ])->columns(2),
                SectionComponent::make('Status')
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
                    ->label('Color')
                    ->formatStateUsing(fn (?string $state): string => $state ?? '—'),
                Tables\Columns\TextColumn::make('icon')
                    ->toggleable()
                    ->label('Icon')
                    ->formatStateUsing(fn (?string $state): string => $state ?? '—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubjectCategoryStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
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
