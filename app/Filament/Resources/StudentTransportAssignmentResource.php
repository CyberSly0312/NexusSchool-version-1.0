<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Transport\Enums\AssignmentStatus;
use App\Filament\Resources\StudentTransportAssignmentResource\Pages;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Student;
use App\Models\StudentTransportAssignment;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentTransportAssignmentResource extends Resource
{
    protected static ?string $model = StudentTransportAssignment::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Transport';

    protected static ?string $navigationLabel = 'Affectations transport';

    protected static ?string $modelLabel = 'Affectation transport';

    protected static ?string $pluralModelLabel = 'Affectations transport';

    protected static ?int $navigationSort = 55;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Élève et itinéraire')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Élève')
                            ->options(fn () => Student::all()->map(fn (Student $student) => [
                                'value' => $student->id,
                                'label' => trim($student->first_name.' '.$student->last_name).' ('.$student->student_number.')',
                            ])->pluck('label', 'value'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('route_id')
                            ->label('Itinéraire')
                            ->options(fn () => Route::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                SectionComponent::make('Arrêts')
                    ->schema([
                        Forms\Components\Select::make('pickup_stop_id')
                            ->label('Arrêt de prise en charge')
                            ->options(fn () => RouteStop::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('dropoff_stop_id')
                            ->label('Arrêt de dépôt')
                            ->options(fn () => RouteStop::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                SectionComponent::make('Période académique')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Année académique')
                            ->options(fn () => AcademicYear::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('academic_term_id')
                            ->label('Trimestre académique')
                            ->options(fn () => AcademicTerm::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                SectionComponent::make('Frais et statut')
                    ->schema([
                        Forms\Components\TextInput::make('monthly_fee')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Select::make('status')
                            ->options(AssignmentStatus::labels())
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student')
                    ->label('Élève')
                    ->getStateUsing(fn (StudentTransportAssignment $record): string => trim($record->student->first_name.' '.$record->student->last_name))
                    ->searchable(),
                TextColumn::make('route.name')
                    ->label('Itinéraire')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pickupStop.name')
                    ->label('Arrêt de prise en charge')
                    ->toggleable(),
                TextColumn::make('dropoffStop.name')
                    ->label('Arrêt de dépôt')
                    ->toggleable(),
                TextColumn::make('monthly_fee')
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('academicYear.name')
                    ->label('Année académique')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (AssignmentStatus $state): string => AssignmentStatus::colors()[$state->value] ?? 'gray')
                    ->icon(fn (AssignmentStatus $state): string => AssignmentStatus::icons()[$state->value] ?? 'heroicon-o-question-mark'),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AssignmentStatus::labels()),
                SelectFilter::make('route_id')
                    ->label('Itinéraire')
                    ->options(fn () => Route::pluck('name', 'id')),
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
            'index' => Pages\ListStudentTransportAssignments::route('/'),
            'create' => Pages\CreateStudentTransportAssignment::route('/create'),
            'edit' => Pages\EditStudentTransportAssignment::route('/{record}/edit'),
        ];
    }
}
