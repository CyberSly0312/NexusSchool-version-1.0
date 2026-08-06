<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources;

use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentStatistics;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentStatisticsResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentStatisticsResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AssessmentStatistics::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Évaluations';

    protected static ?string $navigationLabel = 'Statistiques';

    protected static ?string $modelLabel = 'Statistique';

    protected static ?string $pluralModelLabel = 'Statistiques';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Statistiques')
                    ->schema([
                        Forms\Components\Select::make('assessment_id')->label('Évaluation')
                            ->options(fn (): array => Assessment::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('title', 'id')
                                ->all())
                            ->searchable(),
                        Forms\Components\TextInput::make('total_scores')->label('Nombre de notes')
                            ->numeric(),
                        Forms\Components\TextInput::make('average')->label('Moyenne')
                            ->numeric(),
                        Forms\Components\TextInput::make('minimum')->label('Minimum')
                            ->numeric(),
                        Forms\Components\TextInput::make('maximum')->label('Maximum')
                            ->numeric(),
                        Forms\Components\TextInput::make('median')->label('Médiane')
                            ->numeric(),
                        Forms\Components\TextInput::make('pass_rate')->label('Taux de réussite')
                            ->numeric()
                            ->suffix('%'),
                        Forms\Components\TextInput::make('completion_rate')->label('Taux de complétion')
                            ->numeric()
                            ->suffix('%'),
                        Forms\Components\DateTimePicker::make('generated_at')->label('Généré le')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('generated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('assessment_id')
                    ->label('Évaluation')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_scores')
                    ->label('Notes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average')
                    ->label('Moyenne')
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum')
                    ->label('Min')
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum')
                    ->label('Max')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pass_rate')
                    ->label('Réussite')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1).' %'),
                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Généré le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentStatistics::route('/'),
        ];
    }
}
