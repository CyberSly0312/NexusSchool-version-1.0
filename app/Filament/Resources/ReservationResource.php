<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Library\Enums\BorrowerType;
use App\Domains\Library\Enums\ReservationStatus;
use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Bibliothèque';

    protected static ?string $navigationLabel = 'Réservations';

    protected static ?string $modelLabel = 'Réservation';

    protected static ?string $pluralModelLabel = 'Réservations';

    protected static ?int $navigationSort = 36;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la réservation')
                    ->schema([
                        Forms\Components\Select::make('book_id')
                            ->label('Livre')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('borrower_id')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('borrower_type')
                            ->options(BorrowerType::class)
                            ->required(),
                    ])->columns(2),
                SectionComponent::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('reservation_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->required(),
                        Forms\Components\DatePicker::make('fulfilled_date')
                            ->nullable(),
                    ])->columns(3),
                SectionComponent::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(ReservationStatus::class)
                            ->default(ReservationStatus::Pending)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('book.title')
                    ->label('Titre du livre')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('borrower_id')
                    ->sortable(),
                TextColumn::make('borrower_type')
                    ->badge()
                    ->color(fn (BorrowerType $state): string => $state === BorrowerType::Student ? 'info' : 'success'),
                TextColumn::make('reservation_date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (ReservationStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ReservationStatus::class),
                SelectFilter::make('borrower_type')
                    ->options(BorrowerType::class),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
