<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The Attendance domain references foreign domains only through the contracts
 * declared in Domain/Contracts and implemented by the adapters living in
 * Infrastructure/Contracts. The Domain and Application layers must stay pure:
 * they may never import a model, enum or service from another domain.
 */
test('the attendance Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/Attendance');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all('/use\s+App\\\Domains\\\\(Student|Enrollment|Staff|Timetable|Academic|Guardian)\\\\/', $contents, $matches);

        expect($matches[0], $file->getRelativePathname())
            ->toBeEmpty();
    }
});

/**
 * The Infrastructure/Contracts directory is the only sanctioned gateway to
 * foreign domains, and every adapter must implement a contract.
 */
test('every attendance adapter implements one of the domain contracts', function (): void {
    $contracts = [
        App\Domains\Attendance\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\Attendance\Domain\Contracts\EnrollmentContract::class,
        App\Domains\Attendance\Domain\Contracts\StudentIdentityContract::class,
        App\Domains\Attendance\Domain\Contracts\StaffContract::class,
        App\Domains\Attendance\Domain\Contracts\TimetableContract::class,
        App\Domains\Attendance\Domain\Contracts\FutureNotificationContract::class,
        App\Domains\Attendance\Domain\Contracts\AnalyticsContract::class,
    ];

    foreach (File::files(app_path('Domains/Attendance/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\Attendance\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

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
 * All seven contracts must have a container binding wired by the provider.
 */
test('every attendance domain contract is bound in the container', function (): void {
    $contracts = [
        App\Domains\Attendance\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\Attendance\Domain\Contracts\EnrollmentContract::class,
        App\Domains\Attendance\Domain\Contracts\StudentIdentityContract::class,
        App\Domains\Attendance\Domain\Contracts\StaffContract::class,
        App\Domains\Attendance\Domain\Contracts\TimetableContract::class,
        App\Domains\Attendance\Domain\Contracts\FutureNotificationContract::class,
        App\Domains\Attendance\Domain\Contracts\AnalyticsContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});
