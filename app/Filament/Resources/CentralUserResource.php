<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Role\DTOs\RoleChangeCommand;
use App\Domains\Role\DTOs\RoleChangeContext;
use App\Domains\Role\Enums\RoleChangeOperation;
use App\Domains\Role\Exceptions\RoleChangeException;
use App\Domains\Role\Services\UserRoleChangeService;
use App\Domains\School\Models\School;
use App\Filament\Resources\CentralUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CentralUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme';

    protected static ?string $navigationLabel = 'Utilisateurs centraux';

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations personnelles')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50),
                ])->columns(2),

            Section::make('Affectation')
                ->schema([
                    Forms\Components\Select::make('school_id')
                        ->label('École')
                        ->options(School::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Forms\Components\Select::make('roles')
                        ->label('Rôles')
                        ->options(Role::query()->orderBy('name')->pluck('name', 'id'))
                        ->formatStateUsing(fn (?User $record): array => $record?->roles->pluck('id')->all() ?? [])
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->dehydrated(false),
                ])->columns(2),

            Section::make('Mot de passe')
                ->visible(function (Get $get, ?User $record): bool {
                    $roles = $get('roles');

                    return ! self::isSchoolAdminTarget(is_array($roles) ? $roles : [], $record);
                })
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->maxLength(255)
                        ->dehydrated(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->required(fn ($context) => $context === 'create'),
                ]),
        ]);
    }

    /**
     * A Super Admin must never define or transmit a School Admin plaintext
     * password. The field is hidden for those accounts and any injected form
     * state is stripped again before persistence.
     *
     * @param  array<int, int|string>  $roleIds
     */
    public static function isSchoolAdminTarget(array $roleIds, ?User $record): bool
    {
        if ($record instanceof User && $record->hasRole('school_admin')) {
            return true;
        }

        if (in_array('school_admin', $roleIds, true)) {
            return true;
        }

        $ids = array_values(array_filter(
            array_map(static fn (int|string $id): int => (int) $id, $roleIds),
            static fn (int $id): bool => $id > 0,
        ));

        if ($ids === []) {
            return false;
        }

        return Role::query()
            ->whereIn('id', $ids)
            ->where('name', 'school_admin')
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, int|string>  $roleIds
     * @return array<string, mixed>
     */
    public static function stripPasswordForSchoolAdmin(array $data, array $roleIds, ?User $record): array
    {
        if (self::isSchoolAdminTarget($roleIds, $record)) {
            unset($data['password'], $data['password_confirmation']);
        }

        return $data;
    }

    /**
     * Reconcile submitted role IDs through the authoritative role-change
     * service. The form field is intentionally not a Filament relationship,
     * which prevents automatic unrestricted pivot writes.
     *
     * @param  array<int, int|string>  $roleIds
     */
    public static function syncRolesFromFormState(User $user, array $roleIds): void
    {
        $actor = auth()->user();
        if (! $actor instanceof User) {
            throw new RoleChangeException('Aucun administrateur actif n’est connecté.');
        }

        $context = RoleChangeContext::forActor($actor, null);
        $desired = array_values(array_unique(array_map('intval', $roleIds)));
        $current = $user->roles()->pluck('id')->all();

        $toAssign = array_values(array_diff($desired, $current));
        $toRemove = array_values(array_diff($current, $desired));

        DB::transaction(function () use ($user, $context, $toAssign, $toRemove): void {
            $service = app(UserRoleChangeService::class);

            if ($toAssign !== []) {
                $service->apply(
                    new RoleChangeCommand((string) $user->getKey(), $toAssign, null, RoleChangeOperation::Assign),
                    $context,
                );
            }

            if ($toRemove !== []) {
                $service->apply(
                    new RoleChangeCommand((string) $user->getKey(), $toRemove, null, RoleChangeOperation::Remove),
                    $context,
                );
            }
        });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->toggleable(),
                TextColumn::make('school.name')
                    ->label('École')
                    ->searchable()
                    ->sortable()
                    ->default('—'),
                TextColumn::make('roles.name')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'director' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('school_id')
                    ->label('École')
                    ->options(School::pluck('name', 'id'))
                    ->searchable(),
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
            'index' => Pages\ListCentralUsers::route('/'),
            'create' => Pages\CreateCentralUser::route('/create'),
            'edit' => Pages\EditCentralUser::route('/{record}/edit'),
        ];
    }
}
