<?php

declare(strict_types=1);

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
use App\Traits\ScopedByTenant;

class SubjectCoefficientResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = SubjectCoefficient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Enseignements';

    protected static ?string $navigationLabel = 'Coefficients';

    protected static ?string $modelLabel = 'Coefficient';

    protected static ?string $pluralModelLabel = 'Coefficients';

    protected static ?string $recordTitleAttribute = 'coefficient';

    protected static ?int $navigationSort = 19;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Contexte académique')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->label('Matière')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('program_id')
                            ->label('Programme')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('grade_level_id')
                            ->label('Niveau scolaire')
                            ->relationship('gradeLevel', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Année académique')
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Laisser vide pour un coefficient pour tout le programme'),
                    ])->columns(2),
                SectionComponent::make('Détails du coefficient')
                    ->schema([
                        Forms\Components\TextInput::make('coefficient')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->maxValue(99.99)
                            ->default(1.00)
                            ->step(0.01)
                            ->helperText('Valeur de pondération (ex. 1.5)'),
                        Forms\Components\DatePicker::make('effective_from')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('effective_to')
                            ->nullable()
                            ->helperText('Laisser vide pour un coefficient permanent'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                SectionComponent::make('Statut')
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
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('effective_to')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubjectCoefficientStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('effective_from', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubjectCoefficientStatus::class),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Matière')
                    ->relationship('subject', 'name'),
                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Programme')
                    ->relationship('program', 'name'),
                Tables\Filters\SelectFilter::make('grade_level_id')
                    ->label('Niveau scolaire')
                    ->relationship('gradeLevel', 'name'),
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Année académique')
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
