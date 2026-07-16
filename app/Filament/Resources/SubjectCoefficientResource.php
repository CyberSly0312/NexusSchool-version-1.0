<?php

namespace App\Filament\Resources;

use App\Domains\SubjectCoefficient\Enums\SubjectCoefficientStatus;
use App\Filament\Resources\SubjectCoefficientResource\Pages;
use App\Models\SubjectCoefficient;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectCoefficientResource extends Resource
{
    protected static ?string $model = SubjectCoefficient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Academics';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 19;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Academic Context')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->label('Subject')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('grade_level_id')
                            ->label('Grade Level')
                            ->relationship('gradeLevel', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Academic Year')
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Leave blank for curriculum-wide coefficient'),
                    ])->columns(2),
                SectionComponent::make('Coefficient Details')
                    ->schema([
                        Forms\Components\TextInput::make('coefficient')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->maxValue(99.99)
                            ->default(1.00)
                            ->step(0.01)
                            ->helperText('Weighting value (e.g. 1.5)'),
                        Forms\Components\DatePicker::make('effective_from')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('effective_to')
                            ->nullable()
                            ->helperText('Leave blank for ongoing coefficient'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                SectionComponent::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(SubjectCoefficientStatus::class)
                            ->default(SubjectCoefficientStatus::Active)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('gradeLevel.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->sortable()
                    ->toggleable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('coefficient')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                Tables\Columns\TextColumn::make('effective_from')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('effective_to')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubjectCoefficientStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('effective_from', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubjectCoefficientStatus::class),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),
                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'name'),
                Tables\Filters\SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->relationship('gradeLevel', 'name'),
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->relationship('academicYear', 'name'),
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
            'index' => Pages\ListSubjectCoefficients::route('/'),
            'create' => Pages\CreateSubjectCoefficient::route('/create'),
            'edit' => Pages\EditSubjectCoefficient::route('/{record}/edit'),
        ];
    }
}
