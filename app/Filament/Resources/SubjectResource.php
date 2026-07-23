<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Subject\Enums\SubjectStatus;
use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Enseignements';

    protected static ?string $navigationLabel = 'Matières';

    protected static ?string $modelLabel = 'Matière';

    protected static ?string $pluralModelLabel = 'Matières';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la matière')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Code unique au sein de l\'école'),
                        Forms\Components\TextInput::make('short_name')
                            ->maxLength(20)
                            ->helperText('Abréviation (ex. Math, Phy)'),
                        Forms\Components\Select::make('subject_category_id')
                            ->label('Catégorie de matière')
                            ->relationship('subjectCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->label('Département')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('program_id')
                            ->label('Programme')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\TextInput::make('color')
                            ->maxLength(20)
                            ->helperText('Code couleur hexadécimal, ex. #3B82F6'),
                    ])->columns(2),
                SectionComponent::make('Classification')
                    ->schema([
                        Forms\Components\Toggle::make('is_core_subject')
                            ->label('Matière principale')
                            ->default(true),
                        Forms\Components\Toggle::make('is_elective')
                            ->label('Matière optionnelle')
                            ->default(false),
                        Forms\Components\TextInput::make('weekly_hours')
                            ->label('Heures hebdomadaires')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),
                    ])->columns(3),
                SectionComponent::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(SubjectStatus::class)
                            ->default(SubjectStatus::Active)
                            ->required(),
                    ])->columns(2),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('short_name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subjectCategory.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('weekly_hours')
                    ->sortable()
                    ->label('Heures'),
                Tables\Columns\IconColumn::make('is_core_subject')
                    ->boolean()
                    ->label('Principal')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_elective')
                    ->boolean()
                    ->label('Optionnel')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubjectStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubjectStatus::class),
                Tables\Filters\SelectFilter::make('subject_category_id')
                    ->label('Catégorie de matière')
                    ->relationship('subjectCategory', 'name'),
                Tables\Filters\SelectFilter::make('is_core_subject')
                    ->label('Matière principale')
                    ->options([
                        1 => 'Oui',
                        0 => 'Non',
                    ]),
                Tables\Filters\SelectFilter::make('is_elective')
                    ->label('Optionnel')
                    ->options([
                        1 => 'Oui',
                        0 => 'Non',
                    ]),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
