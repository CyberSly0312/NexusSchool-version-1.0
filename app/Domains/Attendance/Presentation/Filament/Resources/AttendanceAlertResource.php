<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources;

use App\Domains\Attendance\Domain\Enums\AttendanceAlertStatus;
use App\Domains\Attendance\Infrastructure\Models\AttendanceAlert;
use App\Domains\Attendance\Presentation\Filament\Resources\AttendanceAlertResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceAlertResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AttendanceAlert::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Présences';

    protected static ?string $navigationLabel = 'Alertes';

    protected static ?string $modelLabel = 'Alerte';

    protected static ?string $pluralModelLabel = 'Alertes';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 53;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('detected_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('alert_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low_attendance', 'high_late_percentage', 'consecutive_absences' => 'danger',
                        'consecutive_lates' => 'warning',
                        'risk' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('enrollment_id')
                    ->label('Inscription')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('threshold')
                    ->label('Seuil')
                    ->numeric(),
                Tables\Columns\TextColumn::make('observed_value')
                    ->label('Observé')
                    ->numeric(),
                Tables\Columns\TextColumn::make('detected_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AttendanceAlertStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AttendanceAlertStatus::class),
                Tables\Filters\SelectFilter::make('alert_type')
                    ->label('Type'),
            ])
            ->actions([
                Action::make('resolve')
                    ->label('Résoudre')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn (AttendanceAlert $record): bool => $record->isOpen())
                    ->action(fn (AttendanceAlert $record): mixed => $record->update([
                        'status' => AttendanceAlertStatus::Resolved,
                    ])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceAlerts::route('/'),
        ];
    }
}
