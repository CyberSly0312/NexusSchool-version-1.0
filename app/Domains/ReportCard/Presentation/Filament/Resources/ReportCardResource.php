<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Presentation\Filament\Resources;

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarTerm;
use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Infrastructure\Models\ReportCard;
use App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource\Pages;
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

class ReportCardResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = ReportCard::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Bulletins';

    protected static ?string $navigationLabel = 'Bulletins';

    protected static ?string $modelLabel = 'Bulletin';

    protected static ?string $pluralModelLabel = 'Bulletins';

    protected static ?string $recordTitleAttribute = 'id';

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
                        Forms\Components\Select::make('report_card_type')
                            ->options(\App\Domains\ReportCard\Domain\Enums\ReportCardType::class)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(ReportCardStatus::class)
                            ->default(ReportCardStatus::Draft)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('school_class_id')
                    ->label('Classe')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_calendar_term_id')
                    ->label('Période')
                    ->sortable(),
                Tables\Columns\TextColumn::make('entries_count')
                    ->label('Élèves')
                    ->counts('entries')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ReportCardStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Généré')
                    ->dateTime()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié')
                    ->dateTime()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ReportCardStatus::class),
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
            'index' => Pages\ListReportCards::route('/'),
            'create' => Pages\CreateReportCard::route('/create'),
            'edit' => Pages\EditReportCard::route('/{record}/edit'),
        ];
    }
}
