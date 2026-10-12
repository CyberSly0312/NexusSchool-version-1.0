<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Academic Policies engine
    |--------------------------------------------------------------------------
    |
    | Configuration of the Academic Policies engine (SPR-009.5). The engine is
    | the single origin of academic rules (ARCHITECTURE_DECISIONS §6). It
    | stores school-owned policies with typed values; it never invents rule
    | values — a policy only exists when the school defines it.
    |
    */

    'policy' => [
        'key_max_length' => (int) env('NEXUS_ACADEMIC_POLICY_KEY_MAX_LENGTH', 100),
        'label_max_length' => (int) env('NEXUS_ACADEMIC_POLICY_LABEL_MAX_LENGTH', 255),
        'description_max_length' => (int) env('NEXUS_ACADEMIC_POLICY_DESCRIPTION_MAX_LENGTH', 2000),
        'audit' => (bool) env('NEXUS_ACADEMIC_POLICY_AUDIT', true),
    ],
];
