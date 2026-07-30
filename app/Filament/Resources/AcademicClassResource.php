<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\AcademicClass\Enums\AcademicClassStatus;
use App\Filament\Resources\AcademicClassResource\Pages;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Program;
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
use App\Traits\ScopedByTenant;

class AcademicClassResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AcademicClass::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Classes';

    protected static ?string $modelLabel = 'Classe';

    protected static ?string $pluralModelLabel = 'Classes';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la classe')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Année scolaire')
                            ->options(AcademicYear::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('program_id')
                            ->label('Programme')
                            ->options(Program::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('grade_level_id')
                            ->label('Niveau')
                            ->options(GradeLevel::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('section_id')
                            ->label('Section')
                            ->options(Section::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Code unique dans l\'établissement (ex., 6EA, PRE-A)'),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Nombre maximum d\'élèves'),
                        Forms\Components\TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),
                SectionComponent::make('Statut et configuration')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(AcademicClassStatus::class)
                            ->default(AcademicClassStatus::Active)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activé pour inscriptions')
                            ->default(true),
                        Forms\Components\Select::make('classroom_id')
                            ->label('Salle')
                            ->options(Classroom::pluck('name', 'id'))
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->helperText('Salle de classe (optionnel)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Année')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Programme')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gradeLevel.name')
                    ->label('Niveau')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section.name')
                    ->label('Section')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activé'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AcademicClassStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AcademicClassStatus::class),
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Année scolaire')
                    ->options(AcademicYear::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Programme')
                    ->options(Program::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('grade_level_id')
                    ->label('Niveau')
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
            'index' => Pages\ListAcademicClasses::route('/'),
            'create' => Pages\CreateAcademicClass::route('/create'),
            'edit' => Pages\EditAcademicClass::route('/{record}/edit'),
        ];
    }
}
