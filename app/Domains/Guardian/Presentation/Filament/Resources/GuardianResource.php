<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources;

use App\Domains\Guardian\Domain\Enums\GuardianStatus;
use App\Domains\Guardian\Domain\Enums\PreferredContactMethod;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource\Pages;
use App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource\RelationManagers\RelationshipsRelationManager;
use App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource\RelationManagers\StudentsRelationManager;
use App\Traits\ScopedByTenant;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GuardianResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Guardian::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des tuteurs';

    protected static ?string $navigationLabel = 'Tuteurs';

    protected static ?string $modelLabel = 'Tuteur';

    protected static ?string $pluralModelLabel = 'Tuteurs';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identité')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')->label('Prénom')->required()->maxLength(100),
                        Forms\Components\TextInput::make('middle_name')->label('Deuxième prénom')->maxLength(100),
                        Forms\Components\TextInput::make('last_name')->label('Nom de famille')->required()->maxLength(100),
                        Forms\Components\TextInput::make('preferred_name')->label('Prénom usuel')->maxLength(100),
                        Forms\Components\Select::make('gender')->label('Genre')
                            ->options(['male' => 'Masculin', 'female' => 'Féminin'])
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_birth')->label('Date de naissance')
                            ->before('today'),
                        Forms\Components\TextInput::make('nationality')->label('Nationalité')->maxLength(100),
                    ])->columns(3),
                Section::make('Référence & statut')
                    ->schema([
                        Forms\Components\TextInput::make('guardian_number')->label('N° tuteur')
                            ->maxLength(50)->readOnlyOn('edit'),
                        Forms\Components\Select::make('status')->label('Statut')
                            ->options(GuardianStatus::class)
                            ->default(GuardianStatus::Active)
                            ->disabledOn('edit')
                            ->dehydrated(false)
                            ->required(),
                    ])->columns(2),
                Section::make('Contact')
                    ->schema([
                        Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')->label('Téléphone')->tel()->required()->maxLength(30),
                        Forms\Components\TextInput::make('alternate_phone')->label('Téléphone secondaire')->tel()->maxLength(30),
                        Forms\Components\Select::make('preferred_contact_method')->label('Contact privilégié')
                            ->options(PreferredContactMethod::class),
                        Forms\Components\TextInput::make('preferred_communication_language')->label('Langue de communication')->maxLength(50),
                    ])->columns(3),
                Section::make('Adresse & profession')
                    ->schema([
                        Forms\Components\TextInput::make('occupation')->label('Profession')->maxLength(200),
                        Forms\Components\TextInput::make('employer')->label('Employeur')->maxLength(200),
                        Forms\Components\Textarea::make('residential_address')->label('Adresse résidentielle')->rows(2),
                        Forms\Components\Textarea::make('postal_address')->label('Adresse postale')->rows(2),
                    ])->columns(2),
                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')->label('Notes')->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guardian_number')->label('N° tuteur')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('full_name')->label('Nom complet')->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('phone_number')->label('Téléphone')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('relationships_count')->label('Élèves liés')
                    ->counts('relationships')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->color(fn (GuardianStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')->label('Créé le')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(GuardianStatus::class),
                Tables\Filters\TrashedFilter::make()->label('Archivés'),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
                Action::make('archive')
                    ->label('Archiver')
                    ->icon('heroicon-o-archive-box')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Guardian $record): bool => $record->isActive())
                    ->action(fn (Guardian $record) => $record->delete()),
                Action::make('restore')
                    ->label('Restaurer')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Guardian $record): bool => $record->isArchived())
                    ->action(fn (Guardian $record) => $record->restore()),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StudentsRelationManager::class,
            RelationshipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuardians::route('/'),
            'create' => Pages\CreateGuardian::route('/create'),
            'edit' => Pages\EditGuardian::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record = null): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
