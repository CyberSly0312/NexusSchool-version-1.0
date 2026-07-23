<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ApiClient;
use App\Domains\School\Models\School;
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

        $client = ApiClient::where('school_id', $school->id)->first();
        if (! $client) {
            return;
        }

        Webhook::firstOrCreate(
            [
                'api_client_id' => $client->id,
                'school_id' => $school->id,
                'url' => config('app.url').'/api/webhooks/demo/receive',
            ],
            [
                'api_client_id' => $client->id,
                'school_id' => $school->id,
                'url' => config('app.url').'/api/webhooks/demo/receive',
                'secret' => env('DEMO_WEBHOOK_SECRET', Str::random(64)),
                'events' => ['student.created', 'enrollment.created'],
                'is_active' => true,
                'description' => 'Webhook de démonstration pour les événements d\'élèvements et d\'inscriptions',
                'failure_count' => 0,
            ]
        );
    }
}
