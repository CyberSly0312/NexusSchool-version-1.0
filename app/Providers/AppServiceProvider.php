<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::creating(function (Model $model) {
            if (
                in_array('uuid', $model->getFillable(), true)
                && $model->uuid === null
            ) {
                $model->uuid = Str::ulid()->toString();
            }
        });
    }
}
