<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources;

use App\Domains\Assessment\Domain\Enums\ScoreStatus;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentComponent;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource\Pages;
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

class AssessmentScoreResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AssessmentScore::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-numbered-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Évaluations';

    protected static ?string $navigationLabel = 'Notes';

    protected static ?string $modelLabel = 'Note';

    protected static ?string $pluralModelLabel = 'Notes';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Note')
                    ->schema([
                        Forms\Components\Select::make('assessment_id')->label('Évaluation')
                            ->options(fn (): array => Assessment::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('title', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('assessment_component_id')->label('Composante')
                            ->options(fn (): array => AssessmentComponent::query()
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable(),
                        Forms\Components\TextInput::make('enrollment_id')->label('Inscription (ID)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('score')->label('Note')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->step(0.01)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(ScoreStatus::class)
                            ->default(ScoreStatus::Draft)
                            ->required(),
                        Forms\Components\Select::make('validation_status')
                            ->options(ScoreValidationStatus::class)
                            ->default(ScoreValidationStatus::Valid),
                        Forms\Components\Textarea::make('feedback')->label('Feedback')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('graded_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('assessment_id')
                    ->label('Évaluation')
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_id')
                    ->label('Inscription')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Note')
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ScoreStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('validation_status')
                    ->badge()
                    ->color(fn (ScoreValidationStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ScoreStatus::class),
                Tables\Filters\SelectFilter::make('validation_status')
                    ->options(ScoreValidationStatus::class),
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
            'index' => Pages\ListAssessmentScores::route('/'),
            'create' => Pages\CreateAssessmentScore::route('/create'),
            'edit' => Pages\EditAssessmentScore::route('/{record}/edit'),
        ];
    }
}
