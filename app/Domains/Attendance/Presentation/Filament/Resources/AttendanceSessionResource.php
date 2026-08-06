<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Attendance\Domain\Enums\AttendanceSessionStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSession;
use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource\Pages;
use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource\RelationManagers\RecordsRelationManager;
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

class AttendanceSessionResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AttendanceSession::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Présences';

    protected static ?string $navigationLabel = 'Séances';

    protected static ?string $modelLabel = 'Séance';

    protected static ?string $pluralModelLabel = 'Séances';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 50;

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
                            ->searchable(),
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
                        Forms\Components\Select::make('academic_subject_id')->label('Matière')
                            ->options(fn (): array => AcademicSubject::query()
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
                            ->searchable(),
                    ])->columns(2),
                Section::make('Horaire')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->required(),
                        Forms\Components\TextInput::make('period_number')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->required(),
                        Forms\Components\TimePicker::make('starts_at'),
                        Forms\Components\TimePicker::make('ends_at'),
                    ])->columns(2),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(AttendanceSessionStatus::class)
                            ->default(AttendanceSessionStatus::Draft)
                            ->required(),
                        Forms\Components\TextInput::make('expected_students_count')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_class_id')
                    ->label('Classe')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('class_section_id')
                    ->label('Section')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_subject_id')
                    ->label('Matière')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_number')
                    ->label('Période')
                    ->sortable(),
                Tables\Columns\TextColumn::make('recorded_students_count')
                    ->label('Enregistrés')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AttendanceSessionStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AttendanceSessionStatus::class),
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
        return [
            RecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceSessions::route('/'),
            'create' => Pages\CreateAttendanceSession::route('/create'),
            'edit' => Pages\EditAttendanceSession::route('/{record}/edit'),
        ];
    }
}
