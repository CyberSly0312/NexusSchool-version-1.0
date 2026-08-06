<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The Academic Communication engine references foreign domains only through the
 * contracts declared in Domain/Contracts and implemented by the adapters living
 * in Infrastructure/Contracts. The Domain and Application layers must stay pure:
 * they may never import a model, enum or service from another domain.
 */
test('the AcademicCommunication Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/AcademicCommunication');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    $pattern = '/use\s+App\\Domains\\\\(?!AcademicCommunication\\\\)[A-Z][A-Za-z]+\\\\/';

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all($pattern, $contents, $matches);

        expect($matches[0], $file->getRelativePathname())
            ->toBeEmpty();
    }
});

/**
 * The Infrastructure/Contracts directory is the only sanctioned gateway to
 * foreign domains, and every adapter must implement one of the domain contracts.
 */
test('every AcademicCommunication adapter implements one of the domain contracts', function (): void {
    $contracts = [
        App\Domains\AcademicCommunication\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\AcademicPoliciesContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\AssessmentContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\AttendanceContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\ClassCouncilContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\GuardianContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\SchoolContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\StaffContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract::class,
    ];

    foreach (File::files(app_path('Domains/AcademicCommunication/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\AcademicCommunication\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

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
 * Every cross-domain contract must have a container binding wired by the
 * provider. The channel contract is intentionally excluded: its drivers are
 * resolved through the config-driven PublicationDispatcher, not the container.
 */
test('every AcademicCommunication domain contract is bound in the container', function (): void {
    $contracts = [
        App\Domains\AcademicCommunication\Domain\Contracts\AcademicFrameworkContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\AcademicPoliciesContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\AssessmentContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\AttendanceContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\ClassCouncilContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\GuardianContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\SchoolContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\StaffContract::class,
        App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});

/**
 * Every AcademicCommunication-domain aggregate that exposes a public uuid must
 * receive it from the shared observer rather than from the application code.
 */
test('every AcademicCommunication aggregate carries a generated public uuid on creation', function (): void {
    $schoolId = makeTenant();

    $template = \App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentTemplate::factory()
        ->forSchool($schoolId)
        ->create();

    $policy = \App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy::factory()
        ->forSchool($schoolId)
        ->create();

    $channel = \App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel::factory()
        ->forSchool($schoolId)
        ->create();

    $document = \App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument::factory()
        ->forSchool($schoolId)
        ->create();

    expect($template->uuid)->not->toBeNull()
        ->and($policy->uuid)->not->toBeNull()
        ->and($channel->uuid)->not->toBeNull()
        ->and($document->uuid)->not->toBeNull();
});
