<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ApiClient;
use App\Domains\School\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApiClientSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        ApiClient::firstOrCreate(
            ['slug' => 'demo-integration'],
            [
                'school_id' => $school->id,
                'name' => 'Demo Integration Client',
                'description' => 'Pre-configured API client for demo purposes',
                'secret' => env('DEMO_API_SECRET', Str::random(64)),
                'callback_url' => config('app.url').'/api/webhooks/demo',
                'is_active' => true,
                'scopes' => ['*'],
                'rate_limit_per_minute' => 120,
                'rate_limit_per_day' => 50000,
            ]
        );
    }
}
