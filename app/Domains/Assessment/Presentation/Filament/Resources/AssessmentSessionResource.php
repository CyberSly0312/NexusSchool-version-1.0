<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources;

use App\Domains\Assessment\Domain\Enums\AssessmentSessionStatus;
use App\Domains\Assessment\Infrastructure\Models\AssessmentSession;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource\Pages;
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

class AssessmentSessionResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AssessmentSession::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Évaluations';

    protected static ?string $navigationLabel = 'Sessions';

    protected static ?string $modelLabel = 'Session';

    protected static ?string $pluralModelLabel = 'Sessions';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Session')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Titre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date')->label('Date'),
                        Forms\Components\TimePicker::make('starts_at')->label('Début'),
                        Forms\Components\TimePicker::make('ends_at')->label('Fin'),
                        Forms\Components\Select::make('status')
                            ->options(AssessmentSessionStatus::class)
                            ->default(AssessmentSessionStatus::Scheduled)
                            ->required(),
                        Forms\Components\Textarea::make('notes')->label('Notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début'),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AssessmentSessionStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AssessmentSessionStatus::class),
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
            'index' => Pages\ListAssessmentSessions::route('/'),
            'create' => Pages\CreateAssessmentSession::route('/create'),
            'edit' => Pages\EditAssessmentSession::route('/{record}/edit'),
        ];
    }
}
