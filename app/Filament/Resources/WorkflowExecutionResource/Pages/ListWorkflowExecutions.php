<?php

declare(strict_types=1);

namespace App\Filament\Resources\WorkflowExecutionResource\Pages;

use App\Filament\Resources\WorkflowExecutionResource;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowExecutions extends ListRecords
{
    protected static string $resource = WorkflowExecutionResource::class;
}
