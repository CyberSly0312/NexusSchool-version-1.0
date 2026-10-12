<?php

declare(strict_types=1);

use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicCommunicationContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ClassCouncilContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\ReportCardContract;
use App\Domains\CertificateAndTranscript\Domain\Contracts\StudentIdentityContract;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequestEntry;
use Illuminate\Support\Facades\File;

/**
 * The Certificate & Transcript domain references foreign domains only through
 * the contracts declared in Domain/Contracts and implemented by the adapters
 * living in Infrastructure/Contracts. The Domain and Application layers must
 * stay pure: they may never import a model, enum or service from another
 * domain.
 */
test('the certificate and transcript Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/CertificateAndTranscript');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all(
            '/use\s+App\\\Domains\\\\(Student|Enrollment|Staff|Timetable|Academic|Guardian|Attendance|Assessment|Gradebook|AcademicCommunication|ReportCard|ClassCouncil|AcademicPolicy|RecordsLifecycle)\\\\/',
            $contents,
            $matches,
        );

        expect($matches[0], $file->getRelativePathname())
            ->toBeEmpty();
    }
});

/**
 * The Infrastructure/Contracts directory is the only sanctioned gateway to
 * foreign domains, and every adapter must implement a domain contract.
 */
test('every certificate and transcript adapter implements one of the domain contracts', function (): void {
    $contracts = [
        ReportCardContract::class,
        ClassCouncilContract::class,
        AcademicPoliciesContract::class,
        EnrollmentContract::class,
        StudentIdentityContract::class,
        AcademicCommunicationContract::class,
    ];

    foreach (File::files(app_path('Domains/CertificateAndTranscript/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\CertificateAndTranscript\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

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
test('every certificate and transcript domain contract is bound in the container', function (): void {
    $contracts = [
        ReportCardContract::class,
        ClassCouncilContract::class,
        AcademicPoliciesContract::class,
        EnrollmentContract::class,
        StudentIdentityContract::class,
        AcademicCommunicationContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});

/**
 * Every completion aggregate must receive its public uuid from the shared
 * observer rather than from the application code.
 */
test('every certificate and transcript aggregate carries a generated public uuid on creation', function (): void {
    $schoolId = makeTenant();
    $context = makeAssessmentContext($schoolId, false);
    $enrollment = enrollStudent(
        $schoolId,
        $context['year']->id,
        $context['program']->id,
        $context['level']->id,
        $context['class']->id,
    );

    $request = CompletionRequest::factory()
        ->forSchool($schoolId)
        ->forStudent($enrollment->student_id)
        ->create();

    expect($request->uuid)->not->toBeNull();

    $entry = CompletionRequestEntry::factory()
        ->forSchool($schoolId)
        ->forRequest($request->getKey())
        ->create();

    expect($entry->uuid)->not->toBeNull();
});
