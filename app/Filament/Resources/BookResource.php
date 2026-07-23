<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Publisher;
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

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Bibliothèque';

    protected static ?string $navigationLabel = 'Livres';

    protected static ?string $modelLabel = 'Livre';

    protected static ?string $pluralModelLabel = 'Livres';

    protected static ?int $navigationSort = 33;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Informations sur le livre')
                    ->schema([
                        Forms\Components\TextInput::make('isbn')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subtitle')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(4),
                        Forms\Components\TextInput::make('language')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('edition')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('publication_year')
                            ->numeric()
                            ->maxLength(4),
                    ])
                    ->columns(2),

                SectionComponent::make('Classification')
                    ->schema([
                        Forms\Components\Select::make('publisher_id')
                            ->label('Éditeur')
                            ->options(fn () => Publisher::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('book_category_id')
                            ->label('Catégorie')
                            ->options(fn () => BookCategory::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('shelf_location')
                            ->maxLength(255),
                        Forms\Components\Select::make('author_ids')
                            ->label('Auteurs')
                            ->relationship('authors', 'id')
                            ->multiple()
                            ->options(fn () => Author::all()->map(fn (Author $author) => [
                                'value' => $author->id,
                                'label' => trim($author->first_name.' '.$author->last_name),
                            ])->pluck('label', 'value'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Actif',
                                'inactive' => 'Inactif',
                                'archived' => 'Archivé',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                SectionComponent::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->directory('book-covers'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('isbn')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('authors.first_name')
                    ->label('Auteurs')
                    ->getStateUsing(fn (Book $record): string => $record->authors->pluck('first_name')->implode(', '))
                    ->toggleable(),
                TextColumn::make('publisher.name')
                    ->label('Éditeur')
                    ->toggleable(),
                TextColumn::make('bookCategory.name')
                    ->label('Catégorie')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'archived' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'archived' => 'Archivé',
                    ]),
                SelectFilter::make('book_category_id')
                    ->label('Catégorie')
                    ->options(fn () => BookCategory::pluck('name', 'id')),
                SelectFilter::make('publisher_id')
                    ->label('Éditeur')
                    ->options(fn () => Publisher::pluck('name', 'id')),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
