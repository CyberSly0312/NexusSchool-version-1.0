<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources;

use App\Domains\Assessment\Infrastructure\Models\AssessmentTemplate;
use App\Domains\Assessment\Infrastructure\Models\AssessmentType;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource\Pages;
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

class AssessmentTemplateResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AssessmentTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'Évaluations';

    protected static ?string $navigationLabel = 'Modèles';

    protected static ?string $modelLabel = 'Modèle';

    protected static ?string $pluralModelLabel = 'Modèles';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Modèle')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('assessment_type_id')->label('Type d\'évaluation')
                            ->options(fn (): array => AssessmentType::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('default_maximum_score')->label('Note maximale par défaut')
                            ->numeric()
                            ->minValue(0)
                            ->default(20)
                            ->required(),
                        Forms\Components\TextInput::make('default_passing_score')->label('Note de passage par défaut')
                            ->numeric()
                            ->minValue(0)
                            ->default(10),
                        Forms\Components\TextInput::make('default_weight')->label('Coefficient par défaut')
                            ->numeric()
                            ->minValue(0)
                            ->default(1),
                        Forms\Components\Textarea::make('description')->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('structure')->label('Structure (JSON)')
                            ->json()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')->label('Actif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_maximum_score')
                    ->label('Max')
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_weight')
                    ->label('Coeff.')
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
            'index' => Pages\ListAssessmentTemplates::route('/'),
            'create' => Pages\CreateAssessmentTemplate::route('/create'),
            'edit' => Pages\EditAssessmentTemplate::route('/{record}/edit'),
        ];
    }
}
