<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Models\CentralDomain;
use App\Domains\School\Models\School;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DomainResource extends Resource
{
    protected static ?string $model = CentralDomain::class;

    protected static ?string $recordTitleAttribute = 'domain';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme';

    protected static ?string $navigationLabel = 'Domaines';

    protected static ?string $modelLabel = 'Domaine';

    protected static ?string $pluralModelLabel = 'Domaines';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Configuration du domaine')
                ->schema([
                    Forms\Components\TextInput::make('domain')
                        ->label('Domaine')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('ecole.nexusschool.com'),
                    Forms\Components\Select::make('tenant_id')
                        ->label('École')
                        ->options(School::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->preload(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')
                    ->label('Domaine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tenant.name')
                    ->label('École')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('domain')
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
