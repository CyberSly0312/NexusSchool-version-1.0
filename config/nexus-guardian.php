<?php

declare(strict_types=1);

use App\Domains\Guardian\Domain\Enums\RelationshipCategory;

return [

    /*
    |--------------------------------------------------------------------------
    | Guardian number generator
    |--------------------------------------------------------------------------
    */

    'guardian_number_prefix' => 'GUA',

    /*
    |--------------------------------------------------------------------------
    | Configurable relationship types
    |--------------------------------------------------------------------------
    |
    | The canonical catalog of relationship types supported by the Guardian
    | domain. Rows are materialized per tenant in the `relationship_types`
    | table (see RelationshipTypeSeeder). A school may later add custom codes
    | through the same table; the domain never hard-codes the catalog.
    |
    */

    'relationship_types' => [
        ['code' => 'father', 'label' => 'Father', 'description' => 'Biological or adoptive father.', 'category' => RelationshipCategory::Family, 'sort_order' => 1],
        ['code' => 'mother', 'label' => 'Mother', 'description' => 'Biological or adoptive mother.', 'category' => RelationshipCategory::Family, 'sort_order' => 2],
        ['code' => 'guardian', 'label' => 'Guardian', 'description' => 'General legal guardian responsible for the student.', 'category' => RelationshipCategory::Legal, 'sort_order' => 3],
        ['code' => 'grandparent', 'label' => 'Grandparent', 'description' => 'Grandfather or grandmother.', 'category' => RelationshipCategory::Family, 'sort_order' => 4],
        ['code' => 'uncle', 'label' => 'Uncle', 'description' => 'Paternal or maternal uncle.', 'category' => RelationshipCategory::Family, 'sort_order' => 5],
        ['code' => 'aunt', 'label' => 'Aunt', 'description' => 'Paternal or maternal aunt.', 'category' => RelationshipCategory::Family, 'sort_order' => 6],
        ['code' => 'brother', 'label' => 'Brother', 'description' => 'Older or younger brother.', 'category' => RelationshipCategory::Family, 'sort_order' => 7],
        ['code' => 'sister', 'label' => 'Sister', 'description' => 'Older or younger sister.', 'category' => RelationshipCategory::Family, 'sort_order' => 8],
        ['code' => 'sponsor', 'label' => 'Sponsor', 'description' => 'Person or organization financing the student\'s education.', 'category' => RelationshipCategory::Financial, 'sort_order' => 9],
        ['code' => 'foster_parent', 'label' => 'Foster Parent', 'description' => 'Foster family member hosting the student.', 'category' => RelationshipCategory::Legal, 'sort_order' => 10],
        ['code' => 'legal_representative', 'label' => 'Legal Representative', 'description' => 'Person empowered to represent the student legally.', 'category' => RelationshipCategory::Legal, 'sort_order' => 11],
        ['code' => 'emergency_contact', 'label' => 'Emergency Contact', 'description' => 'Contact to reach in case of emergency.', 'category' => RelationshipCategory::Emergency, 'sort_order' => 12],
        ['code' => 'other', 'label' => 'Other', 'description' => 'Any other relationship not covered above.', 'category' => RelationshipCategory::Other, 'sort_order' => 13],
    ],

    /*
    |--------------------------------------------------------------------------
    | Relationship role flags
    |--------------------------------------------------------------------------
    |
    | A relationship carries business meaning through role flags. A single
    | guardian may hold several roles for the same student (primary + medical
    | contact + authorized pickup, for example).
    |
    */

    'flags' => [
        'primary' => 'is_primary',
        'emergency_contact' => 'is_emergency_contact',
        'authorized_pickup' => 'is_authorized_pickup',
        'financial_responsible' => 'is_financial_responsible',
        'notification_recipient' => 'is_notification_recipient',
        'medical_contact' => 'is_medical_contact',
    ],
];
