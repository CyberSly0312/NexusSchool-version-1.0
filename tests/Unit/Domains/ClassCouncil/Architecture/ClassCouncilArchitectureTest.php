<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The Class Council domain references foreign domains only through the
 * contracts declared in Domain/Contracts and implemented by the adapters living
 * in Infrastructure/Contracts. The Domain and Application layers must stay pure:
 * they may never import a model, enum or service from another domain.
 */
test('the ClassCouncil Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/ClassCouncil');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all(
            '/use\s+App\\Domains\\\\(Student|Enrollment|Staff|Timetable|Academic|Guardian|Attendance|Assessment|Gradebook|AcademicCommunication|ReportCard)\\\\/',
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
test('every ClassCouncil adapter implements one of the domain contracts', function (): void {
    $contracts = [
        App\Domains\ClassCouncil\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\ClassCouncil\Domain\Contracts\EnrollmentContract::class,
        App\Domains\ClassCouncil\Domain\Contracts\GradebookContract::class,
    ];

    foreach (File::files(app_path('Domains/ClassCouncil/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\ClassCouncil\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

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
test('every ClassCouncil domain contract is bound in the container', function (): void {
    $contracts = [
        App\Domains\ClassCouncil\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\ClassCouncil\Domain\Contracts\EnrollmentContract::class,
        App\Domains\ClassCouncil\Domain\Contracts\GradebookContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});

/**
 * Every ClassCouncil-domain aggregate that exposes a public uuid must receive it
 * from the shared observer rather than from the application code.
 */
test('every ClassCouncil aggregate carries a generated public uuid on creation', function (): void {
    $schoolId = makeTenant();
    $context = makeAssessmentContext($schoolId, false);

    $session = \App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession::factory()
        ->forSchool($schoolId)
        ->forClass($context['class']->id)
        ->create();

    expect($session->uuid)->not->toBeNull();

    $decision = \App\Domains\ClassCouncil\Infrastructure\Models\CouncilDecision::factory()
        ->forSchool($schoolId)
        ->forSession($session->id)
        ->create();

    expect($decision->uuid)->not->toBeNull();

    $deliberation = \App\Domains\ClassCouncil\Infrastructure\Models\CouncilDeliberation::factory()
        ->forSchool($schoolId)
        ->forSession($session->id)
        ->create();

    expect($deliberation->uuid)->not->toBeNull();
});
