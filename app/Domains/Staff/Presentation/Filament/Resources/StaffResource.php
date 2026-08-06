<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources;

use App\Domains\Staff\Domain\Enums\StaffSpecialization;
use App\Domains\Staff\Domain\Enums\StaffStatus;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Presentation\Filament\Resources\StaffResource\Pages;
use App\Domains\Staff\Presentation\Filament\Resources\StaffResource\RelationManagers\AssignmentsRelationManager;
use App\Domains\Staff\Presentation\Filament\Resources\StaffResource\RelationManagers\EmploymentsRelationManager;
use App\Domains\Staff\Presentation\Filament\Resources\StaffResource\RelationManagers\QualificationsRelationManager;
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

class StaffResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = Staff::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Ressources humaines';

    protected static ?string $navigationLabel = 'Personnel';

    protected static ?string $modelLabel = 'Membre du personnel';

    protected static ?string $pluralModelLabel = 'Personnel';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 10;

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
                        Forms\Components\FileUpload::make('photo')->label('Photo')->image(),
                    ])->columns(4),
                Section::make('Référence & statut')
                    ->schema([
                        Forms\Components\TextInput::make('staff_number')->label('N° matricule')
                            ->maxLength(50)->readOnlyOn('edit'),
                        Forms\Components\Select::make('specialization')->label('Spécialisation')
                            ->options(StaffSpecialization::class)
                            ->default(StaffSpecialization::Other),
                        Forms\Components\Select::make('status')->label('Statut')
                            ->options(StaffStatus::class)
                            ->default(StaffStatus::Active)
                            ->disabledOn('edit')
                            ->dehydrated(false)
                            ->required(),
                    ])->columns(3),
                Section::make('Contact')
                    ->schema([
                        Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')->label('Téléphone')->tel()->required()->maxLength(30),
                        Forms\Components\TextInput::make('alternate_phone')->label('Téléphone secondaire')->tel()->maxLength(30),
                        Forms\Components\Textarea::make('residential_address')->label('Adresse résidentielle')->rows(2),
                    ])->columns(3),
                Section::make('Identifiants légaux & financiers')
                    ->schema([
                        Forms\Components\TextInput::make('national_id')->label('N° CNIB')->maxLength(50),
                        Forms\Components\TextInput::make('tax_number')->label('N° IFU')->maxLength(50),
                        Forms\Components\TextInput::make('bank_name')->label('Banque')->maxLength(100),
                        Forms\Components\TextInput::make('bank_account')->label('Compte bancaire')->maxLength(50),
                    ])->columns(4),
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
                Tables\Columns\TextColumn::make('staff_number')->label('N° matricule')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('full_name')->label('Nom complet')->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('specialization')->label('Spécialisation')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('phone_number')->label('Téléphone')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('employments_count')->label('Contrats')
                    ->counts('employments')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->color(fn (StaffStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')->label('Créé le')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(StaffStatus::class),
                Tables\Filters\SelectFilter::make('specialization')->label('Spécialisation')->options(StaffSpecialization::class),
                Tables\Filters\TrashedFilter::make()->label('Archivés'),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
                Action::make('suspend')
                    ->label('Suspendre')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Staff $record): bool => $record->isActive())
                    ->action(fn (Staff $record) => app(\App\Domains\Staff\Application\Services\StaffService::class)->suspend($record, actor: auth()->user())),
                Action::make('restore')
                    ->label('Réintégrer')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Staff $record): bool => $record->isSuspended())
                    ->action(fn (Staff $record) => app(\App\Domains\Staff\Application\Services\StaffService::class)->restore($record, auth()->user())),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmploymentsRelationManager::class,
            AssignmentsRelationManager::class,
            QualificationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
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
