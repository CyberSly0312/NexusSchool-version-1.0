<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The Report Card domain references foreign domains only through the contracts
 * declared in Domain/Contracts and implemented by the adapters living in
 * Infrastructure/Contracts. The Domain and Application layers must stay pure:
 * they may never import a model, enum or service from another domain.
 */
test('the report card Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/ReportCard');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all(
            '/use\s+App\\\Domains\\\\(Student|Enrollment|Staff|Timetable|Academic|Guardian|Attendance|Assessment|Gradebook|AcademicCommunication)\\\\/',
            $contents,
            $matches,
        );

        expect($matches[0], $file->getRelativePathname())
            ->toBeEmpty();
    }
});

/**
 * The Infrastructure/Contracts directory is the only sanctioned gateway to
 * foreign domains, and every adapter must implement a contract.
 */
test('every report card adapter implements one of the domain contracts', function (): void {
    $contracts = [
        App\Domains\ReportCard\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\ReportCard\Domain\Contracts\EnrollmentContract::class,
        App\Domains\ReportCard\Domain\Contracts\GradebookContract::class,
        App\Domains\ReportCard\Domain\Contracts\AttendanceContract::class,
        App\Domains\ReportCard\Domain\Contracts\AcademicPoliciesContract::class,
        App\Domains\ReportCard\Domain\Contracts\ClassCouncilContract::class,
        App\Domains\ReportCard\Domain\Contracts\AcademicCommunicationContract::class,
        App\Domains\ReportCard\Domain\Contracts\RecordsLifecycleContract::class,
    ];

    foreach (File::files(app_path('Domains/ReportCard/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\ReportCard\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

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
 * Every contract must have a container binding wired by the provider.
 */
test('every report card domain contract is bound in the container', function (): void {
    $contracts = [
        App\Domains\ReportCard\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\ReportCard\Domain\Contracts\EnrollmentContract::class,
        App\Domains\ReportCard\Domain\Contracts\GradebookContract::class,
        App\Domains\ReportCard\Domain\Contracts\AttendanceContract::class,
        App\Domains\ReportCard\Domain\Contracts\AcademicPoliciesContract::class,
        App\Domains\ReportCard\Domain\Contracts\ClassCouncilContract::class,
        App\Domains\ReportCard\Domain\Contracts\AcademicCommunicationContract::class,
        App\Domains\ReportCard\Domain\Contracts\RecordsLifecycleContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});

/**
 * Every Report Card-domain aggregate that exposes a public uuid must receive it
 * from the shared observer rather than from the application code.
 */
test('every report card aggregate carries a generated public uuid on creation', function (): void {
    $schoolId = makeTenant();
    $context = makeAssessmentContext($schoolId, false);
    $level = $context['level'];

    $enrollment = enrollStudent(
        $schoolId,
        $context['year']->id,
        $context['program']->id,
        $level->id,
        $context['class']->id,
    );

    $reportCard = \App\Domains\ReportCard\Infrastructure\Models\ReportCard::factory()
        ->forSchool($schoolId)
        ->forClass($context['class']->id)
        ->create();

    expect($reportCard->uuid)->not->toBeNull();

    $entry = \App\Domains\ReportCard\Infrastructure\Models\ReportCardEntry::factory()
        ->forSchool($schoolId)
        ->forReportCard($reportCard->id)
        ->create([
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
        ]);

    expect($entry->uuid)->not->toBeNull();
});
