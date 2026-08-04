<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources;

use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;
use App\Domains\Enrollment\Presentation\Filament\Resources\GraduationResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GraduationResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = EnrollmentGraduation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des inscriptions';

    protected static ?string $navigationLabel = 'Diplômes';

    protected static ?string $modelLabel = 'Diplôme';

    protected static ?string $pluralModelLabel = 'Diplômes';

    protected static ?int $navigationSort = 44;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')->searchable(),
                Tables\Columns\TextColumn::make('educationLevel.name')->label('Niveau'),
                Tables\Columns\TextColumn::make('decision')->label('Décision')->badge(),
                Tables\Columns\TextColumn::make('graduated_on')->label('Le')->date()->sortable(),
                Tables\Columns\TextColumn::make('certificate_reference')->label('Réf. certificat'),
            ])
            ->defaultSort('graduated_on', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGraduations::route('/'),
        ];
    }
}
