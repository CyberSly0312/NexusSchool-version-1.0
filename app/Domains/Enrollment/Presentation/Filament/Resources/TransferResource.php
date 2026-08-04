<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources;

use App\Domains\Enrollment\Infrastructure\Models\EnrollmentTransfer;
use App\Domains\Enrollment\Presentation\Filament\Resources\TransferResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransferResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = EnrollmentTransfer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des inscriptions';

    protected static ?string $navigationLabel = 'Transferts';

    protected static ?string $modelLabel = 'Transfert';

    protected static ?string $pluralModelLabel = 'Transferts';

    protected static ?int $navigationSort = 43;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')->searchable(),
                Tables\Columns\TextColumn::make('transfer_type')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('fromClass.name')->label('Classe d\'origine'),
                Tables\Columns\TextColumn::make('toClass.name')->label('Classe cible'),
                Tables\Columns\TextColumn::make('to_school_id')->label('École cible'),
                Tables\Columns\TextColumn::make('reason')->label('Motif')->limit(40),
                Tables\Columns\TextColumn::make('transfer_date')->label('Le')->date()->sortable(),
            ])
            ->defaultSort('transfer_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransfers::route('/'),
        ];
    }
}
