<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ApiClient;
use App\Models\School;
use App\Models\Webhook;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WebhookSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $client = ApiClient::where('slug', 'demo-integration')->first();
        if (! $client) {
            return;
        }

        Webhook::firstOrCreate(
            ['slug' => 'demo-webhook'],
            [
                'api_client_id' => $client->id,
                'school_id' => $school->id,
                'url' => config('app.url').'/api/webhooks/demo/receive',
                'secret' => env('DEMO_WEBHOOK_SECRET', Str::random(64)),
                'events' => ['student.created', 'enrollment.created'],
                'is_active' => true,
                'description' => 'Demo webhook for student and enrollment events',
                'failure_count' => 0,
            ]
        );
    }
}
