<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Guardian\Enums\GuardianStatus;
use App\Domains\Guardian\Enums\PreferredContactMethod;
use App\Domains\Guardian\Enums\RelationshipType;
use App\Filament\Resources\GuardianResource\Pages;
use App\Models\Guardian;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Traits\ScopedByTenant;

class GuardianResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Guardian::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'École';

    protected static ?string $navigationLabel = 'Tuteurs';

    protected static ?string $modelLabel = 'Tuteur';

    protected static ?string $pluralModelLabel = 'Tuteurs';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Informations personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('guardian_number')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Identifiant tuteur unique dans l\'établissement'),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('middle_name')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\TextInput::make('preferred_name')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Masculin',
                                'female' => 'Féminin',
                                'other' => 'Autre',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->nullable()
                            ->maxDate(now()),
                        Forms\Components\TextInput::make('nationality')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\Select::make('relationship_type')
                            ->options(collect(RelationshipType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                    ])->columns(3),
                SectionComponent::make('Coordonnées')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('phone_number')
                            ->required()
                            ->maxLength(30)
                            ->tel(),
                        Forms\Components\TextInput::make('alternate_phone')
                            ->maxLength(30)
                            ->nullable()
                            ->tel(),
                        Forms\Components\Select::make('preferred_contact_method')
                            ->options(collect(PreferredContactMethod::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->default('phone'),
                        Forms\Components\Select::make('preferred_communication_language')
                            ->options([
                                'English' => 'Anglais',
                                'French' => 'Français',
                                'Spanish' => 'Espagnol',
                            ])
                            ->nullable(),
                        Forms\Components\Textarea::make('residential_address')
                            ->rows(2)
                            ->nullable(),
                        Forms\Components\Textarea::make('postal_address')
                            ->rows(2)
                            ->nullable(),
                        Forms\Components\Textarea::make('emergency_contact')
                            ->rows(2)
                            ->nullable(),
                    ])->columns(2),
                SectionComponent::make('Informations professionnelles')
                    ->schema([
                        Forms\Components\TextInput::make('occupation')
                            ->maxLength(200)
                            ->nullable(),
                        Forms\Components\TextInput::make('employer')
                            ->maxLength(200)
                            ->nullable(),
                        Forms\Components\Toggle::make('is_primary_guardian')
                            ->label('Tuteur principal')
                            ->default(false),
                    ])->columns(3),
                SectionComponent::make('Statut et notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(GuardianStatus::class)
                            ->default(GuardianStatus::Active)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guardian_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('relationship_type')
                    ->formatStateUsing(fn ($state): string => $state?->label() ?? '')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_primary_guardian')
                    ->boolean()
                    ->label('Principal')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (GuardianStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('last_name')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(GuardianStatus::class),
                Tables\Filters\SelectFilter::make('relationship_type')
                    ->options(collect(RelationshipType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Tables\Filters\SelectFilter::make('is_primary_guardian')
                    ->label('Tuteur principal')
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
            'index' => Pages\ListGuardians::route('/'),
            'create' => Pages\CreateGuardian::route('/create'),
            'edit' => Pages\EditGuardian::route('/{record}/edit'),
        ];
    }
}
