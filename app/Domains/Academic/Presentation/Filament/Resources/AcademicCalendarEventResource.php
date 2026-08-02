<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources;

use App\Domains\Academic\Domain\Enums\CalendarEventType;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarEvent;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarEventResource\Pages;
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

class AcademicCalendarEventResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AcademicCalendarEvent::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Événements';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Événements';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make("Détails de l'événement")
                    ->schema([
                        Forms\Components\Select::make('calendar_year_id')
                            ->label('Année scolaire')
                            ->options(fn (): array => AcademicCalendarYear::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all())
                            ->required(),
                        Forms\Components\Select::make('calendar_term_id')
                            ->label('Période')
                            ->options(fn (): array => AcademicCalendarTerm::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all()),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('type')
                            ->options(CalendarEventType::class)
                            ->default(CalendarEventType::Event)
                            ->required(),
                        Forms\Components\DatePicker::make('starts_on')
                            ->required(),
                        Forms\Components\DatePicker::make('ends_on')
                            ->required()
                            ->afterOrEqual('starts_on'),
                        Forms\Components\Toggle::make('is_all_day')
                            ->label('Journée entière')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (CalendarEventType $state): string => $state->color()),
                Tables\Columns\TextColumn::make('calendarYear.name')
                    ->label('Année scolaire'),
                Tables\Columns\TextColumn::make('starts_on')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_all_day')
                    ->boolean()
                    ->label('Journée entière'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(CalendarEventType::class),
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
            'index' => Pages\ListAcademicCalendarEvents::route('/'),
            'create' => Pages\CreateAcademicCalendarEvent::route('/create'),
            'edit' => Pages\EditAcademicCalendarEvent::route('/{record}/edit'),
        ];
    }
}
