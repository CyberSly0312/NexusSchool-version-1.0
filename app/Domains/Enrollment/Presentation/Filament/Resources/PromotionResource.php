<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources;

use App\Domains\Enrollment\Infrastructure\Models\EnrollmentPromotion;
use App\Domains\Enrollment\Presentation\Filament\Resources\PromotionResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = EnrollmentPromotion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des inscriptions';

    protected static ?string $navigationLabel = 'Passages & redoublements';

    protected static ?string $modelLabel = 'Passage';

    protected static ?string $pluralModelLabel = 'Passages & redoublements';

    protected static ?int $navigationSort = 42;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')->searchable(),
                Tables\Columns\TextColumn::make('academicCalendarYear.name')->label('Année scolaire')->sortable(),
                Tables\Columns\TextColumn::make('fromClass.name')->label('Classe d\'origine'),
                Tables\Columns\TextColumn::make('toClass.name')->label('Classe cible'),
                Tables\Columns\TextColumn::make('decision')->label('Décision')->badge(),
                Tables\Columns\TextColumn::make('promoted_on')->label('Le')->date()->sortable(),
                Tables\Columns\TextColumn::make('academic_result')
                    ->label('Moyenne')
                    ->formatStateUsing(fn ($state): string => is_array($state) && isset($state['average'])
                        ? number_format((float) $state['average'], 2)
                        : '—'),
            ])
            ->defaultSort('promoted_on', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
        ];
    }
}
