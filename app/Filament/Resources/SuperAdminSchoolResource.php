<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Models\School;
use App\Filament\Resources\SuperAdminSchoolResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuperAdminSchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme';

    protected static ?string $navigationLabel = 'Écoles';

    protected static ?string $modelLabel = 'École';

    protected static ?string $pluralModelLabel = 'Écoles';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations de l\'école')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('school_type')
                        ->label('Type')
                        ->options(collect(\App\Domains\School\Enums\SchoolType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray()),
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(collect(SchoolStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray())
                        ->required()
                        ->default(SchoolStatus::Pending->value),
                ])->columns(2),

            Section::make('Contact')
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50),
                    Forms\Components\Textarea::make('address')
                        ->label('Adresse')
                        ->rows(2),
                ])->columns(2),

            Section::make('Localisation')
                ->schema([
                    Forms\Components\TextInput::make('city')
                        ->label('Ville')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('state')
                        ->label('Région')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('country')
                        ->label('Pays')
                        ->default('Burkina Faso')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('postal_code')
                        ->label('Code postal')
                        ->maxLength(20),
                ])->columns(2),

            Section::make('Paramètres système')
                ->schema([
                    Forms\Components\TextInput::make('database')
                        ->label('Base de données')
                        ->maxLength(100)
                        ->disabled(),
                    Forms\Components\TextInput::make('domain')
                        ->label('Domaine')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('timezone')
                        ->label('Fuseau horaire')
                        ->default('Africa/Ouagadougou')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XOF')
                        ->maxLength(10),
                    Forms\Components\TextInput::make('locale')
                        ->label('Langue')
                        ->default('fr')
                        ->maxLength(10),
                    Forms\Components\TextInput::make('registration_number')
                        ->label('N° d\'inscription')
                        ->maxLength(100),
                ])->columns(3),

            Section::make('Administrateur de l\'école')
                ->schema([
                    Forms\Components\Toggle::make('create_admin')
                        ->label('Créer un administrateur')
                        ->default(false)
                        ->reactive(),
                    Forms\Components\TextInput::make('admin_name')
                        ->label('Nom complet')
                        ->maxLength(255)
                        ->visible(fn ($get) => $get('create_admin')),
                    Forms\Components\TextInput::make('admin_email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->visible(fn ($get) => $get('create_admin')),
                ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Ville')
                    ->searchable(),
                TextColumn::make('country')
                    ->label('Pays')
                    ->sortable(),
                TextColumn::make('school_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state): string => $state instanceof \BackedEnum ? $state->label() : ucfirst((string) $state)),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (SchoolStatus $state): string => $state->color()),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('students_count')
                    ->label('Élèves')
                    ->counts('students')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(SchoolStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray()),
                SelectFilter::make('school_type')
                    ->label('Type')
                    ->options(collect(\App\Domains\School\Enums\SchoolType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->toArray()),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
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
            'index' => Pages\ListSuperAdminSchools::route('/'),
            'create' => Pages\CreateSuperAdminSchool::route('/create'),
            'edit' => Pages\EditSuperAdminSchool::route('/{record}/edit'),
        ];
    }
}
