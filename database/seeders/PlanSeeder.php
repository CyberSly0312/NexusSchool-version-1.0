<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Gratuit',
                'description' => 'Plan d\'essai gratuit pour découvrir la plateforme',
                'price' => 0,
                'currency' => 'XOF',
                'billing_period' => 'monthly',
                'max_schools' => 1,
                'max_students' => 30,
                'max_teachers' => 5,
                'max_users' => 10,
                'storage_limit' => '500MB',
                'modules' => ['notes', 'presences', 'emploi_du_temps'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Essentiel',
                'description' => 'Pour les petites écoles débutant leur numérisation',
                'price' => 25000,
                'currency' => 'XOF',
                'billing_period' => 'monthly',
                'max_schools' => 1,
                'max_students' => 200,
                'max_teachers' => 20,
                'max_users' => 50,
                'storage_limit' => '2GB',
                'modules' => ['notes', 'presences', 'emploi_du_temps', 'bulletins', 'paiements', 'messagerie'],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professionnel',
                'description' => 'Pour les écoles avec des besoins avancés',
                'price' => 75000,
                'currency' => 'XOF',
                'billing_period' => 'monthly',
                'max_schools' => 1,
                'max_students' => 1000,
                'max_teachers' => 50,
                'max_users' => 100,
                'storage_limit' => '10GB',
                'modules' => ['notes', 'presences', 'emploi_du_temps', 'bulletins', 'paiements', 'messagerie', 'transport', 'bibliotheque', 'examens', 'statistiques'],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Institution',
                'description' => 'Pour les groupes scolaires et universités',
                'price' => 150000,
                'currency' => 'XOF',
                'billing_period' => 'monthly',
                'max_schools' => 5,
                'max_students' => 5000,
                'max_teachers' => 200,
                'max_users' => 500,
                'storage_limit' => '50GB',
                'modules' => ['notes', 'presences', 'emploi_du_temps', 'bulletins', 'paiements', 'messagerie', 'transport', 'bibliotheque', 'examens', 'statistiques', 'hr', 'comptabilite', 'api'],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                $data
            );
        }
    }
}
