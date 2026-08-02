<?php

declare(strict_types=1);

namespace App\Domains\Student\Presentation\Filament\Resources\StudentResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GuardiansRelationManager extends RelationManager
{
    protected static string $relationship = 'guardians';

    protected static ?string $title = 'Tuteurs / Parents';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom complet')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->maxLength(30),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Select::make('relationship')
                    ->label('Lien')
                    ->options([
                        'parent' => 'Parent',
                        'guardian' => 'Tuteur',
                        'sibling' => 'Frère / Sœur',
                        'other' => 'Autre',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_primary')
                    ->label('Principal'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom complet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('relationship')
                    ->label('Lien')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Principal')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Ajouter un tuteur'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
