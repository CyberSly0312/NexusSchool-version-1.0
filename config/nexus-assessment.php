<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Assessment lifecycle
    |--------------------------------------------------------------------------
    |
    | Ordered lifecycle of an Assessment. The engine only moves forward through
    | this list. Published assessments are immutable: their definition (title,
    | maximum score, passing score, weight and components) can no longer be
    | edited; scores remain recordable until the assessment is completed.
    |
    */

    'lifecycle' => [
        'draft',
        'scheduled',
        'published',
        'completed',
        'archived',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assessment types
    |--------------------------------------------------------------------------
    |
    | The canonical assessment types. They are configurable: a school can add,
    | rename or reorder types without touching the code. `key` is the stored
    | value and the AssessmentType enum case name; `custom` marks the type that
    | schools use for free-form types.
    |
    */

    'types' => [
        ['key' => 'quiz', 'label' => 'Quiz', 'color' => 'info', 'sort' => 10],
        ['key' => 'homework', 'label' => 'Devoir maison', 'color' => 'gray', 'sort' => 20],
        ['key' => 'assignment', 'label' => 'Devoir', 'color' => 'gray', 'sort' => 30],
        ['key' => 'practical', 'label' => 'Pratique', 'color' => 'warning', 'sort' => 40],
        ['key' => 'laboratory', 'label' => 'Laboratoire', 'color' => 'warning', 'sort' => 50],
        ['key' => 'project', 'label' => 'Projet', 'color' => 'primary', 'sort' => 60],
        ['key' => 'oral', 'label' => 'Oral', 'color' => 'info', 'sort' => 70],
        ['key' => 'midterm', 'label' => 'Examen partiel', 'color' => 'danger', 'sort' => 80],
        ['key' => 'final_exam', 'label' => 'Examen final', 'color' => 'danger', 'sort' => 90],
        ['key' => 'continuous_assessment', 'label' => 'Évaluation continue', 'color' => 'success', 'sort' => 100],
        ['key' => 'behavior_assessment', 'label' => 'Évaluation comportementale', 'color' => 'info', 'sort' => 110],
        ['key' => 'custom', 'label' => 'Personnalisé', 'color' => 'gray', 'sort' => 120],
    ],

    /*
    |--------------------------------------------------------------------------
    | Score validation
    |--------------------------------------------------------------------------
    |
    | A score is valid when it is a finite number inside [0, maximum_score].
    | When a passing score is configured, a score strictly below it is flagged
    | as "below passing" (a fail), not as an invalid score.
    |
    */

    'validation' => [
        'decimal_places' => (int) env('NEXUS_ASSESSMENT_SCORE_DECIMALS', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Weights policy
    |--------------------------------------------------------------------------
    |
    | Assessment weights are expressed as a percentage of the total term grade.
    | A school may enforce that the sum of weights does not exceed a ceiling
    | (default 100 %); when `enforce_total` is false the ceiling is advisory.
    |
    */

    'weights' => [
        'enforce_total' => (bool) env('NEXUS_ASSESSMENT_ENFORCE_WEIGHT_TOTAL', true),
        'max_total' => (float) env('NEXUS_ASSESSMENT_MAX_WEIGHT_TOTAL', 100.0),
        'decimal_places' => (int) env('NEXUS_ASSESSMENT_WEIGHT_DECIMALS', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Remarks
    |--------------------------------------------------------------------------
    */

    'remarks' => [
        'max_length' => (int) env('NEXUS_ASSESSMENT_REMARK_MAX_LENGTH', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    |
    | Aggregates are rounded to a fixed number of decimals. `pass_rate` uses the
    | assessment passing score when set, otherwise the school-scoped
    | `academic.assessment.default_passing_percentage` policy, otherwise the
    | configurable 50 % default below.
    |
    */

    'statistics' => [
        'decimal_places' => (int) env('NEXUS_ASSESSMENT_STATISTICS_DECIMALS', 2),
        'default_passing_percentage' => (float) env('NEXUS_ASSESSMENT_DEFAULT_PASSING_PERCENTAGE', 50.0),
        'passing_percentage_key' => env('NEXUS_ASSESSMENT_DEFAULT_PASSING_PERCENTAGE_KEY', 'academic.assessment.default_passing_percentage'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bulk performance
    |--------------------------------------------------------------------------
    |
    | Bulk score writes are chunked so large classes stay bounded in memory and
    | query count.
    |
    */

    'bulk' => [
        'chunk_size' => (int) env('NEXUS_ASSESSMENT_CHUNK_SIZE', 200),
    ],
];
