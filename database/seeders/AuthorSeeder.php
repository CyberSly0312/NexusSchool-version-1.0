<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $authors = [
            [
                'first_name' => 'Kwame',
                'last_name' => 'Asante',
                'middle_name' => null,
                'biography' => 'Award-winning Ghanaian author and professor of African Literature at the University of Ghana.',
                'nationality' => 'Ghana',
                'photo' => null,
                'website' => null,
                'status' => 'active',
            ],
            [
                'first_name' => 'Ama',
                'last_name' => 'Mensah',
                'middle_name' => 'Adwoa',
                'biography' => 'Novelist and poet exploring themes of identity and cultural heritage in West Africa.',
                'nationality' => 'Ghana',
                'photo' => null,
                'website' => 'https://amamensah.com',
                'status' => 'active',
            ],
            [
                'first_name' => 'Kofi',
                'last_name' => 'Darko',
                'middle_name' => null,
                'biography' => 'Science communicator and author of several popular science textbooks for schools.',
                'nationality' => 'Ghana',
                'photo' => null,
                'website' => null,
                'status' => 'active',
            ],
            [
                'first_name' => 'Efua',
                'last_name' => 'Osei',
                'middle_name' => 'Serwaa',
                'biography' => 'Historian and writer specializing in pre-colonial Ghanaian history and oral traditions.',
                'nationality' => 'Ghana',
                'photo' => null,
                'website' => null,
                'status' => 'active',
            ],
            [
                'first_name' => 'Yaw',
                'last_name' => 'Boateng',
                'middle_name' => null,
                'biography' => 'Mathematics educator and author of widely used secondary school mathematics guides.',
                'nationality' => 'Ghana',
                'photo' => null,
                'website' => 'https://yawboateng.edu',
                'status' => 'active',
            ],
        ];

        foreach ($authors as $author) {
            Author::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'first_name' => $author['first_name'],
                    'last_name' => $author['last_name'],
                ],
                $author
            );
        }
    }
}
