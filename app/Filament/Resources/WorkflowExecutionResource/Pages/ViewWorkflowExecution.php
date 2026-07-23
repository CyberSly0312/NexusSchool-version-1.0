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
                Section::make('Détails de l\'exécution')
                    ->schema([
                        TextEntry::make('workflow.name')
                            ->label('Flux de travail'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('current_node_id')
                            ->label('Nœud actuel'),
                        TextEntry::make('retry_count')
                            ->label('Tentatives'),
                        TextEntry::make('started_at')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('completed_at')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('duration_ms')
                            ->label('Durée (ms)'),
                    ])
                    ->columns(3),

                Section::make('Contexte')
                    ->schema([
                        TextEntry::make('context')
                            ->label('Contexte d\'exécution')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '-'),
                        TextEntry::make('variables')
                            ->label('Variables')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '-'),
                    ]),

                Section::make('Erreur')
                    ->schema([
                        TextEntry::make('error')
                            ->placeholder('Aucune erreur'),
                    ])
                    ->visible(fn ($record) => ! empty($record->error)),
            ]);
    }
}
