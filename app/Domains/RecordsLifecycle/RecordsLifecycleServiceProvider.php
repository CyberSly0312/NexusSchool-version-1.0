<?php

declare(strict_types=1);

namespace App\Domains\RecordsLifecycle;

use App\Domains\RecordsLifecycle\Application\Actions\ArchiveRecordLifecycleAction;
use App\Domains\RecordsLifecycle\Application\Actions\PublishRecordLifecycleAction;
use App\Domains\RecordsLifecycle\Application\Actions\RegisterRecordLifecycleAction;
use App\Domains\RecordsLifecycle\Application\Services\RecordLifecycleService;
use App\Domains\RecordsLifecycle\Infrastructure\Models\RecordLifecycle;
use App\Domains\RecordsLifecycle\Infrastructure\Policies\RecordLifecyclePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class RecordsLifecycleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RecordLifecycleService::class);
        $this->app->bind(RegisterRecordLifecycleAction::class);
        $this->app->bind(PublishRecordLifecycleAction::class);
        $this->app->bind(ArchiveRecordLifecycleAction::class);
    }

    public function boot(): void
    {
        Gate::policy(RecordLifecycle::class, RecordLifecyclePolicy::class);
    }
}
