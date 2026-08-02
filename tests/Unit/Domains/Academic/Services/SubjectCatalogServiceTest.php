<?php

declare(strict_types=1);

use App\Domains\Academic\Application\DTOs\CoefficientData;
use App\Domains\Academic\Application\DTOs\SubjectData;
use App\Domains\Academic\Application\DTOs\SubjectGroupData;
use App\Domains\Academic\Application\Services\SubjectCatalogService;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\Coefficient;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->catalog = app(SubjectCatalogService::class);

    $this->program = \App\Domains\Academic\Infrastructure\Models\AcademicProgram::create([
        'school_id' => $this->schoolId,
        'name' => 'Premier cycle',
        'code' => 'PRE',
    ]);

    $this->schoolClass = \App\Domains\Academic\Infrastructure\Models\SchoolClass::create([
        'school_id' => $this->schoolId,
        'academic_program_id' => $this->program->id,
        'name' => '6ème',
        'code' => '6E',
    ]);
});

test('creates a subject group', function (): void {
    $group = $this->catalog->createGroup(SubjectGroupData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Sciences',
        'code' => 'SCI',
    ]));

    expect($group->name)->toBe('Sciences');
});

test('creates a subject', function (): void {
    $subject = $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Mathématiques',
        'code' => 'MAT',
    ]));

    expect($subject->code)->toBe('MAT')
        ->and($subject->school_id)->toBe($this->schoolId);
});

test('rejects a duplicate subject code', function (): void {
    $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Mathématiques',
        'code' => 'MAT',
    ]));

    $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Maths avancées',
        'code' => 'MAT',
    ]));
})->throws(DuplicateResourceException::class);

test('rejects a duplicate subject name', function (): void {
    $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Mathématiques',
        'code' => 'MAT',
    ]));

    $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Mathématiques',
        'code' => 'MATH',
    ]));
})->throws(DuplicateResourceException::class);

test('allows the same subject code in different schools', function (): void {
    $otherSchoolId = makeTenant();

    $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Mathématiques',
        'code' => 'MAT',
    ]));
    $subject = $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $otherSchoolId,
        'name' => 'Mathématiques',
        'code' => 'MAT',
    ]));

    expect($subject->school_id)->toBe($otherSchoolId);
});

test('assigns a coefficient to a class and subject', function (): void {
    $subject = $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Mathématiques',
        'code' => 'MAT',
    ]));

    $coefficient = $this->catalog->assignCoefficient(CoefficientData::fromArray([
        'school_id' => $this->schoolId,
        'school_class_id' => $this->schoolClass->id,
        'academic_subject_id' => $subject->id,
        'coefficient' => 5,
    ]));

    expect($coefficient)
        ->toBeInstanceOf(Coefficient::class)
        ->and($coefficient->value())->toBe(5.0)
        ->and($coefficient->is_optional)->toBeFalse();
});

test('rejects a duplicate class-subject coefficient', function (): void {
    $subject = $this->catalog->createSubject(SubjectData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Mathématiques',
        'code' => 'MAT',
    ]));

    $this->catalog->assignCoefficient(CoefficientData::fromArray([
        'school_id' => $this->schoolId,
        'school_class_id' => $this->schoolClass->id,
        'academic_subject_id' => $subject->id,
        'coefficient' => 5,
    ]));

    $this->catalog->assignCoefficient(CoefficientData::fromArray([
        'school_id' => $this->schoolId,
        'school_class_id' => $this->schoolClass->id,
        'academic_subject_id' => $subject->id,
        'coefficient' => 3,
    ]));
})->throws(DuplicateResourceException::class);
