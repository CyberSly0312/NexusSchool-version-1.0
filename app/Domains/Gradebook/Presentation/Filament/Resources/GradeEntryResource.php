<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Presentation\Filament\Resources;

use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Domains\Gradebook\Presentation\Filament\Resources\GradeEntryResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GradeEntryResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = GradeEntry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Carnets de notes';

    protected static ?string $navigationLabel = 'Notes par élève';

    protected static ?string $modelLabel = 'Note';

    protected static ?string $pluralModelLabel = 'Notes';

    protected static ?int $navigationSort = 20;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('gradebook_id', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('gradebook_id')
                    ->label('Carnet')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_id')
                    ->label('Élève')
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Pourcentage')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 2).' %')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average')
                    ->label('Moyenne')
                    ->sortable(),
                Tables\Columns\TextColumn::make('letter_grade')
                    ->label('Appréciation')
                    ->badge(),
                Tables\Columns\TextColumn::make('rank')
                    ->label('Rang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (GradeEntryStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gradebook_id')
                    ->label('Carnet de notes')
                    ->options(fn (): array => Gradebook::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->pluck('id', 'id')
                        ->all()),
                Tables\Filters\SelectFilter::make('status')
                    ->options(GradeEntryStatus::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradeEntries::route('/'),
        ];
    }
}
