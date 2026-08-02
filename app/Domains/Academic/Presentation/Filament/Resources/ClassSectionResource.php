<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource\Pages;
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

class ClassSectionResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = ClassSection::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Sections';

    protected static ?string $modelLabel = 'Section';

    protected static ?string $pluralModelLabel = 'Sections';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 80;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Détails de la section')
                    ->schema([
                        Forms\Components\Select::make('school_class_id')
                            ->label('Classe')
                            ->options(fn (): array => SchoolClass::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all())
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(50)
                            ->helperText('ex., A, B, C'),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->default(30),
                    ])->columns(2),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(EntityStatus::class)
                            ->default(EntityStatus::Active)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Classe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (EntityStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(EntityStatus::class),
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
            'index' => Pages\ListClassSections::route('/'),
            'create' => Pages\CreateClassSection::route('/create'),
            'edit' => Pages\EditClassSection::route('/{record}/edit'),
        ];
    }
}
