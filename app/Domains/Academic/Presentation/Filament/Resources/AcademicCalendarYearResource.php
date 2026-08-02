<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources;

use App\Domains\Academic\Application\Services\CalendarService;
use App\Domains\Academic\Domain\Enums\CalendarYearStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AcademicCalendarYearResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AcademicCalendarYear::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Années scolaires';

    protected static ?string $modelLabel = 'Année scolaire';

    protected static ?string $pluralModelLabel = 'Années scolaires';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make("Détails de l'année scolaire")
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(50)
                            ->helperText('Code court optionnel'),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->afterOrEqual('start_date'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(CalendarYearStatus::class)
                            ->default(CalendarYearStatus::Draft)
                            ->required(),
                    ]),
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
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (CalendarYearStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(CalendarYearStatus::class),
            ])
            ->actions([
                EditAction::make(),
                Action::make('activate')
                    ->label('Activer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AcademicCalendarYear $record): bool => ! $record->isActive())
                    ->action(function (AcademicCalendarYear $record): void {
                        app(CalendarService::class)->activateYear($record);
                    }),
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
            'index' => Pages\ListAcademicCalendarYears::route('/'),
            'create' => Pages\CreateAcademicCalendarYear::route('/create'),
            'edit' => Pages\EditAcademicCalendarYear::route('/{record}/edit'),
        ];
    }
}
