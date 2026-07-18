<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Library\Enums\BookCopyCondition;
use App\Domains\Library\Enums\BookCopyStatus;
use App\Filament\Resources\BookCopyResource\Pages;
use App\Models\BookCopy;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookCopyResource extends Resource
{
    protected static ?string $model = BookCopy::class;

    protected static ?string $recordTitleAttribute = 'barcode';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Library';

    protected static ?int $navigationSort = 34;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Copy Details')
                    ->schema([
                        Forms\Components\Select::make('book_id')
                            ->label('Book')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('barcode')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('inventory_number')
                            ->maxLength(100),
                    ])->columns(2),
                SectionComponent::make('Acquisition')
                    ->schema([
                        Forms\Components\DatePicker::make('acquisition_date'),
                        Forms\Components\TextInput::make('purchase_cost')
                            ->numeric()
                            ->prefix('currency'),
                        Forms\Components\Select::make('condition')
                            ->options(BookCopyCondition::class)
                            ->required(),
                    ])->columns(3),
                SectionComponent::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->default(true),
                        Forms\Components\Select::make('status')
                            ->options(BookCopyStatus::class)
                            ->default(BookCopyStatus::Available)
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
                TextColumn::make('barcode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inventory_number')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('book.title')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('condition')
                    ->badge()
                    ->color(fn (BookCopyCondition $state): string => $state->color())
                    ->toggleable(),
                IconColumn::make('is_available')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (BookCopyStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(BookCopyStatus::class),
                SelectFilter::make('book_id')
                    ->label('Book')
                    ->relationship('book', 'title'),
                SelectFilter::make('is_available')
                    ->label('Available')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
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
            'index' => Pages\ListBookCopies::route('/'),
            'create' => Pages\CreateBookCopy::route('/create'),
            'edit' => Pages\EditBookCopy::route('/{record}/edit'),
        ];
    }
}
