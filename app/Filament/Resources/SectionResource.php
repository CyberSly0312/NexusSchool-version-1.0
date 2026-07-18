<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Section\Enums\SectionStatus;
use App\Filament\Resources\SectionResource\Pages;
use App\Models\GradeLevel;
use App\Models\Section;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Academics';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 14;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Section Details')
                    ->schema([
                        Forms\Components\Select::make('grade_level_id')
                            ->label('Grade Level')
                            ->options(GradeLevel::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Short unique code (e.g., A, SCI)'),
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),
                SectionComponent::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(SectionStatus::class)
                            ->default(SectionStatus::Active)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gradeLevel.name')
                    ->label('Grade Level')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SectionStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SectionStatus::class),
                Tables\Filters\SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->options(GradeLevel::pluck('name', 'id')),
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
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}
