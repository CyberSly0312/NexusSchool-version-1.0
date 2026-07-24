<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Plan;
use App\Models\Subscription;
use App\Domains\School\Models\School;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme';

    protected static ?string $navigationLabel = 'Abonnements';

    protected static ?string $modelLabel = 'Abonnement';

    protected static ?string $pluralModelLabel = 'Abonnements';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttributeFor = null;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Détails de l\'abonnement')
                ->schema([
                    Forms\Components\Select::make('school_id')
                        ->label('École')
                        ->options(School::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->preload(),
                    Forms\Components\Select::make('plan_id')
                        ->label('Formule')
                        ->options(Plan::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->preload(),
                ])->columns(2),

            Section::make('Période')
                ->schema([
                    Forms\Components\DatePicker::make('starts_at')
                        ->label('Date de début')
                        ->required()
                        ->native(false),
                    Forms\Components\DatePicker::make('expires_at')
                        ->label('Date de fin')
                        ->native(false),
                    Forms\Components\Toggle::make('auto_renew')
                        ->label('Renouvellement automatique')
                        ->default(true),
                ])->columns(3),

            Section::make('Paiement')
                ->schema([
                    Forms\Components\TextInput::make('payment_amount')
                        ->label('Montant payé')
                        ->numeric()
                        ->prefix('FCFA'),
                    Forms\Components\TextInput::make('payment_method')
                        ->label('Mode de paiement')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('payment_reference')
                        ->label('Référence')
                        ->maxLength(100),
                ])->columns(3),

            Section::make('Statut')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'active' => 'Actif',
                            'expired' => 'Expiré',
                            'suspended' => 'Suspendu',
                            'cancelled' => 'Annulé',
                        ])
                        ->required()
                        ->default('active'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('École')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan.name')
                    ->label('Formule')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'warning',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('starts_at')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('payment_amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state): string => $state ? number_format((float) $state, 0, ',', ' ') . ' FCFA' : '-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'expired' => 'Expiré',
                        'suspended' => 'Suspendu',
                        'cancelled' => 'Annulé',
                    ]),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
