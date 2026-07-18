<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Ai\Enums\AiPromptTemplateStatus;
use App\Domains\Ai\Enums\PromptCategory;
use App\Filament\Resources\AiPromptTemplateResource\Pages;
use App\Models\AiPromptTemplate;
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

class AiPromptTemplateResource extends Resource
{
    protected static ?string $model = AiPromptTemplate::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'AI Platform';

    protected static ?int $navigationSort = 74;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Template Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options(PromptCategory::labels())
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])
                    ->columns(1),

                SectionComponent::make('Prompts')
                    ->schema([
                        Forms\Components\Textarea::make('system_prompt')
                            ->required()
                            ->rows(4)
                            ->label('System Prompt'),
                        Forms\Components\Textarea::make('user_prompt_template')
                            ->required()
                            ->rows(4)
                            ->label('User Prompt Template'),
                        Forms\Components\KeyValue::make('variables')
                            ->label('Template Variables'),
                    ])
                    ->columns(1),

                SectionComponent::make('Model Configuration')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(AiPromptTemplateStatus::labels())
                            ->default('active'),
                        Forms\Components\TextInput::make('target_model')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('temperature')
                            ->numeric()
                            ->default(0.7)
                            ->minValue(0)
                            ->maxValue(2),
                        Forms\Components\TextInput::make('max_tokens')
                            ->numeric()
                            ->default(4096)
                            ->minValue(1),
                        Forms\Components\TextInput::make('top_p')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->maxValue(1),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => PromptCategory::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (AiPromptTemplateStatus $state): string => $state->color()),
                TextColumn::make('version')
                    ->sortable(),
                TextColumn::make('target_model')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('temperature')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(PromptCategory::labels()),
                SelectFilter::make('status')
                    ->options(AiPromptTemplateStatus::labels()),
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
            'index' => Pages\ListAiPromptTemplates::route('/'),
            'create' => Pages\CreateAiPromptTemplate::route('/create'),
            'edit' => Pages\EditAiPromptTemplate::route('/{record}/edit'),
        ];
    }
}
