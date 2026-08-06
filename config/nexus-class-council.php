<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Class Council engine
    |--------------------------------------------------------------------------
    |
    | Configuration of the Class Council engine (SPR-013). The engine validates
    | published academic results, records council decisions and exposes them to
    | downstream engines through domain contracts. Academic validation belongs
    | exclusively to Class Council.
    |
    */

    'promotion' => [
        'passing_percentage' => (float) env('NEXUS_CLASS_COUNCIL_PASSING_PERCENTAGE', 50.0),
    ],

    'decision' => [
        'override_requires_approval' => (bool) env('NEXUS_CLASS_COUNCIL_OVERRIDE_REQUIRES_APPROVAL', true),
        'justification_max_length' => (int) env('NEXUS_CLASS_COUNCIL_JUSTIFICATION_MAX_LENGTH', 2000),
        'summary_max_length' => (int) env('NEXUS_CLASS_COUNCIL_SUMMARY_MAX_LENGTH', 1000),
    ],

];
