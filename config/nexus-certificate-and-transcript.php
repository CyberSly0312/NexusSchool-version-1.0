<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Certificate & Transcript engine
    |--------------------------------------------------------------------------
    |
    | Configuration of the Certificate & Transcript engine (SPR-015). The engine
    | assembles completion documents from published evidence (SPR-011 snapshots,
    | SPR-013 decisions, Academic Policies, Enrollment graduation facts) and
    | delegates the official document to SPR-012. It never renders a PDF itself.
    |
    */

    'document_type' => [
        'transcript' => 'transcript',
        'certificate' => 'certificate',
    ],

    'origin' => [
        'transcript' => 'transcript',
        'certificate' => 'certificate',
    ],

    /*
    | Document titles passed to the SPR-012 generate seam. `{student}` is
    | replaced with the student's full name by the actions.
    */
    'titles' => [
        'transcript' => 'Relevé de notes de {student}',
        'certificate' => 'Certificat de fin d\'études de {student}',
    ],

    /*
    | Certificate eligibility thresholds are policy-driven (ADR-021 Decision 4):
    | the orchestration reads the per-school policy keys below and falls back to
    | the configured defaults when the school has not defined the policy.
    */
    'certificate_eligibility' => [
        'passing_percentage_key' => 'academic.certificate.passing_percentage',
        'allowed_failed_subjects_key' => 'academic.certificate.allowed_failed_subjects',
        'default_passing_percentage' => 50,
        'default_allowed_failed_subjects' => 0,
    ],

];
