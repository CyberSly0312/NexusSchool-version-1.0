<?php

declare(strict_types=1);

namespace App\Filament\Resources\WorkflowExecutionResource\Pages;

use App\Filament\Resources\WorkflowExecutionResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;

class ViewWorkflowExecution extends ViewRecord
{
    protected static string $resource = WorkflowExecutionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Execution Details')
                    ->schema([
                        TextEntry::make('workflow.name')
                            ->label('Workflow'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('current_node_id')
                            ->label('Current Node'),
                        TextEntry::make('retry_count')
                            ->label('Retries'),
                        TextEntry::make('started_at')
                            ->dateTime(),
                        TextEntry::make('completed_at')
                            ->dateTime(),
                        TextEntry::make('duration_ms')
                            ->label('Duration (ms)'),
                    ])
                    ->columns(3),

                Section::make('Context')
                    ->schema([
                        TextEntry::make('context')
                            ->label('Execution Context')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '-'),
                        TextEntry::make('variables')
                            ->label('Variables')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '-'),
                    ]),

                Section::make('Error')
                    ->schema([
                        TextEntry::make('error')
                            ->placeholder('No errors'),
                    ])
                    ->visible(fn ($record) => ! empty($record->error)),
            ]);
    }
}
