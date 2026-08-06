<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The Assessment domain references foreign domains only through the contracts
 * declared in Domain/Contracts and implemented by the adapters living in
 * Infrastructure/Contracts. The Domain and Application layers must stay pure:
 * they may never import a model, enum or service from another domain.
 */
test('the assessment Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/Assessment');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all('/use\s+App\\\Domains\\\\(Student|Enrollment|Staff|Timetable|Academic|Guardian|Attendance|Gradebook)\\\\/', $contents, $matches);

        expect($matches[0], $file->getRelativePathname())
            ->toBeEmpty();
    }
});

/**
 * The Infrastructure/Contracts directory is the only sanctioned gateway to
 * foreign domains, and every adapter must implement a contract.
 */
test('every assessment adapter implements one of the domain contracts', function (): void {
    $contracts = [
        App\Domains\Assessment\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\Assessment\Domain\Contracts\EnrollmentContract::class,
        App\Domains\Assessment\Domain\Contracts\StudentIdentityContract::class,
        App\Domains\Assessment\Domain\Contracts\StaffContract::class,
        App\Domains\Assessment\Domain\Contracts\TimetableContract::class,
        App\Domains\Assessment\Domain\Contracts\AttendanceContract::class,
    ];

    foreach (File::files(app_path('Domains/Assessment/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\Assessment\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

        expect(class_exists($class), $class)->toBeTrue();

        $implements = false;
        foreach ($contracts as $contract) {
            if (is_subclass_of($class, $contract)) {
                $implements = true;
                break;
            }
        }

        expect($implements, $class)->toBeTrue();
    }
});

/**
 * All six contracts must have a container binding wired by the provider.
 */
test('every assessment domain contract is bound in the container', function (): void {
    $contracts = [
        App\Domains\Assessment\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\Assessment\Domain\Contracts\EnrollmentContract::class,
        App\Domains\Assessment\Domain\Contracts\StudentIdentityContract::class,
        App\Domains\Assessment\Domain\Contracts\StaffContract::class,
        App\Domains\Assessment\Domain\Contracts\TimetableContract::class,
        App\Domains\Assessment\Domain\Contracts\AttendanceContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});

/**
 * Every Assessment-domain model that exposes a public uuid must receive it from
 * the shared observer rather than from the application code.
 */
test('every assessment aggregate carries a generated public uuid on creation', function (): void {
    $schoolId = makeTenant();

    $type = \App\Domains\Assessment\Infrastructure\Models\AssessmentType::factory()
        ->forSchool($schoolId)
        ->create();

    $session = \App\Domains\Assessment\Infrastructure\Models\AssessmentSession::create([
        'school_id' => $schoolId,
        'title' => 'Session test',
    ]);

    expect($type->uuid)->not->toBeNull()
        ->and($session->uuid)->not->toBeNull();
});
