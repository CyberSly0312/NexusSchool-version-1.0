<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources;

use App\Domains\Academic\Domain\Enums\CalendarTermStatus;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarTermResource\Pages;
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

class AcademicCalendarTermResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = AcademicCalendarTerm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Académique';

    protected static ?string $navigationLabel = 'Périodes';

    protected static ?string $modelLabel = 'Période';

    protected static ?string $pluralModelLabel = 'Périodes';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Détails de la période')
                    ->schema([
                        Forms\Components\Select::make('calendar_year_id')
                            ->label('Année scolaire')
                            ->options(fn (): array => AcademicCalendarYear::query()->forSchool(tenant()?->id ?? '')->pluck('name', 'id')->all())
                            ->helperText('Périodes rattachées à une année scolaire')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(50),
                        Forms\Components\DatePicker::make('starts_on')
                            ->required(),
                        Forms\Components\DatePicker::make('ends_on')
                            ->required()
                            ->afterOrEqual('starts_on'),
                    ])->columns(2),
                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(CalendarTermStatus::class)
                            ->default(CalendarTermStatus::Planned)
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
                Tables\Columns\TextColumn::make('calendarYear.name')
                    ->label('Année scolaire')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starts_on')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_on')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (CalendarTermStatus $state): string => $state->color()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(CalendarTermStatus::class),
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
            'index' => Pages\ListAcademicCalendarTerms::route('/'),
            'create' => Pages\CreateAcademicCalendarTerm::route('/create'),
            'edit' => Pages\EditAcademicCalendarTerm::route('/{record}/edit'),
        ];
    }
}
