<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource\Pages;
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

class GradebookResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Gradebook::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Carnets de notes';

    protected static ?string $navigationLabel = 'Carnets de notes';

    protected static ?string $modelLabel = 'Carnet de notes';

    protected static ?string $pluralModelLabel = 'Carnets de notes';

    protected static ?string $recordTitleAttribute = 'id';

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
                            ->searchable(),
                    ])->columns(2),
                Section::make('Grading')
                    ->schema([
                        Forms\Components\TextInput::make('coefficient')->label('Coefficient')
                            ->numeric()
                            ->minValue(0.01)
                            ->default(1)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(GradebookStatus::class)
                            ->default(GradebookStatus::Draft)
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
                Tables\Columns\TextColumn::make('academic_subject_id')
                    ->label('Matière')
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_class_id')
                    ->label('Classe')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_calendar_term_id')
                    ->label('Période')
                    ->sortable(),
                Tables\Columns\TextColumn::make('coefficient')
                    ->label('Coeff.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('entries_count')
                    ->label('Élèves')
                    ->counts('entries')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (GradebookStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('locked_at')
                    ->label('Verrouillé')
                    ->dateTime()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(GradebookStatus::class),
                Tables\Filters\SelectFilter::make('academic_subject_id')
                    ->label('Matière')
                    ->options(fn (): array => AcademicSubject::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->pluck('name', 'id')
                        ->all()),
                Tables\Filters\SelectFilter::make('school_class_id')
                    ->label('Classe')
                    ->options(fn (): array => SchoolClass::query()
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
            'index' => Pages\ListGradebooks::route('/'),
            'create' => Pages\CreateGradebook::route('/create'),
            'edit' => Pages\EditGradebook::route('/{record}/edit'),
        ];
    }
}
