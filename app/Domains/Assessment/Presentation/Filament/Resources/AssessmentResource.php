<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Assessment\Domain\Enums\AssessmentStatus;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentType;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentResource\Pages;
use App\Domains\Staff\Infrastructure\Models\Staff;
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

class AssessmentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Assessment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Évaluations';

    protected static ?string $navigationLabel = 'Évaluations';

    protected static ?string $modelLabel = 'Évaluation';

    protected static ?string $pluralModelLabel = 'Évaluations';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Contexte')
                    ->schema([
                        Forms\Components\Select::make('academic_calendar_year_id')->label('Année scolaire')
                            ->options(fn (): array => AcademicCalendarYear::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('academic_calendar_term_id')->label('Période')
                            ->options(fn (): array => AcademicCalendarTerm::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('academic_subject_id')->label('Matière')
                            ->options(fn (): array => AcademicSubject::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('school_class_id')->label('Classe')
                            ->options(fn (): array => SchoolClass::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('class_section_id')->label('Section')
                            ->options(fn (): array => ClassSection::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable(),
                        Forms\Components\Select::make('staff_id')->label('Enseignant')
                            ->options(fn (): array => Staff::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->get(['id', 'first_name', 'last_name'])
                                ->mapWithKeys(fn ($staff) => [$staff->id => trim($staff->first_name.' '.$staff->last_name)])
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('assessment_type_id')->label('Type d\'évaluation')
                            ->options(fn (): array => AssessmentType::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->where('is_active', true)
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                    ])->columns(2),
                Section::make('Définition')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Titre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('maximum_score')->label('Note maximale')
                            ->numeric()
                            ->minValue(0.01)
                            ->default(20)
                            ->required(),
                        Forms\Components\TextInput::make('passing_score')->label('Note de réussite')
                            ->numeric()
                            ->minValue(0)
                            ->default(10),
                        Forms\Components\TextInput::make('weight')->label('Coefficient')
                            ->numeric()
                            ->minValue(0.01)
                            ->default(1)
                            ->required(),
                    ])->columns(3),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(AssessmentStatus::class)
                            ->default(AssessmentStatus::Draft)
                            ->required(),
                        Forms\Components\Textarea::make('notes')->label('Notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('academic_subject_id')
                    ->label('Matière')
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_class_id')
                    ->label('Classe')
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_score')
                    ->label('Max')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Coeff.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AssessmentStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AssessmentStatus::class),
                Tables\Filters\SelectFilter::make('academic_subject_id')
                    ->label('Matière')
                    ->options(fn (): array => AcademicSubject::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->pluck('name', 'id')
                        ->all()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessments::route('/'),
            'create' => Pages\CreateAssessment::route('/create'),
            'edit' => Pages\EditAssessment::route('/{record}/edit'),
        ];
    }
}
