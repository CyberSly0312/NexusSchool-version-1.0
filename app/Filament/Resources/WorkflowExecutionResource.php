<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Workflow\Enums\ExecutionStatus;
use App\Filament\Resources\WorkflowExecutionResource\Pages;
use App\Models\WorkflowExecution;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkflowExecutionResource extends Resource
{
    protected static ?string $model = WorkflowExecution::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static string|\UnitEnum|null $navigationGroup = 'Moteur de workflow';

    protected static ?int $navigationSort = 81;

    protected static ?string $navigationLabel = 'Exécutions';

    protected static ?string $modelLabel = 'Exécution';

    protected static ?string $pluralModelLabel = 'Exécutions';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workflow.name')
                    ->label('Workflow')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => ExecutionStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('retry_count')
                    ->label('Retries')
                    ->sortable(),
                TextColumn::make('current_node_id')
                    ->label('Current Node')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('initiated_by')
                    ->label('Initiated By')
                    ->toggleable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('duration_ms')
                    ->label('Duration (ms)')
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
                SelectFilter::make('status')
                    ->options(ExecutionStatus::labels()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflowExecutions::route('/'),
            'view' => Pages\ViewWorkflowExecution::route('/{record}'),
        ];
    }
}
