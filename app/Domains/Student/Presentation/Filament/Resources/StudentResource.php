<?php

declare(strict_types=1);

namespace App\Domains\Student\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Student\Domain\Enums\Gender;
use App\Domains\Student\Domain\Enums\StudentStatus;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Presentation\Filament\Resources\StudentResource\Pages;
use App\Domains\Student\Presentation\Filament\Resources\StudentResource\RelationManagers\GuardiansRelationManager;
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

class StudentResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Élèves';

    protected static ?string $navigationLabel = 'Élèves';

    protected static ?string $modelLabel = 'Élève';

    protected static ?string $pluralModelLabel = 'Élèves';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identité')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')->label('Prénom')->required()->maxLength(100),
                        Forms\Components\TextInput::make('middle_name')->label('Deuxième prénom')->maxLength(100),
                        Forms\Components\TextInput::make('last_name')->label('Nom de famille')->required()->maxLength(100),
                        Forms\Components\TextInput::make('preferred_name')->label('Prénom usuel')->maxLength(100),
                        Forms\Components\Select::make('gender')->label('Genre')->options(Gender::class)->required(),
                        Forms\Components\DatePicker::make('date_of_birth')->label('Date de naissance')
                            ->before('today')->required(),
                        Forms\Components\TextInput::make('nationality')->label('Nationalité')->required()->maxLength(100),
                        Forms\Components\FileUpload::make('photo')->label('Photo')
                            ->image()->directory('student-photos')->imageEditor(),
                    ])->columns(3),
                Section::make('Références')
                    ->schema([
                        Forms\Components\TextInput::make('student_number')->label('Numéro élève')
                            ->readOnlyOn('edit')->maxLength(50)->columnSpan(1),
                        Forms\Components\TextInput::make('admission_number')->label('N° d\'admission')->maxLength(50),
                        Forms\Components\TextInput::make('national_id')->label('N° CNI')->maxLength(100),
                        Forms\Components\TextInput::make('birth_certificate_reference')->label('Réf. acte de naissance')
                            ->maxLength(100),
                    ])->columns(3),
                Section::make('Affectation académique')
                    ->schema([
                        Forms\Components\Select::make('academic_calendar_year_id')->label('Année scolaire')
                            ->options(fn (): array => AcademicCalendarYear::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->required()
                            ->live(),
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
                        Forms\Components\DatePicker::make('admission_date')->label('Date d\'admission')->required(),
                    ])->columns(3),
                Section::make('Contact & adresse')
                    ->schema([
                        Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')->label('Téléphone')->tel()->maxLength(30),
                        Forms\Components\TextInput::make('address_street')->label('Rue'),
                        Forms\Components\TextInput::make('address_city')->label('Ville'),
                        Forms\Components\TextInput::make('address_state')->label('Région'),
                        Forms\Components\TextInput::make('address_postal_code')->label('Code postal'),
                        Forms\Components\TextInput::make('address_country')->label('Pays'),
                    ])->columns(3),
                Section::make('Contact d\'urgence')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_contact_name')->label('Nom'),
                        Forms\Components\TextInput::make('emergency_contact_phone')->label('Téléphone')->tel(),
                        Forms\Components\TextInput::make('emergency_contact_relationship')->label('Lien de parenté'),
                    ])->columns(3),
                Section::make('Médical')
                    ->schema([
                        Forms\Components\Textarea::make('medical_notes')->label('Notes médicales')->rows(3),
                        Forms\Components\Textarea::make('allergies')->label('Allergies')->rows(2),
                    ])->columns(2),
                Section::make('Tuteurs / Parents')
                    ->schema([
                        Forms\Components\Repeater::make('guardians')
                            ->label('Tuteurs')
                            ->default([])
                            ->schema([
                                Forms\Components\TextInput::make('name')->label('Nom complet')->required(),
                                Forms\Components\TextInput::make('phone')->label('Téléphone')->tel(),
                                Forms\Components\TextInput::make('email')->label('Email')->email(),
                                Forms\Components\Select::make('relationship')->label('Lien')
                                    ->options([
                                        'parent' => 'Parent',
                                        'guardian' => 'Tuteur',
                                        'sibling' => 'Frère / Sœur',
                                        'other' => 'Autre',
                                    ])
                                    ->required(),
                                Forms\Components\Toggle::make('is_primary')->label('Principal'),
                            ])
                            ->defaultItems(0)
                            ->maxItems(5),
                    ]),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')->label('Statut')
                            ->options(StudentStatus::class)
                            ->default(StudentStatus::Active)
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
                Tables\Columns\TextColumn::make('student_number')->label('N° élève')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('full_name')->label('Nom complet')->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('gender')->label('Genre')
                    ->formatStateUsing(fn (?Gender $state): ?string => $state?->label()),
                Tables\Columns\TextColumn::make('schoolClass.name')->label('Classe')->sortable(),
                Tables\Columns\TextColumn::make('academicCalendarYear.name')->label('Année scolaire')
                    ->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->color(fn (StudentStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')->label('Créé le')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(StudentStatus::class),
                Tables\Filters\SelectFilter::make('school_class_id')->label('Classe')
                    ->options(fn (): array => SchoolClass::query()
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
                    ->visible(fn (Student $record): bool => $record->isActive() && auth()->user()?->can('student.suspend'))
                    ->action(fn (Student $record) => app(\App\Domains\Student\Application\Services\StudentService::class)
                        ->suspend($record, null, null, auth()->user())),
                Action::make('graduate')
                    ->label('Diplômer')
                    ->icon('heroicon-o-academic-cap')
                    ->requiresConfirmation()
                    ->visible(fn (Student $record): bool => $record->isActive() && auth()->user()?->can('student.graduate'))
                    ->action(fn (Student $record) => app(\App\Domains\Student\Application\Services\StudentService::class)
                        ->graduate($record, auth()->user())),
                Action::make('archive')
                    ->label('Archiver')
                    ->icon('heroicon-o-archive-box')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Student $record): bool => ! $record->isArchived() && auth()->user()?->can('student.archive'))
                    ->action(fn (Student $record) => app(\App\Domains\Student\Application\Services\StudentService::class)
                        ->archive($record, auth()->user())),
                Action::make('restore')
                    ->label('Restaurer')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Student $record): bool => $record->isArchived() && auth()->user()?->can('student.restore'))
                    ->action(fn (Student $record) => app(\App\Domains\Student\Application\Services\StudentService::class)
                        ->restore($record, auth()->user())),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            GuardiansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
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
