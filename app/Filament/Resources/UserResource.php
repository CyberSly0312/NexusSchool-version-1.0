<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\User\Enums\UserStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Utilisateurs';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 30;

    protected static function isSchoolAdminPanel(): bool
    {
        return filament()->getCurrentPanel()->getId() === 'school-admin';
    }

    /**
     * Dedicated schema for the school-admin staff invitation flow.
     *
     * This is deliberately not the generic create form: the actor never sees
     * or supplies a school, status, password or password confirmation. The role
     * list is derived from the service allowlist but is only a UI aid; the
     * service remains the authoritative server-side guard.
     *
     * @return Schema
     */
    public static function schoolAdminProvisioningForm(Schema $schema): Schema
    {
        $roles = [
            'teacher' => 'Enseignant',
            'secretary' => 'Secrétaire',
            'accountant' => 'Comptable',
        ];

        return $schema
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('last_name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->label('Adresse e-mail')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->maxLength(50)
                    ->nullable(),
                Forms\Components\Select::make('role')
                    ->label('Rôle')
                    ->options($roles)
                    ->in(array_keys($roles))
                    ->required()
                    ->searchable()
                    ->preload(),
            ])
            ->columns(2);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informations personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50)
                            ->nullable(),
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->nullable(),
                    ])->columns(2),

                Section::make('Compte')
                    ->schema([
                        ...(! self::isSchoolAdminPanel() ? [
                            Forms\Components\Select::make('school_id')
                                ->relationship('school', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ] : []),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Actif',
                                'inactive' => 'Inactif',
                                'pending' => 'En attente',
                                'suspended' => 'Suspendu',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\DateTimePicker::make('last_login_at')
                            ->label('Dernière connexion')
                            ->disabled(),
                    ])->columns(2),

                Section::make('Mot de passe')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->dehydrated(false),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isSchoolAdmin = self::isSchoolAdminPanel();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                ...(! $isSchoolAdmin ? [
                    Tables\Columns\TextColumn::make('school.name')
                        ->label('École')
                        ->sortable()
                        ->toggleable(),
                ] : []),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'active', UserStatus::Active => 'success',
                        'pending', UserStatus::Pending => 'warning',
                        'inactive', UserStatus::Inactive => 'gray',
                        'suspended', UserStatus::Suspended => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'pending' => 'En attente',
                        'suspended' => 'Suspendu',
                    ]),
                ...(! $isSchoolAdmin ? [
                    Tables\Filters\SelectFilter::make('school_id')
                        ->relationship('school', 'name')
                        ->searchable()
                        ->preload()
                        ->label('École'),
                ] : []),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (self::isSchoolAdminPanel()) {
            $tenantId = tenant()?->id;
            if ($tenantId) {
                $query->where('school_id', $tenantId);
            } else {
                // Fail closed: never expose every school's users when the
                // school-admin panel runs without an initialized tenant.
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
