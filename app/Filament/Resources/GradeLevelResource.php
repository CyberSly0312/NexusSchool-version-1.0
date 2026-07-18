<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\GradeLevel\Enums\EducationalCycle;
use App\Domains\GradeLevel\Enums\GradeLevelStatus;
use App\Filament\Resources\GradeLevelResource\Pages;
use App\Models\GradeLevel;
use App\Models\Program;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GradeLevelResource extends Resource
{
    protected static ?string $model = GradeLevel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string|\UnitEnum|null $navigationGroup = 'Academics';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 13;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Grade Level Details')
                    ->schema([
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->options(Program::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Short unique code (e.g., 6E, SEC)'),
                        Forms\Components\TextInput::make('short_name')
                            ->maxLength(20)
                            ->helperText('Abbreviation (e.g., 6ème)'),
                        Forms\Components\TextInput::make('level_order')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Position within program'),
                        Forms\Components\Select::make('educational_cycle')
                            ->label('Educational Cycle')
                            ->options(EducationalCycle::class)
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),
                Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(GradeLevelStatus::class)
                            ->default(GradeLevelStatus::Active)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('short_name')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('educational_cycle')
                    ->label('Cycle')
                    ->formatStateUsing(fn (EducationalCycle $state): string => $state->label())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (GradeLevelStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('level_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(GradeLevelStatus::class),
                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Program')
                    ->options(Program::pluck('name', 'id')),
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
            'index' => Pages\ListGradeLevels::route('/'),
            'create' => Pages\CreateGradeLevel::route('/create'),
            'edit' => Pages\EditGradeLevel::route('/{record}/edit'),
        ];
    }
}
