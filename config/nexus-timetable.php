<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Lesson duration
    |--------------------------------------------------------------------------
    |
    | Default length (in minutes) of a single teaching period when none is
    | supplied by the caller. Used by the auto-generation pass.
    |
    */

    'default_lesson_duration' => (int) env('NEXUS_TIMETABLE_LESSON_DURATION', 55),

    /*
    |--------------------------------------------------------------------------
    | Default weekly workload
    |--------------------------------------------------------------------------
    |
    | Lessons per subject per week when the caller does not supply an explicit
    | workload map. The Timetable engine never derives the workload from the
    | Academic domain's coefficients — it only consumes what it is given.
    |
    */

    'default_weekly_lessons_per_subject' => (int) env('NEXUS_TIMETABLE_WEEKLY_LESSONS', 2),

    /*
    |--------------------------------------------------------------------------
    | Conflict-free publishing
    |--------------------------------------------------------------------------
    |
    | When true, PublishTimetableAction refuses to publish a timetable that still
    | contains detected conflicts. When false, conflicts are reported but do not
    | block publication (recommended during the transition to the engine).
    |
    */

    'require_conflict_free_publish' => (bool) env('NEXUS_TIMETABLE_CONFLICT_FREE_PUBLISH', false),

    /*
    |--------------------------------------------------------------------------
    | Master teaching grid
    |--------------------------------------------------------------------------
    |
    | The canonical weekly grid materialized per tenant by TeachingPeriodSeeder.
    | day 0 = Monday .. 6 = Sunday. Schools can later adjust their own rows
    | without touching this catalog.
    |
    */

    'teaching_periods' => [
        ['day' => 0, 'period' => 1, 'starts_at' => '08:00', 'ends_at' => '08:55'],
        ['day' => 0, 'period' => 2, 'starts_at' => '09:05', 'ends_at' => '10:00'],
        ['day' => 0, 'period' => 3, 'starts_at' => '10:10', 'ends_at' => '11:05'],
        ['day' => 0, 'period' => 4, 'starts_at' => '11:15', 'ends_at' => '12:10'],
        ['day' => 0, 'period' => 5, 'starts_at' => '13:00', 'ends_at' => '13:55'],
        ['day' => 0, 'period' => 6, 'starts_at' => '14:05', 'ends_at' => '15:00'],
        ['day' => 0, 'period' => 7, 'starts_at' => '15:10', 'ends_at' => '16:05'],
        ['day' => 0, 'period' => 8, 'starts_at' => '16:15', 'ends_at' => '17:10'],
        ['day' => 1, 'period' => 1, 'starts_at' => '08:00', 'ends_at' => '08:55'],
        ['day' => 1, 'period' => 2, 'starts_at' => '09:05', 'ends_at' => '10:00'],
        ['day' => 1, 'period' => 3, 'starts_at' => '10:10', 'ends_at' => '11:05'],
        ['day' => 1, 'period' => 4, 'starts_at' => '11:15', 'ends_at' => '12:10'],
        ['day' => 1, 'period' => 5, 'starts_at' => '13:00', 'ends_at' => '13:55'],
        ['day' => 1, 'period' => 6, 'starts_at' => '14:05', 'ends_at' => '15:00'],
        ['day' => 1, 'period' => 7, 'starts_at' => '15:10', 'ends_at' => '16:05'],
        ['day' => 1, 'period' => 8, 'starts_at' => '16:15', 'ends_at' => '17:10'],
        ['day' => 2, 'period' => 1, 'starts_at' => '08:00', 'ends_at' => '08:55'],
        ['day' => 2, 'period' => 2, 'starts_at' => '09:05', 'ends_at' => '10:00'],
        ['day' => 2, 'period' => 3, 'starts_at' => '10:10', 'ends_at' => '11:05'],
        ['day' => 2, 'period' => 4, 'starts_at' => '11:15', 'ends_at' => '12:10'],
        ['day' => 2, 'period' => 5, 'starts_at' => '13:00', 'ends_at' => '13:55'],
        ['day' => 2, 'period' => 6, 'starts_at' => '14:05', 'ends_at' => '15:00'],
        ['day' => 2, 'period' => 7, 'starts_at' => '15:10', 'ends_at' => '16:05'],
        ['day' => 2, 'period' => 8, 'starts_at' => '16:15', 'ends_at' => '17:10'],
        ['day' => 3, 'period' => 1, 'starts_at' => '08:00', 'ends_at' => '08:55'],
        ['day' => 3, 'period' => 2, 'starts_at' => '09:05', 'ends_at' => '10:00'],
        ['day' => 3, 'period' => 3, 'starts_at' => '10:10', 'ends_at' => '11:05'],
        ['day' => 3, 'period' => 4, 'starts_at' => '11:15', 'ends_at' => '12:10'],
        ['day' => 3, 'period' => 5, 'starts_at' => '13:00', 'ends_at' => '13:55'],
        ['day' => 3, 'period' => 6, 'starts_at' => '14:05', 'ends_at' => '15:00'],
        ['day' => 3, 'period' => 7, 'starts_at' => '15:10', 'ends_at' => '16:05'],
        ['day' => 3, 'period' => 8, 'starts_at' => '16:15', 'ends_at' => '17:10'],
        ['day' => 4, 'period' => 1, 'starts_at' => '08:00', 'ends_at' => '08:55'],
        ['day' => 4, 'period' => 2, 'starts_at' => '09:05', 'ends_at' => '10:00'],
        ['day' => 4, 'period' => 3, 'starts_at' => '10:10', 'ends_at' => '11:05'],
        ['day' => 4, 'period' => 4, 'starts_at' => '11:15', 'ends_at' => '12:10'],
        ['day' => 4, 'period' => 5, 'starts_at' => '13:00', 'ends_at' => '13:55'],
        ['day' => 4, 'period' => 6, 'starts_at' => '14:05', 'ends_at' => '15:00'],
        ['day' => 4, 'period' => 7, 'starts_at' => '15:10', 'ends_at' => '16:05'],
        ['day' => 4, 'period' => 8, 'starts_at' => '16:15', 'ends_at' => '17:10'],
    ],
];
