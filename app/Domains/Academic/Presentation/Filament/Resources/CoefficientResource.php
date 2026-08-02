<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\Coefficient;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CoefficientResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Coefficient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Coefficients';

    protected static ?string $modelLabel = 'Coefficient';

    protected static ?string $pluralModelLabel = 'Coefficients';

    protected static ?string $recordTitleAttribute = 'coefficient';

    protected static ?int $navigationSort = 110;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Détails du coefficient')
                    ->schema([
                        Forms\Components\Select::make('school_class_id')
                            ->label('Classe')
                            ->options(fn (): array => SchoolClass::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all())
                            ->required(),
                        Forms\Components\Select::make('academic_subject_id')
                            ->label('Matière')
                            ->options(fn (): array => AcademicSubject::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all())
                            ->required(),
                        Forms\Components\TextInput::make('coefficient')
                            ->label('Coefficient')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.25),
                        Forms\Components\Toggle::make('is_optional')
                            ->label('Optionnel')
                            ->default(false),
                        Forms\Components\Textarea::make('description')
                            ->rows(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Classe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Matière')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coefficient')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_optional')
                    ->boolean()
                    ->label('Optionnel'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoefficients::route('/'),
            'create' => Pages\CreateCoefficient::route('/create'),
            'edit' => Pages\EditCoefficient::route('/{record}/edit'),
        ];
    }
}
