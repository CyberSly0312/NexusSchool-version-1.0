<?php

declare(strict_types=1);

use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyScope;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Domains\AcademicPolicy\Domain\References\AcademicPolicyReference;
use Illuminate\Support\Facades\File;

/**
 * The Academic Policy domain follows the same layering rules as the other
 * engine domains: the Domain and Application layers never import a foreign
 * domain class, adapters live in Infrastructure/Contracts only, and every
 * contract is bound by the provider.
 */
test('the AcademicPolicy Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/AcademicPolicy');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all(
            '/use\s+App\\\Domains\\\\(Student|Enrollment|Staff|Timetable|Academic|Guardian|Attendance|Assessment|Gradebook|ClassCouncil|ReportCard|AcademicCommunication)\\\\/',
            $contents,
            $matches,
        );

        expect($matches[0], $file->getRelativePathname())
            ->toBeEmpty();
    }
});

test('every AcademicPolicy adapter implements one of the domain contracts', function (): void {
    $contracts = [
        App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyRepositoryInterface::class,
        App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract::class,
    ];

    foreach (File::files(app_path('Domains/AcademicPolicy/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\AcademicPolicy\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

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

test('every AcademicPolicy domain contract is bound in the container', function (): void {
    $contracts = [
        App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyRepositoryInterface::class,
        App\Domains\AcademicPolicy\Domain\Contracts\AcademicPolicyQueryContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});

test('the AcademicPolicy value object is final readonly and persistence-free', function (): void {
    expect(App\Domains\AcademicPolicy\Domain\ValueObjects\PolicyValue::class)
        ->toBeFinal()
        ->toBeReadonly()
        ->not->toUse('App\Domains\AcademicPolicy\Infrastructure')
        ->not->toUse('Illuminate\Database\Eloquent');
});

test('the AcademicPolicy DTO is final readonly', function (): void {
    expect(App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData::class)
        ->toBeFinal()
        ->toBeReadonly();
});

test('actions are final classes exposing an execute method', function (): void {
    expect('App\Domains\AcademicPolicy\Application\Actions')
        ->toBeFinal()
        ->toHaveMethod('execute');
});

test('services are final classes', function (): void {
    expect('App\Domains\AcademicPolicy\Application\Services')
        ->toBeFinal();
});

test('domain events are plain classes', function (): void {
    expect('App\Domains\AcademicPolicy\Domain\Events')
        ->toBeClasses();
});

test('exceptions extend the expected base class', function (): void {
    expect('App\Domains\AcademicPolicy\Domain\Exceptions')
        ->toBeFinal()
        ->toExtend(RuntimeException::class);
});

test('the reference is a final readonly class', function (): void {
    expect(AcademicPolicyReference::class)
        ->toBeFinal()
        ->toBeReadonly();
});

test('the scope and value type enums are backed enums', function (): void {
    expect(AcademicPolicyScope::class)->toBeEnums()
        ->and(AcademicPolicyValueType::class)->toBeEnums();
});
