<?php

declare(strict_types=1);

use App\Domains\Staff\Domain\Enums\PositionCategory;

return [

    /*
    |--------------------------------------------------------------------------
    | Staff number generator
    |--------------------------------------------------------------------------
    */

    'staff_number_prefix' => 'STF',

    /*
    |--------------------------------------------------------------------------
    | Configurable positions catalog
    |--------------------------------------------------------------------------
    |
    | The canonical catalog of workforce positions supported by the Staff
    | domain. Rows are materialized per tenant in the `positions` table (see
    | PositionSeeder). A school may later add custom positions through the
    | same table; the domain never hard-codes the catalog.
    |
    */

    'positions' => [
        ['code' => 'teacher', 'title' => 'Enseignant', 'category' => PositionCategory::Teaching, 'sort_order' => 1],
        ['code' => 'director', 'title' => 'Directeur', 'category' => PositionCategory::NonTeaching, 'sort_order' => 2],
        ['code' => 'deputy_director', 'title' => 'Directeur adjoint', 'category' => PositionCategory::NonTeaching, 'sort_order' => 3],
        ['code' => 'secretary', 'title' => 'Secrétaire', 'category' => PositionCategory::NonTeaching, 'sort_order' => 4],
        ['code' => 'accountant', 'title' => 'Comptable', 'category' => PositionCategory::NonTeaching, 'sort_order' => 5],
        ['code' => 'librarian', 'title' => 'Bibliothécaire', 'category' => PositionCategory::NonTeaching, 'sort_order' => 6],
        ['code' => 'registrar', 'title' => 'Responsable scolarité', 'category' => PositionCategory::NonTeaching, 'sort_order' => 7],
        ['code' => 'administrator', 'title' => 'Administrateur', 'category' => PositionCategory::NonTeaching, 'sort_order' => 8],
        ['code' => 'maintenance', 'title' => 'Agent d\'entretien', 'category' => PositionCategory::NonTeaching, 'sort_order' => 9],
        ['code' => 'cafeteria', 'title' => 'Personnel de cantine', 'category' => PositionCategory::NonTeaching, 'sort_order' => 10],
        ['code' => 'driver', 'title' => 'Chauffeur', 'category' => PositionCategory::NonTeaching, 'sort_order' => 11],
        ['code' => 'nurse', 'title' => 'Infirmier·ère scolaire', 'category' => PositionCategory::NonTeaching, 'sort_order' => 12],
        ['code' => 'counselor', 'title' => 'Conseiller·ère', 'category' => PositionCategory::NonTeaching, 'sort_order' => 13],
        ['code' => 'other', 'title' => 'Autre', 'category' => PositionCategory::NonTeaching, 'sort_order' => 99],
    ],
];
