<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Gradebook lifecycle
    |--------------------------------------------------------------------------
    |
    | Ordered lifecycle of a term/subject gradebook. The engine only moves
    | forward through this list. Once `locked` the entries are immutable and
    | the published report card figures can no longer change; the state can be
    | re-opened explicitly (unlock) when a correction is authorized.
    |
    */

    'lifecycle' => [
        'draft',
        'calculated',
        'locked',
        'published',
        'archived',
    ],

    /*
    |--------------------------------------------------------------------------
    | Grade scaling
    |--------------------------------------------------------------------------
    |
    | Averages are computed as a percentage and mapped onto the school grading
    | system. Primary schools commonly use a 100-point scale, secondary schools
    | a 20-point scale. The scale only changes how the percentage is displayed
    | as an `average`; percentages and letter grades stay canonical.
    |
    */

    'grade_system' => [
        'scale' => (int) env('NEXUS_GRADEBOOK_SCALE', 20),
        'decimal_places' => (int) env('NEXUS_GRADEBOOK_AVERAGE_DECIMALS', 2),
        'default_passing_percentage' => (float) env('NEXUS_GRADEBOOK_DEFAULT_PASSING_PERCENTAGE', 50.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ranking
    |--------------------------------------------------------------------------
    |
    | Rankings use standard competition ranking ("1224"): students with equal
    | averages share the same rank and the next rank skips. `max_report_rows`
    | caps the number of ranked rows surfaced to the communication engine.
    |
    */

    'ranking' => [
        'decimal_places' => (int) env('NEXUS_GRADEBOOK_RANKING_DECIMALS', 2),
        'max_report_rows' => (int) env('NEXUS_GRADEBOOK_MAX_RANKING_REPORT_ROWS', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | An entry is valid when it carries a finite percentage inside [0, 100].
    | `auto_validate_on_calculate` marks entries as validated as soon as the
    | gradebook is calculated; teachers may re-open an entry while the
    | gradebook is not yet locked.
    |
    */

    'validation' => [
        'auto_validate_on_calculate' => (bool) env('NEXUS_GRADEBOOK_AUTO_VALIDATE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Remarks
    |--------------------------------------------------------------------------
    */

    'remarks' => [
        'max_length' => (int) env('NEXUS_GRADEBOOK_REMARK_MAX_LENGTH', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Calculation
    |--------------------------------------------------------------------------
    |
    | Term averages are the weighted mean of the assessment percentages, the
    | weights being the assessment weights (percentages of the term grade).
    | Assessments still in `draft` or `scheduled` are excluded. A gradebook with
    | no published assessment data yields no entries (students receive no
    | average rather than a zero).
    |
    */

    'calculation' => [
        'exclude_statuses' => ['draft', 'scheduled'],
        'chunk_size' => (int) env('NEXUS_GRADEBOOK_CHUNK_SIZE', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | GPA & academic standing
    |--------------------------------------------------------------------------
    |
    | A student's GPA is the coefficient-weighted mean of the grade points of
    | their published grade entries; the CGPA is the same aggregation over every
    | published entry (no term filter). Academic standing derives from the
    | overall weighted percentage and the number of failing subjects.
    |
    */

    'gpa' => [
        'scale' => (float) env('NEXUS_GRADEBOOK_GPA_SCALE', 4.0),
        'decimal_places' => (int) env('NEXUS_GRADEBOOK_GPA_DECIMALS', 2),
    ],

    'standing' => [
        'passing_percentage' => (float) env('NEXUS_GRADEBOOK_STANDING_PASSING_PERCENTAGE', 50.0),
        'passing_percentage_key' => 'academic.gradebook.passing_percentage',
        'distinction_percentage' => (float) env('NEXUS_GRADEBOOK_STANDING_DISTINCTION_PERCENTAGE', 80.0),
        'distinction_percentage_key' => 'academic.gradebook.distinction_percentage',
        'conditional_failures' => (int) env('NEXUS_GRADEBOOK_STANDING_CONDITIONAL_FAILURES', 1),
        'conditional_failures_key' => 'academic.gradebook.conditional_failures',
    ],
];
