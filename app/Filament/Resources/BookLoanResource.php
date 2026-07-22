<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Library\Enums\BookLoanStatus;
use App\Domains\Library\Enums\BorrowerType;
use App\Filament\Resources\BookLoanResource\Pages;
use App\Models\BookLoan;
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
use Illuminate\Database\Eloquent\Builder;

class BookLoanResource extends Resource
{
    protected static ?string $model = BookLoan::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Bibliothèque';

    protected static ?string $navigationLabel = 'Emprunts';

    protected static ?string $modelLabel = 'Emprunt';

    protected static ?string $pluralModelLabel = 'Emprunts';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Loan Details')
                    ->schema([
                        Forms\Components\Select::make('book_copy_id')
                            ->label('Book Copy')
                            ->relationship('bookCopy', 'barcode')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->barcode.' - '.$record->book->title)
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
                        Forms\Components\DatePicker::make('loan_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('due_date')
                            ->required(),
                        Forms\Components\DatePicker::make('return_date')
                            ->nullable(),
                    ])->columns(3),
                SectionComponent::make('Status & Staff')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(BookLoanStatus::class)
                            ->default(BookLoanStatus::Active)
                            ->required(),
                        Forms\Components\TextInput::make('issued_by')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('returned_to')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('renewal_count')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bookCopy.barcode')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('bookCopy.book.title')
                    ->label('Book Title')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('borrower_id')
                    ->sortable(),
                TextColumn::make('borrower_type')
                    ->badge()
                    ->color(fn (BorrowerType $state): string => $state === BorrowerType::Student ? 'info' : 'success'),
                TextColumn::make('loan_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->color(fn (BookLoan $record): string => $record->status === BookLoanStatus::Active && $record->due_date->isPast() ? 'danger' : null),
                TextColumn::make('return_date')
                    ->date()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (BookLoanStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(BookLoanStatus::class),
                SelectFilter::make('borrower_type')
                    ->options(BorrowerType::class),
                SelectFilter::make('is_overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query): Builder => $query->where('status', BookLoanStatus::Active)->where('due_date', '<', now()))
                    ->options([
                        1 => 'Yes',
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
            'index' => Pages\ListBookLoans::route('/'),
            'create' => Pages\CreateBookLoan::route('/create'),
            'edit' => Pages\EditBookLoan::route('/{record}/edit'),
        ];
    }
}
