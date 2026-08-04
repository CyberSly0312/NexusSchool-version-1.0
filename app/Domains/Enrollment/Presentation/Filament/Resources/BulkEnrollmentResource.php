<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Presentation\Filament\Resources\BulkEnrollmentResource\Pages;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Traits\ScopedByTenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BulkEnrollmentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Enrollment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des inscriptions';

    protected static ?string $navigationLabel = 'Inscription groupée';

    protected static ?string $modelLabel = 'Inscription groupée';

    protected static ?string $pluralModelLabel = 'Inscription groupée';

    protected static ?int $navigationSort = 45;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Élèves à inscrire')
                    ->schema([
                        Forms\Components\Select::make('student_ids')->label('Élèves')
                            ->options(fn (): array => Student::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->active()
                                ->get(['id', 'first_name', 'last_name', 'preferred_name'])
                                ->mapWithKeys(fn ($student) => [$student->id => $student->displayName()])
                                ->all())
                            ->multiple()
                            ->searchable()
                            ->required(),
                    ]),
                Section::make('Affectation')
                    ->schema([
                        Forms\Components\Select::make('academic_calendar_year_id')->label('Année scolaire')
                            ->options(fn (): array => AcademicCalendarYear::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->active()
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
                        Forms\Components\DatePicker::make('enrolled_at')->label("Date d'inscription")->required(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('enrollment_number')->label('N° inscription'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateBulkEnrollment::route('/create'),
        ];
    }
}
