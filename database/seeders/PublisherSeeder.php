<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $publishers = [
            [
                'name' => 'Ghana Publishing Corporation',
                'address' => 'P.O. Box GP 1864',
                'city' => 'Accra',
                'state' => 'Greater Accra',
                'country' => 'Ghana',
                'postal_code' => '00233',
                'phone' => '+233 30 222 1234',
                'email' => 'info@ghanapublishing.gov.gh',
                'website' => 'https://ghanapublishing.gov.gh',
                'notes' => 'Government-owned publishing house established in 1963.',
                'status' => 'active',
            ],
            [
                'name' => 'African Minds',
                'address' => '14 Loop Street',
                'city' => 'Cape Town',
                'state' => 'Western Cape',
                'country' => 'South Africa',
                'postal_code' => '8001',
                'phone' => '+27 21 424 5678',
                'email' => 'info@africanminds.co.za',
                'website' => 'https://africanminds.co.za',
                'notes' => 'Independent pan-African publisher focusing on scholarly works.',
                'status' => 'active',
            ],
            [
                'name' => 'Sublime Books',
                'address' => '15 Cantonments Road',
                'city' => 'Accra',
                'state' => 'Greater Accra',
                'country' => 'Ghana',
                'postal_code' => '00233',
                'phone' => '+233 30 277 9012',
                'email' => 'hello@sublimebooks.com',
                'website' => 'https://sublimebooks.com',
                'notes' => 'Boutique publisher of children and young adult literature.',
                'status' => 'active',
            ],
        ];

        foreach ($publishers as $publisher) {
            Publisher::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $publisher['name'],
                ],
                $publisher
            );
        }
    }
}
