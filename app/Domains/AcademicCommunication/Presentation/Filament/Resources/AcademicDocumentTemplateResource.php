<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate;
use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource\Pages;
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
 * Filament resource for document templates (SPR-012). Templates are school
 * scoped and managed with the `document.template.manage` capability.
 */
class AcademicDocumentTemplateResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AcademicDocumentTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Modèles de documents';

    protected static ?string $modelLabel = 'Modèle de document';

    protected static ?string $pluralModelLabel = 'Modèles de documents';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Modèle')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')->label('Code')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('document_type')->label('Type de document')
                            ->options(DocumentType::options())
                            ->required(),
                        Forms\Components\TextInput::make('locale')->label('Locale')
                            ->default('fr')
                            ->maxLength(10),
                        Forms\Components\Toggle::make('is_active')->label('Actif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')->label('Par défaut')
                            ->default(false),
                        Forms\Components\Toggle::make('qr_code_enabled')->label('QR code')
                            ->default(false),
                        Forms\Components\Toggle::make('verification_code_enabled')->label('Code de vérification')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Par défaut')
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
            'index' => Pages\ListAcademicDocumentTemplates::route('/'),
            'create' => Pages\CreateAcademicDocumentTemplate::route('/create'),
            'edit' => Pages\EditAcademicDocumentTemplate::route('/{record}/edit'),
        ];
    }
}
