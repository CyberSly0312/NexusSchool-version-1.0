<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Session lifecycle
    |--------------------------------------------------------------------------
    |
    | Ordered lifecycle of an AttendanceSession. The engine only moves forward
    | through this list; reverting a step is a controlled Unlock transition that
    | always leaves an audit trail (SPR-008).
    |
    */

    'session_lifecycle' => [
        'draft',
        'open',
        'taking_attendance',
        'submitted',
        'locked',
        'archived',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance statuses
    |--------------------------------------------------------------------------
    |
    | The ten canonical statuses. They are configurable: a school can reclassify
    | which statuses count as "present", which as "absent" and which are neutral
    | (excluded from the denominator) without changing any code. `key` is the
    | stored value and the enum case name.
    |
    */

    'statuses' => [
        ['key' => 'present', 'label' => 'Présent', 'color' => 'success', 'counts_as_present' => true, 'counts_as_absent' => false, 'neutral' => false, 'sort' => 10],
        ['key' => 'late', 'label' => 'En retard', 'color' => 'warning', 'counts_as_present' => true, 'counts_as_absent' => false, 'neutral' => false, 'sort' => 20],
        ['key' => 'excused', 'label' => 'Excusé', 'color' => 'info', 'counts_as_present' => false, 'counts_as_absent' => false, 'neutral' => true, 'sort' => 30],
        ['key' => 'medical', 'label' => 'Médical', 'color' => 'info', 'counts_as_present' => false, 'counts_as_absent' => false, 'neutral' => true, 'sort' => 40],
        ['key' => 'authorized_leave', 'label' => 'Absence autorisée', 'color' => 'info', 'counts_as_present' => false, 'counts_as_absent' => false, 'neutral' => true, 'sort' => 50],
        ['key' => 'unexcused', 'label' => 'Non excusé', 'color' => 'danger', 'counts_as_present' => false, 'counts_as_absent' => true, 'neutral' => false, 'sort' => 60],
        ['key' => 'remote', 'label' => 'À distance', 'color' => 'primary', 'counts_as_present' => true, 'counts_as_absent' => false, 'neutral' => false, 'sort' => 70],
        ['key' => 'school_activity', 'label' => 'Activité scolaire', 'color' => 'primary', 'counts_as_present' => true, 'counts_as_absent' => false, 'neutral' => false, 'sort' => 80],
        ['key' => 'holiday', 'label' => 'Congé', 'color' => 'gray', 'counts_as_present' => false, 'counts_as_absent' => false, 'neutral' => true, 'sort' => 90],
        ['key' => 'absent', 'label' => 'Absent', 'color' => 'danger', 'counts_as_present' => false, 'counts_as_absent' => true, 'neutral' => false, 'sort' => 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | Percentage rounding
    |--------------------------------------------------------------------------
    */

    'percentage' => [
        'decimal_places' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Risk engine
    |--------------------------------------------------------------------------
    |
    | The risk score is 0-100 and is a weighted combination of the attendance
    | percentage, the late percentage, the consecutive absences and the
    | consecutive lates. Each factor is normalized to a 0-1 sub-score first.
    |
    */

    'risk' => [
        'enabled' => true,
        'weights' => [
            'attendance_percentage' => 0.55,
            'late_percentage' => 0.15,
            'consecutive_absences' => 0.20,
            'consecutive_lates' => 0.10,
        ],
        'levels' => [
            'low' => ['min' => 0.0, 'color' => 'success'],
            'medium' => ['min' => 40.0, 'color' => 'warning'],
            'high' => ['min' => 70.0, 'color' => 'danger'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerts
    |--------------------------------------------------------------------------
    |
    | Thresholds that drive the alert engine. The engine only publishes domain
    | events; actual notification delivery is owned by the future Notifications
    | domain (out of sprint scope) through FutureNotificationContract.
    |
    */

    'alerts' => [
        'enabled' => true,
        'attendance_percentage_threshold' => (float) env('NEXUS_ATTENDANCE_LOW_THRESHOLD', 75.0),
        'late_percentage_threshold' => (float) env('NEXUS_ATTENDANCE_LATE_THRESHOLD', 20.0),
        'consecutive_absences_threshold' => (int) env('NEXUS_ATTENDANCE_CONSECUTIVE_ABSENCES', 3),
        'consecutive_lates_threshold' => (int) env('NEXUS_ATTENDANCE_CONSECUTIVE_LATES', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bulk performance
    |--------------------------------------------------------------------------
    |
    | Bulk operations are chunked so large classes (and the 10,000+ records/day
    | target) stay bounded in memory and query count.
    |
    */

    'bulk' => [
        'chunk_size' => (int) env('NEXUS_ATTENDANCE_CHUNK_SIZE', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Late grace
    |--------------------------------------------------------------------------
    |
    | Minutes after the scheduled start at which a student is still "present"
    | instead of "late".
    |
    */

    'late_grace_minutes' => (int) env('NEXUS_ATTENDANCE_GRACE_MINUTES', 10),
];
