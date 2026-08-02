<?php

declare(strict_types=1);

use App\Domains\Academic\Application\DTOs\ClassSectionData;
use App\Domains\Academic\Application\DTOs\EducationLevelData;
use App\Domains\Academic\Application\DTOs\ProgramData;
use App\Domains\Academic\Application\DTOs\SchoolClassData;
use App\Domains\Academic\Application\DTOs\StreamData;
use App\Domains\Academic\Application\Services\AcademicStructureService;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Academic\Infrastructure\Models\Stream;

beforeEach(function (): void {
    $this->schoolId = makeTenant();
    $this->structure = app(AcademicStructureService::class);

    $this->level = $this->structure->createLevel(EducationLevelData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Secondaire',
        'code' => 'SEC',
    ]));
    $this->program = $this->structure->createProgram(ProgramData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Premier cycle',
        'code' => 'PRE',
        'education_level_id' => $this->level->id,
    ]));
});

test('creates an education level and a program', function (): void {
    expect($this->level)
        ->toBeInstanceOf(EducationLevel::class)
        ->and($this->program)
        ->toBeInstanceOf(AcademicProgram::class)
        ->and($this->program->educationLevel->is($this->level))->toBeTrue();
});

test('creates a stream', function (): void {
    $stream = $this->structure->createStream(StreamData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Scientifique',
        'code' => 'SCI',
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
    ]));

    expect($stream)
        ->toBeInstanceOf(Stream::class)
        ->and($stream->program->is($this->program))->toBeTrue();
});

test('rejects a duplicate stream inside the same program', function (): void {
    $this->structure->createStream(StreamData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Scientifique',
        'code' => 'SCI',
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
    ]));

    $this->structure->createStream(StreamData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Scientifique',
        'code' => 'SCI2',
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
    ]));
})->throws(DuplicateResourceException::class);

test('creates a class', function (): void {
    $stream = $this->structure->createStream(StreamData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'Scientifique',
        'code' => 'SCI',
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
    ]));

    $class = $this->structure->createClass(SchoolClassData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '6ème',
        'code' => '6E',
        'academic_program_id' => $this->program->id,
        'education_level_id' => $this->level->id,
        'stream_id' => $stream->id,
        'capacity' => 40,
    ]));

    expect($class)
        ->toBeInstanceOf(SchoolClass::class)
        ->and($class->stream->is($stream))->toBeTrue();
});

test('rejects a duplicate class name within the same program', function (): void {
    $this->structure->createClass(SchoolClassData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '6ème',
        'code' => '6E',
        'academic_program_id' => $this->program->id,
    ]));

    $this->structure->createClass(SchoolClassData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '6ème',
        'code' => '6E2',
        'academic_program_id' => $this->program->id,
    ]));
})->throws(DuplicateResourceException::class);

test('creates a class section', function (): void {
    $class = $this->structure->createClass(SchoolClassData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '6ème',
        'code' => '6E',
        'academic_program_id' => $this->program->id,
    ]));

    $section = $this->structure->createSection(ClassSectionData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'A',
        'school_class_id' => $class->id,
    ]));

    expect($section)
        ->toBeInstanceOf(ClassSection::class)
        ->and($section->schoolClass->is($class))->toBeTrue();
});

test('rejects a duplicate section name in the same class', function (): void {
    $class = $this->structure->createClass(SchoolClassData::fromArray([
        'school_id' => $this->schoolId,
        'name' => '6ème',
        'code' => '6E',
        'academic_program_id' => $this->program->id,
    ]));

    $this->structure->createSection(ClassSectionData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'A',
        'school_class_id' => $class->id,
    ]));

    $this->structure->createSection(ClassSectionData::fromArray([
        'school_id' => $this->schoolId,
        'name' => 'A',
        'school_class_id' => $class->id,
    ]));
})->throws(DuplicateResourceException::class);
