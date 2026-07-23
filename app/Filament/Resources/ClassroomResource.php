<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Classroom\Enums\ClassroomStatus;
use App\Domains\Classroom\Enums\RoomType;
use App\Filament\Resources\ClassroomResource\Pages;
use App\Models\Classroom;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Installations';

    protected static ?string $navigationLabel = 'Salles';

    protected static ?string $modelLabel = 'Salle';

    protected static ?string $pluralModelLabel = 'Salles';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la salle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Code unique dans l\'établissement'),
                        Forms\Components\TextInput::make('building')
                            ->maxLength(100)
                            ->helperText('Nom du bâtiment'),
                        Forms\Components\TextInput::make('floor')
                            ->maxLength(50)
                            ->helperText('Numéro ou nom de l\'étage'),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Nombre maximum d\'élèves'),
                        Forms\Components\Select::make('room_type')
                            ->options(RoomType::class)
                            ->required()
                            ->default(RoomType::StandardClassroom),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(2),
                SectionComponent::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(ClassroomStatus::class)
                            ->default(ClassroomStatus::Active)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('building')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('floor')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('room_type')
                    ->badge()
                    ->formatStateUsing(fn (RoomType $state): string => $state->label()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ClassroomStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ClassroomStatus::class),
                Tables\Filters\SelectFilter::make('room_type')
                    ->label('Type de salle')
                    ->options(RoomType::class),
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
            'index' => Pages\ListClassrooms::route('/'),
            'create' => Pages\CreateClassroom::route('/create'),
            'edit' => Pages\EditClassroom::route('/{record}/edit'),
        ];
    }
}
