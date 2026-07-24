<?php

declare(strict_types=1);

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\StudentEnrollment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentEnrollmentsWidget extends TableWidget
{
    protected static ?int $sort = 6;

    protected static ?string $heading = 'Inscriptions récentes';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StudentEnrollment::with(['student', 'academicClass', 'academicYear'])
                    ->where('school_id', tenant('id'))
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.student_number')
                    ->label('N°')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Élève')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicClass.name')
                    ->label('Classe')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Année')
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string|array|null => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
