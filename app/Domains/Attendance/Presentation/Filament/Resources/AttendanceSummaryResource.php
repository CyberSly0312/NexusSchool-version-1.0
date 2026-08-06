<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources;

use App\Domains\Attendance\Domain\ValueObjects\AttendancePeriod;
use App\Domains\Attendance\Infrastructure\Models\AttendanceSummary;
use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSummaryResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceSummaryResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AttendanceSummary::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Présences';

    protected static ?string $navigationLabel = 'Rapports';

    protected static ?string $modelLabel = 'Rapport';

    protected static ?string $pluralModelLabel = 'Rapports';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 54;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('calculated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('enrollment_id')
                    ->label('Inscription')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('period_type')
                    ->label('Période')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        AttendancePeriod::TYPE_MONTHLY => 'info',
                        AttendancePeriod::TYPE_SEMESTER => 'warning',
                        AttendancePeriod::TYPE_YEARLY => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Du')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_end')
                    ->label('Au')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sessions_count')
                    ->label('Séances')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_percentage')
                    ->label('Taux')
                    ->suffix('%')
                    ->color(fn (float $state): string => $state >= 75 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('late_percentage')
                    ->label('Retards')
                    ->suffix('%')
                    ->color(fn (float $state): string => $state <= 15 ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('risk_score')
                    ->label('Risque')
                    ->suffix(' / 100')
                    ->color(fn (float $state): string => $state >= 75 ? 'danger' : ($state >= 40 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('calculated_at')
                    ->label('Calculé le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period_type')
                    ->label('Période')
                    ->options([
                        AttendancePeriod::TYPE_MONTHLY => 'Mensuel',
                        AttendancePeriod::TYPE_SEMESTER => 'Semestre',
                        AttendancePeriod::TYPE_YEARLY => 'Annuel',
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceSummaries::route('/'),
        ];
    }
}
