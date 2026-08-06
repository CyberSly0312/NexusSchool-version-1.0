<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource\Pages;
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

class CouncilSessionResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = CouncilSession::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Conseil de classe';

    protected static ?string $navigationLabel = 'Séances du conseil';

    protected static ?string $modelLabel = 'Séance du conseil';

    protected static ?string $pluralModelLabel = 'Séances du conseil';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Contexte')
                    ->schema([
                        Forms\Components\Select::make('academic_calendar_year_id')->label('Année scolaire')
                            ->options(fn (): array => AcademicCalendarYear::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('academic_calendar_term_id')->label('Période')
                            ->options(fn (): array => AcademicCalendarTerm::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('school_class_id')->label('Classe')
                            ->options(fn (): array => SchoolClass::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('class_section_id')->label('Section')
                            ->options(fn (): array => ClassSection::query()
                                ->forSchool(tenant()?->id ?? '')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options(CouncilSessionStatus::class)
                            ->default(CouncilSessionStatus::Draft)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable(),
                Tables\Columns\TextColumn::make('school_class_id')
                    ->label('Classe')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_calendar_term_id')
                    ->label('Période')
                    ->sortable(),
                Tables\Columns\TextColumn::make('decisions_count')
                    ->label('Décisions')
                    ->counts('decisions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (CouncilSessionStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('held_at')
                    ->label('Réunion')
                    ->dateTime()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié')
                    ->dateTime()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(CouncilSessionStatus::class),
                Tables\Filters\SelectFilter::make('school_class_id')
                    ->label('Classe')
                    ->options(fn (): array => SchoolClass::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->pluck('name', 'id')
                        ->all()),
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
            'index' => Pages\ListCouncilSessions::route('/'),
            'create' => Pages\CreateCouncilSession::route('/create'),
            'edit' => Pages\EditCouncilSession::route('/{record}/edit'),
        ];
    }
}
