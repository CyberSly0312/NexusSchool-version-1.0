<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Application\Services\EnrollmentService;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\EnrollmentType;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\Pages;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers\GraduationsRelationManager;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers\HistoryRelationManager;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers\PromotionsRelationManager;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers\TransfersRelationManager;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\ScopedByTenant;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Enrollment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des inscriptions';

    protected static ?string $navigationLabel = 'Inscriptions';

    protected static ?string $modelLabel = 'Inscription';

    protected static ?string $pluralModelLabel = 'Inscriptions';

    protected static ?string $recordTitleAttribute = 'enrollment_number';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Élève')
                    ->schema([
                        Forms\Components\Select::make('student_id')->label('Élève')
                            ->options(fn (): array => Student::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->get(['id', 'first_name', 'last_name', 'preferred_name'])
                                ->mapWithKeys(fn ($student) => [$student->id => $student->displayName()])
                                ->all())
                            ->searchable()
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('enrollment_type')->label("Type d'inscription")
                            ->options(EnrollmentType::class)
                            ->default(EnrollmentType::New),
                    ])->columns(2),
                Section::make('Année scolaire et classe')
                    ->schema([
                        Forms\Components\Select::make('academic_calendar_year_id')->label('Année scolaire')
                            ->options(fn (): array => AcademicCalendarYear::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('academic_program_id')->label('Programme')
                            ->options(fn (): array => AcademicProgram::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->required(),
                        Forms\Components\Select::make('education_level_id')->label('Niveau')
                            ->options(fn (): array => EducationLevel::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->required(),
                        Forms\Components\Select::make('school_class_id')->label('Classe')
                            ->options(fn (): array => SchoolClass::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('class_section_id')->label('Section')
                            ->options(fn (Get $get): array => ClassSection::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->when($get('school_class_id'), fn ($query, $classId) => $query->where('school_class_id', $classId))
                                ->pluck('name', 'id')
                                ->all()),
                    ])->columns(3),
                Section::make('Référence')
                    ->schema([
                        Forms\Components\TextInput::make('enrollment_number')->label('Numéro d\'inscription')
                            ->maxLength(50)->readOnlyOn('edit'),
                        Forms\Components\DatePicker::make('enrolled_at')->label("Date d'inscription")->required(),
                        Forms\Components\Textarea::make('notes')->label('Notes')->rows(3),
                    ])->columns(3),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')->label('Statut')
                            ->options(EnrollmentStatus::class)
                            ->default(EnrollmentStatus::Active)
                            ->disabledOn('edit')
                            ->dehydrated(false)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enrollment_number')->label('N° inscription')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')
                    ->searchable(['student.first_name', 'student.last_name', 'student.student_number']),
                Tables\Columns\TextColumn::make('schoolClass.name')->label('Classe')->sortable(),
                Tables\Columns\TextColumn::make('academicCalendarYear.name')->label('Année scolaire')
                    ->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('enrollment_type')->label('Type')
                    ->formatStateUsing(fn (?EnrollmentType $state): ?string => $state?->label())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->color(fn (EnrollmentStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('enrolled_at')->label('Inscrit le')->date()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(EnrollmentStatus::class),
                Tables\Filters\SelectFilter::make('school_class_id')->label('Classe')
                    ->options(fn (): array => SchoolClass::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->pluck('name', 'id')
                        ->all()),
                Tables\Filters\SelectFilter::make('academic_calendar_year_id')->label('Année scolaire')
                    ->options(fn (): array => AcademicCalendarYear::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->pluck('name', 'id')
                        ->all()),
                Tables\Filters\TrashedFilter::make()->label('Archivés'),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
                Action::make('suspend')
                    ->label('Suspendre')
                    ->icon('heroicon-o-pause-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Enrollment $record): bool => $record->isActive() && auth()->user()?->can('enrollment.suspend'))
                    ->action(fn (Enrollment $record) => app(EnrollmentService::class)
                        ->suspend($record, null, null, auth()->user())),
                Action::make('restore')
                    ->label('Restaurer')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Enrollment $record): bool => $record->isSuspended() && auth()->user()?->can('enrollment.restore'))
                    ->action(fn (Enrollment $record) => app(EnrollmentService::class)
                        ->restore($record, auth()->user())),
                Action::make('withdraw')
                    ->label('Retirer')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Enrollment $record): bool => $record->isActive() && auth()->user()?->can('enrollment.withdraw'))
                    ->action(fn (Enrollment $record) => app(EnrollmentService::class)
                        ->withdraw($record, null, auth()->user())),
                Action::make('graduate')
                    ->label('Diplômer')
                    ->icon('heroicon-o-academic-cap')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Enrollment $record): bool => $record->isActive() && auth()->user()?->can('enrollment.graduate'))
                    ->action(fn (Enrollment $record) => app(EnrollmentService::class)
                        ->graduate($record, \App\Domains\Enrollment\Application\DTOs\GraduationData::fromArray([
                            'decision' => 'graduated',
                            'education_level_id' => $record->education_level_id,
                            'graduated_on' => now()->toDateString(),
                        ]), auth()->user())),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            HistoryRelationManager::class,
            PromotionsRelationManager::class,
            TransfersRelationManager::class,
            GraduationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
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
