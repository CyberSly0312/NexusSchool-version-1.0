<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Student\Enums\StudentStatus;
use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Student Identifiers')
                    ->schema([
                        Forms\Components\TextInput::make('student_number')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Unique identifier within the school'),
                        Forms\Components\TextInput::make('admission_number')
                            ->maxLength(50)
                            ->nullable()
                            ->helperText('Unique admission number'),
                        Forms\Components\TextInput::make('national_id')
                            ->maxLength(100)
                            ->nullable()
                            ->label('National ID'),
                    ])->columns(3),
                SectionComponent::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('middle_name')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\TextInput::make('preferred_name')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->required()
                            ->maxDate(now()),
                        Forms\Components\TextInput::make('place_of_birth')
                            ->maxLength(200)
                            ->nullable(),
                        Forms\Components\TextInput::make('nationality')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\Select::make('blood_group')
                            ->options([
                                'A+' => 'A+',
                                'A-' => 'A-',
                                'B+' => 'B+',
                                'B-' => 'B-',
                                'AB+' => 'AB+',
                                'AB-' => 'AB-',
                                'O+' => 'O+',
                                'O-' => 'O-',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('religion')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('student-photos')
                            ->nullable(),
                    ])->columns(3),
                SectionComponent::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('phone_number')
                            ->maxLength(30)
                            ->nullable()
                            ->tel(),
                        Forms\Components\Textarea::make('residential_address')
                            ->rows(2)
                            ->nullable(),
                    ])->columns(2),
                SectionComponent::make('Academic Information')
                    ->schema([
                        Forms\Components\DatePicker::make('admission_date')
                            ->nullable(),
                    ])->columns(2),
                SectionComponent::make('Medical Information')
                    ->schema([
                        Forms\Components\Textarea::make('allergies')
                            ->rows(2)
                            ->nullable(),
                        Forms\Components\Textarea::make('medical_notes')
                            ->rows(2)
                            ->nullable(),
                    ])->columns(2),
                SectionComponent::make('Status & Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(collect(StudentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->default(StudentStatus::Active)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (StudentStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('admission_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('last_name')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(StudentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
