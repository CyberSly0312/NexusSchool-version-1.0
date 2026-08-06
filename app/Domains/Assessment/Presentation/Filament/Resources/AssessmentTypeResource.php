<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources;

use App\Domains\Assessment\Domain\Enums\AssessmentType;
use App\Domains\Assessment\Infrastructure\Models\AssessmentType as AssessmentTypeModel;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTypeResource\Pages;
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

class AssessmentTypeResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AssessmentTypeModel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Évaluations';

    protected static ?string $navigationLabel = 'Types d\'évaluation';

    protected static ?string $modelLabel = 'Type d\'évaluation';

    protected static ?string $pluralModelLabel = 'Types d\'évaluation';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Type')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')->label('Code')
                            ->maxLength(50),
                        Forms\Components\Select::make('type_key')->label('Type canonique')
                            ->options(AssessmentType::options())
                            ->default(AssessmentType::Custom)
                            ->required(),
                        Forms\Components\Textarea::make('description')->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')->label('Actif')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')->label('Ordre')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_key')
                    ->label('Type canonique')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentTypes::route('/'),
            'create' => Pages\CreateAssessmentType::route('/create'),
            'edit' => Pages\EditAssessmentType::route('/{record}/edit'),
        ];
    }
}
