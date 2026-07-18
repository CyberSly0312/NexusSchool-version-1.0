<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\StudentEnrollment\Enums\EnrollmentStatus;
use App\Domains\StudentEnrollment\Enums\EnrollmentType;
use App\Filament\Resources\StudentEnrollmentResource\Pages;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StudentEnrollmentResource extends Resource
{
    protected static ?string $model = StudentEnrollment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $recordTitleAttribute = 'enrollment_number';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Enrollment Details')
                    ->schema([
                        Forms\Components\TextInput::make('enrollment_number')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Unique enrollment number within the school'),
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->relationship('student', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Student $record) => "{$record->full_name} ({$record->student_number})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Academic Year')
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('academic_class_id')
                            ->label('Class')
                            ->relationship('academicClass', 'name')
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
                        Forms\Components\Select::make('section_id')
                            ->label('Section')
                            ->relationship('section', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('classroom_id')
                            ->label('Classroom')
                            ->relationship('classroom', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])->columns(3),
                SectionComponent::make('Dates & Type')
                    ->schema([
                        Forms\Components\DatePicker::make('enrollment_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('effective_date')
                            ->required()
                            ->default(now())
                            ->afterOrEqual('enrollment_date')
                            ->helperText('Must be on or after the enrollment date'),
                        Forms\Components\Select::make('enrollment_type')
                            ->options(collect(EnrollmentType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required()
                            ->default(EnrollmentType::NewAdmission),
                        Forms\Components\Select::make('status')
                            ->options(collect(EnrollmentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->default(EnrollmentStatus::Pending)
                            ->required(),
                    ])->columns(2),
                SectionComponent::make('Transfer Information')
                    ->schema([
                        Forms\Components\TextInput::make('previous_school')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\Textarea::make('transfer_reason')
                            ->rows(2)
                            ->maxLength(500)
                            ->nullable(),
                    ])->columns(2),
                SectionComponent::make('Exit Information')
                    ->schema([
                        Forms\Components\DatePicker::make('exit_date')
                            ->nullable()
                            ->afterOrEqual('enrollment_date')
                            ->helperText('Must be on or after the enrollment date'),
                        Forms\Components\Textarea::make('exit_reason')
                            ->rows(2)
                            ->maxLength(500)
                            ->nullable(),
                    ])->columns(2),
                SectionComponent::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enrollment_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicClass.name')
                    ->label('Class')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('gradeLevel.name')
                    ->label('Grade')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('section.name')
                    ->label('Section')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('enrollment_type')
                    ->badge()
                    ->color(fn (EnrollmentType $state): string => 'info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (EnrollmentStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('effective_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(EnrollmentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Tables\Filters\SelectFilter::make('enrollment_type')
                    ->options(collect(EnrollmentType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
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
            'index' => Pages\ListStudentEnrollments::route('/'),
            'create' => Pages\CreateStudentEnrollment::route('/create'),
            'edit' => Pages\EditStudentEnrollment::route('/{record}/edit'),
        ];
    }
}
