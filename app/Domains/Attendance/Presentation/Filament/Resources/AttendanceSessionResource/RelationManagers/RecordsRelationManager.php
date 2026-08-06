<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Presentation\Filament\Resources\AttendanceSessionResource\RelationManagers;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'records';

    protected static ?string $title = 'Présences';

    protected static ?string $recordTitleAttribute = 'uuid';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enrollment_id')
                    ->label('Inscription')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AttendanceStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('late_minutes')
                    ->label('Retard (min)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('recorded_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AttendanceStatus::class),
            ]);
    }
}
