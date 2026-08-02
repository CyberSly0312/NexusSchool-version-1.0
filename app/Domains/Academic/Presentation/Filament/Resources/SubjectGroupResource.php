<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources;

use App\Domains\Academic\Domain\Enums\EntityStatus;
use App\Domains\Academic\Infrastructure\Models\SubjectGroup;
use App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource\Pages;
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

class SubjectGroupResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = SubjectGroup::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Groupes de matières';

    protected static ?string $modelLabel = 'Groupe de matières';

    protected static ?string $pluralModelLabel = 'Groupes de matières';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Détails du groupe de matières')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
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
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
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
            'index' => Pages\ListSubjectGroups::route('/'),
            'create' => Pages\CreateSubjectGroup::route('/create'),
            'edit' => Pages\EditSubjectGroup::route('/{record}/edit'),
        ];
    }
}
