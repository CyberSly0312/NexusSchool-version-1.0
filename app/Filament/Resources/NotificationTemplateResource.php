<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Communication\Enums\NotificationChannel;
use App\Domains\Communication\Enums\TemplateCategory;
use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
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

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Modèles de notification';

    protected static ?string $modelLabel = 'Modèle de notification';

    protected static ?string $pluralModelLabel = 'Modèles de notification';

    protected static ?int $navigationSort = 61;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails du modèle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options(TemplateCategory::labels())
                            ->required(),
                        Forms\Components\Select::make('channel')
                            ->options(NotificationChannel::labels())
                            ->required(),
                    ])
                    ->columns(2),

                SectionComponent::make('Contenu')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(6),
                    ]),

                SectionComponent::make('Variables et statut')
                    ->schema([
                        Forms\Components\TagsInput::make('variables')
                            ->label('Variables disponibles'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (TemplateCategory $state): string => TemplateCategory::colors()[$state] ?? 'gray')
                    ->icon(fn (TemplateCategory $state): string => TemplateCategory::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('channel')
                    ->badge()
                    ->color(fn (NotificationChannel $state): string => NotificationChannel::colors()[$state] ?? 'gray')
                    ->icon(fn (NotificationChannel $state): string => NotificationChannel::icons()[$state] ?? 'heroicon-o-question-mark'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(TemplateCategory::labels()),
                SelectFilter::make('channel')
                    ->options(NotificationChannel::labels()),
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
            'index' => Pages\ListNotificationTemplates::route('/'),
            'create' => Pages\CreateNotificationTemplate::route('/create'),
            'edit' => Pages\EditNotificationTemplate::route('/{record}/edit'),
        ];
    }
}
