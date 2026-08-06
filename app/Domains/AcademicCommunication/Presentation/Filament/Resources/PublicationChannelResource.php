<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel;
use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Filament resource for the per-school publication channel registry (SPR-012).
 * Only channels enabled here are offered to the publication dispatcher.
 */
class PublicationChannelResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = PublicationChannel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Canaux de publication';

    protected static ?string $modelLabel = 'Canal de publication';

    protected static ?string $pluralModelLabel = 'Canaux de publication';

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Canal')
                    ->schema([
                        Forms\Components\Select::make('key')->label('Canal')
                            ->options(DocumentChannel::options())
                            ->required(),
                        Forms\Components\TextInput::make('label')->label('Libellé')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_enabled')->label('Activé')
                            ->default(true),
                        Forms\Components\KeyValue::make('config')->label('Configuration')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('key')
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Canal')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Libellé')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label('Activé')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublicationChannels::route('/'),
            'create' => Pages\CreatePublicationChannel::route('/create'),
            'edit' => Pages\EditPublicationChannel::route('/{record}/edit'),
        ];
    }
}
