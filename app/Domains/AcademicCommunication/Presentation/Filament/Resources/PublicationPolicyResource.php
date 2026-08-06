<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources;

use App\Domains\AcademicCommunication\Domain\Enums\DocumentType;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource\Pages;
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
 * Filament resource for publication policies (SPR-012). Directors configure —
 * per document type — who receives the document, through which channels,
 * whether approval is required and the visibility map applied at generation.
 */
class PublicationPolicyResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = PublicationPolicy::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Politiques de publication';

    protected static ?string $modelLabel = 'Politique de publication';

    protected static ?string $pluralModelLabel = 'Politiques de publication';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Politique')
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
                        Forms\Components\CheckboxList::make('recipient_scopes')->label('Destinataires')
                            ->options(VisibilityScope::options())
                            ->columns(2),
                        Forms\Components\CheckboxList::make('channels')->label('Canaux')
                            ->options(\App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel::options())
                            ->columns(2),
                        Forms\Components\Toggle::make('requires_approval')->label('Approbation requise')
                            ->default(false),
                        Forms\Components\TagsInput::make('approval_roles')->label('Rôles approbateurs'),
                        Forms\Components\Toggle::make('is_active')->label('Actif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')->label('Par défaut')
                            ->default(false),
                        Forms\Components\DatePicker::make('effective_from')->label('Effectif à partir de'),
                        Forms\Components\DatePicker::make('effective_until')->label('Effectif jusqu\'à'),
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
                Tables\Columns\IconColumn::make('requires_approval')
                    ->label('Approbation')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
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
            'index' => Pages\ListPublicationPolicies::route('/'),
            'create' => Pages\CreatePublicationPolicy::route('/create'),
            'edit' => Pages\EditPublicationPolicy::route('/{record}/edit'),
        ];
    }
}
