<?php

namespace Database\Seeders;

use App\Domains\Guardian\Enums\GuardianStatus;
use App\Domains\Guardian\Enums\PreferredContactMethod;
use App\Domains\Guardian\Enums\RelationshipType;
use App\Domains\School\Models\School;
use App\Models\Guardian;
use Illuminate\Database\Seeder;

class GuardianSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $guardians = [
            [
                'guardian_number' => 'GUA-001',
                'first_name' => 'Kwame',
                'last_name' => 'Asante',
                'gender' => 'male',
                'relationship_type' => RelationshipType::Father->value,
                'email' => 'kwame.asante@example.com',
                'phone_number' => '+233 24 100 0001',
                'occupation' => 'Business Executive',
                'employer' => 'Asante Group Ltd',
                'is_primary_guardian' => true,
                'preferred_contact_method' => PreferredContactMethod::Phone->value,
            ],
            [
                'guardian_number' => 'GUA-002',
                'first_name' => 'Akosua',
                'last_name' => 'Asante',
                'gender' => 'female',
                'relationship_type' => RelationshipType::Mother->value,
                'email' => 'akosua.asante@example.com',
                'phone_number' => '+233 24 100 0002',
                'occupation' => 'Medical Doctor',
                'employer' => 'Korle Bu Teaching Hospital',
                'is_primary_guardian' => false,
                'preferred_contact_method' => PreferredContactMethod::Email->value,
            ],
            [
                'guardian_number' => 'GUA-003',
                'first_name' => 'Yaw',
                'last_name' => 'Mensah',
                'gender' => 'male',
                'relationship_type' => RelationshipType::LegalGuardian->value,
                'email' => 'yaw.mensah@example.com',
                'phone_number' => '+233 24 100 0003',
                'occupation' => 'Teacher',
                'employer' => 'Accra High School',
                'is_primary_guardian' => true,
                'preferred_contact_method' => PreferredContactMethod::WhatsApp->value,
            ],
            [
                'guardian_number' => 'GUA-004',
                'first_name' => 'Esi',
                'last_name' => 'Ofori',
                'gender' => 'female',
                'relationship_type' => RelationshipType::Aunt->value,
                'email' => 'esi.ofori@example.com',
                'phone_number' => '+233 24 100 0004',
                'occupation' => 'Lawyer',
                'employer' => 'Ofori & Associates',
                'is_primary_guardian' => true,
                'preferred_contact_method' => PreferredContactMethod::SMS->value,
            ],
            [
                'guardian_number' => 'GUA-005',
                'first_name' => 'Nana',
                'last_name' => 'Boateng',
                'gender' => 'male',
                'relationship_type' => RelationshipType::Grandparent->value,
                'email' => null,
                'phone_number' => '+233 24 100 0005',
                'occupation' => 'Retired',
                'employer' => null,
                'is_primary_guardian' => false,
                'preferred_contact_method' => PreferredContactMethod::Phone->value,
            ],
        ];

        foreach ($guardians as $guardianData) {
            Guardian::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'guardian_number' => $guardianData['guardian_number'],
                ],
                array_merge($guardianData, [
                    'school_id' => $school->id,
                    'last_name' => $guardianData['last_name'],
                    'nationality' => 'Ghanaian',
                    'status' => GuardianStatus::Active->value,
                ]),
            );
        }
    }
}
