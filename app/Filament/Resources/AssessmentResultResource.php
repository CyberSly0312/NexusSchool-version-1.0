<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Gradebook\Enums\AssessmentResultStatus;
use App\Domains\Gradebook\Services\GradebookService;
use App\Filament\Resources\AssessmentResultResource\Pages;
use App\Models\AssessmentResult;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction as DeleteActionAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentResultResource extends Resource
{
    protected static ?string $model = AssessmentResult::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Gradebook';

    protected static ?string $recordTitleAttribute = 'raw_score';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Assessment Information')
                    ->schema([
                        Forms\Components\Select::make('assessment_id')
                            ->relationship('assessment', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Forms\Components\Select::make('student_id')
                            ->relationship('student', 'first_name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name} ({$record->student_number})"),
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])->columns(3),
                SectionComponent::make('Scores')
                    ->schema([
                        Forms\Components\TextInput::make('raw_score')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\TextInput::make('adjusted_score')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->nullable(),
                        Forms\Components\TextInput::make('maximum_score')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->default(100),
                        Forms\Components\TextInput::make('percentage')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('coefficient')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->maxValue(99.99)
                            ->step(0.01)
                            ->default(1.00),
                    ])->columns(3),
                SectionComponent::make('Status & Flags')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(collect(AssessmentResultStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                            ->required()
                            ->native(false),
                        Forms\Components\Toggle::make('is_absent')
                            ->label('Absent'),
                        Forms\Components\Toggle::make('is_excused')
                            ->label('Excused'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published'),
                        Forms\Components\Toggle::make('is_locked')
                            ->label('Locked'),
                        Forms\Components\Textarea::make('remarks')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('student.first_name')
                    ->label('Student')
                    ->formatStateUsing(fn ($record) => "{$record->student?->first_name} {$record->student?->last_name}")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.student_number')
                    ->label('Student #')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('assessment.title')
                    ->label('Assessment')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('raw_score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->color(fn ($state) => $state < 50 ? 'danger' : ($state < 75 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('letter_grade')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade_point')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('weighted_score')
                    ->numeric(decimalPlaces: 4)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rank_in_assessment')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        AssessmentResultStatus::Draft->value => 'gray',
                        AssessmentResultStatus::Entered->value => 'info',
                        AssessmentResultStatus::Verified->value => 'success',
                        AssessmentResultStatus::Published->value => 'primary',
                        AssessmentResultStatus::Locked->value => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_absent')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(AssessmentResultStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\SelectFilter::make('is_absent')
                    ->label('Absent')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ]),
                Tables\Filters\SelectFilter::make('assessment_id')
                    ->label('Assessment')
                    ->relationship('assessment', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(fn (AssessmentResult $record) => app(GradebookService::class)->verify($record->id))
                    ->visible(fn (AssessmentResult $record) => $record->canTransitionTo(AssessmentResultStatus::Verified)),
                DeleteActionAction::make()
                    ->visible(fn (AssessmentResult $record) => $record->status->isDeletable()),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentResults::route('/'),
            'create' => Pages\CreateAssessmentResult::route('/create'),
            'edit' => Pages\EditAssessmentResult::route('/{record}/edit'),
        ];
    }
}
