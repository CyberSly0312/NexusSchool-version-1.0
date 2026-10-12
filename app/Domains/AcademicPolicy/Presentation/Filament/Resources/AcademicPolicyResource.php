<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Infrastructure\Models\AcademicPolicy;
use App\Domains\AcademicPolicy\Presentation\Filament\Resources\AcademicPolicyResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AcademicPolicyResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AcademicPolicy::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bookmark';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Politiques académiques';

    protected static ?string $modelLabel = 'Politique académique';

    protected static ?string $pluralModelLabel = 'Politiques académiques';

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?int $navigationSort = 120;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Politique')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Clé')
                            ->helperText('Identifiant technique unique (ex : grading_scale, honor_threshold).')
                            ->required()
                            ->maxLength((int) config('nexus-academic-policy.policy.key_max_length', 100)),
                        Forms\Components\TextInput::make('label')
                            ->label('Libellé')
                            ->required()
                            ->maxLength((int) config('nexus-academic-policy.policy.label_max_length', 255)),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                    ])->columns(2),
                Section::make('Valeur')
                    ->schema([
                        Forms\Components\Select::make('value_type')
                            ->label('Type de valeur')
                            ->options(collect(AcademicPolicyValueType::cases())->mapWithKeys(
                                fn (AcademicPolicyValueType $case): array => [$case->value => $case->label()],
                            )->all())
                            ->default(AcademicPolicyValueType::String->value)
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('value')
                            ->label('Valeur')
                            ->visible(fn (Get $get): bool => ! in_array($get('value_type'), [AcademicPolicyValueType::Boolean->value, AcademicPolicyValueType::Json->value], true)),
                        Forms\Components\Toggle::make('value')
                            ->label('Valeur')
                            ->visible(fn (Get $get): bool => $get('value_type') === AcademicPolicyValueType::Boolean->value),
                        Forms\Components\KeyValue::make('value')
                            ->label('Valeur')
                            ->keyLabel('Clé')
                            ->valueLabel('Valeur')
                            ->visible(fn (Get $get): bool => $get('value_type') === AcademicPolicyValueType::Json->value),
                    ])->columns(2),
                Section::make('Portée')
                    ->schema([
                        Forms\Components\Select::make('scope')
                            ->label('Portée')
                            ->options(collect(AcademicPolicyScope::cases())->mapWithKeys(
                                fn (AcademicPolicyScope $case): array => [$case->value => $case->label()],
                            )->all())
                            ->default(AcademicPolicyScope::School->value)
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Année académique')
                            ->options(fn (): array => AcademicCalendarYear::query()->forSchool((string) (optional(tenant())->id ?? ''))->pluck('name', 'id')->all())
                            ->visible(fn (Get $get): bool => $get('scope') === AcademicPolicyScope::AcademicYear->value),
                        Forms\Components\Select::make('academic_subject_id')
                            ->label('Matière')
                            ->options(fn (): array => AcademicSubject::query()->forSchool((string) (optional(tenant())->id ?? ''))->pluck('name', 'id')->all())
                            ->visible(fn (Get $get): bool => $get('scope') === AcademicPolicyScope::Subject->value),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Clé')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value_type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valeur')
                    ->formatStateUsing(fn (mixed $state): string => is_array($state) ? json_encode($state) : (string) $state)
                    ->limit(40),
                Tables\Columns\TextColumn::make('scope')
                    ->label('Portée'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
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
            'index' => Pages\ListAcademicPolicies::route('/'),
            'create' => Pages\CreateAcademicPolicy::route('/create'),
            'edit' => Pages\EditAcademicPolicy::route('/{record}/edit'),
        ];
    }
}
