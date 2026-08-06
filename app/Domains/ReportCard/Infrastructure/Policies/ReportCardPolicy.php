<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Policies;

use App\Domains\ReportCard\Domain\Traits\HandlesReportCardAuthorization;

final class ReportCardPolicy
{
    use HandlesReportCardAuthorization;

    protected function permissionGroup(): string
    {
        return 'report-card';
    }
}
