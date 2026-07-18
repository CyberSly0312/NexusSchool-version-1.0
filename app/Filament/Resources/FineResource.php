<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Library\Enums\BorrowerType;
use App\Domains\Library\Enums\FineStatus;
use App\Filament\Resources\FineResource\Pages;
use App\Models\Fine;
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

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;

    protected static ?string $recordTitleAttribute = 'reason';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Library';

    protected static ?int $navigationSort = 37;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Fine Details')
                    ->schema([
                        Forms\Components\TextInput::make('borrower_id')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('borrower_type')
                            ->options(BorrowerType::class)
                            ->required(),
                        Forms\Components\TextInput::make('reason')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('currency')
                            ->required(),
                        Forms\Components\DatePicker::make('fine_date')
                            ->required()
                            ->default(now()),
                    ])->columns(2),
                SectionComponent::make('Payment')
                    ->schema([
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->prefix('currency')
                            ->default(0),
                        Forms\Components\DatePicker::make('paid_date')
                            ->nullable(),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'card' => 'Card',
                                'mobile_money' => 'Mobile Money',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('reference_number')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\TextInput::make('waived_amount')
                            ->numeric()
                            ->prefix('currency')
                            ->default(0),
                        Forms\Components\DatePicker::make('waived_date')
                            ->nullable(),
                    ])->columns(3),
                SectionComponent::make('Reference')
                    ->schema([
                        Forms\Components\TextInput::make('book_loan_id')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('processed_by')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->options(FineStatus::class)
                            ->default(FineStatus::Pending)
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
                TextColumn::make('borrower_id')
                    ->sortable(),
                TextColumn::make('borrower_type')
                    ->badge()
                    ->color(fn (BorrowerType $state): string => $state === BorrowerType::Student ? 'info' : 'success'),
                TextColumn::make('reason')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money()
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->money()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (FineStatus $state): string => $state->color()),
                TextColumn::make('fine_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(FineStatus::class),
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
            'index' => Pages\ListFines::route('/'),
            'create' => Pages\CreateFine::route('/create'),
            'edit' => Pages\EditFine::route('/{record}/edit'),
        ];
    }
}
