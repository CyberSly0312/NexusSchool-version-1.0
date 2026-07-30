<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Analytics\Enums\ReportFormat;
use App\Domains\Analytics\Enums\ReportStatus;
use App\Domains\Analytics\Enums\ScheduleFrequency;
use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
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
use App\Traits\ScopedByTenant;

class ReportResource extends Resource
{
    use ScopedByTenant;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Report::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Analytiques';

    protected static ?string $navigationLabel = 'Rapports';

    protected static ?string $modelLabel = 'Rapport';

    protected static ?string $pluralModelLabel = 'Rapports';

    protected static ?int $navigationSort = 71;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            SectionComponent::make('Détails du rapport')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(3),
                Forms\Components\TextInput::make('type')->required()->maxLength(100)->default('custom'),
            ]),
            SectionComponent::make('Sortie')->schema([
                Forms\Components\Select::make('format')
                    ->options(ReportFormat::labels())->required()->default('pdf'),
                Forms\Components\Select::make('status')
                    ->options(ReportStatus::labels())->required()->default('draft'),
            ])->columns(2),
            SectionComponent::make('Planification')->schema([
                Forms\Components\Select::make('scheduled_frequency')
                    ->options(ScheduleFrequency::labels())->nullable(),
                Forms\Components\DateTimePicker::make('scheduled_at'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('type')->searchable(),
                TextColumn::make('format')->badge()
                    ->color(fn (ReportFormat $state): string => $state->color()),
                TextColumn::make('status')->badge()
                    ->color(fn (ReportStatus $state): string => $state->color()),
                TextColumn::make('scheduled_frequency')->label('Fréquence')->toggleable(),
                TextColumn::make('last_generated_at')->dateTime('d/m/Y H:i')->sortable()->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable()->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ReportStatus::labels()),
                SelectFilter::make('format')->options(ReportFormat::labels()),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
