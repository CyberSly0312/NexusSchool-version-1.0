<?php

declare(strict_types=1);

use App\Domains\Student\Domain\Enums\StudentStatus;

arch('student domain exposes its canonical entry points')
    ->expect([
        'App\Domains\Student\StudentServiceProvider',
        'App\Domains\Student\Application\Actions\CreateStudentAction',
        'App\Domains\Student\Application\DTOs\CreateStudentData',
        'App\Domains\Student\Application\Services\StudentService',
        'App\Domains\Student\Domain\ValueObjects\StudentName',
        'App\Domains\Student\Infrastructure\Models\Student',
        'App\Domains\Student\Presentation\Filament\Resources\StudentResource',
    ])
    ->toBeClasses();

arch('the student status enum is defined in the domain layer')
    ->expect('App\Domains\Student\Domain\Enums\StudentStatus')
    ->toBeEnums();

arch('value objects are final readonly classes that never touch persistence')
    ->expect('App\Domains\Student\Domain\ValueObjects')
    ->toBeFinal()
    ->toBeReadonly()
    ->not->toUse('App\Domains\Student\Infrastructure')
    ->not->toUse('Illuminate\Database\Eloquent');

arch('DTOs are final readonly classes')
    ->expect('App\Domains\Student\Application\DTOs')
    ->toBeFinal()
    ->toBeReadonly();

arch('actions are final classes exposing an execute method')
    ->expect('App\Domains\Student\Application\Actions')
    ->toBeFinal()
    ->toHaveMethod('execute');

arch('services are facades over actions')
    ->expect('App\Domains\Student\Application\Services')
    ->toBeFinal();

arch('the student status enum defines the full lifecycle')
    ->expect(StudentStatus::cases())
    ->toHaveCount(6);

arch('no legacy student scaffold is referenced by the new domain')
    ->expect('App\Domains\Student\Application')
    ->not->toUse('App\Domains\Student\Services')
    ->and('App\Domains\Student\Domain')
    ->not->toUse('App\Domains\Student\DTOs');
