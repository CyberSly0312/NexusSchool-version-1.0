<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources;

use App\Domains\Enrollment\Infrastructure\Models\EnrollmentHistory;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentHistoryResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentHistoryResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = EnrollmentHistory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des inscriptions';

    protected static ?string $navigationLabel = 'Historique des inscriptions';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Historique des inscriptions';

    protected static ?int $navigationSort = 41;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')->label('Date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')->searchable(),
                Tables\Columns\TextColumn::make('enrollment.enrollment_number')->label('Inscription')->searchable(),
                Tables\Columns\TextColumn::make('event')->label('Événement')->badge(),
                Tables\Columns\TextColumn::make('description')->label('Description'),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollmentHistory::route('/'),
        ];
    }
}
