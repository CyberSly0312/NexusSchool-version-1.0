<?php

declare(strict_types=1);

use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;

arch('enrollment domain exposes its canonical entry points')
    ->expect([
        'App\Domains\Enrollment\EnrollmentServiceProvider',
        'App\Domains\Enrollment\Application\Actions\CreateEnrollmentAction',
        'App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData',
        'App\Domains\Enrollment\Application\Services\EnrollmentService',
        'App\Domains\Enrollment\Domain\ValueObjects\EnrollmentNumber',
        'App\Domains\Enrollment\Infrastructure\Models\Enrollment',
        'App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource',
    ])
    ->toBeClasses();

arch('the enrollment status enum is defined in the domain layer')
    ->expect('App\Domains\Enrollment\Domain\Enums\EnrollmentStatus')
    ->toBeEnums();

arch('value objects are final readonly classes that never touch persistence')
    ->expect('App\Domains\Enrollment\Domain\ValueObjects')
    ->toBeFinal()
    ->toBeReadonly()
    ->not->toUse('App\Domains\Enrollment\Infrastructure')
    ->not->toUse('Illuminate\Database\Eloquent');

arch('DTOs are final readonly classes')
    ->expect('App\Domains\Enrollment\Application\DTOs')
    ->toBeFinal()
    ->toBeReadonly();

arch('actions are final classes exposing an execute method')
    ->expect('App\Domains\Enrollment\Application\Actions')
    ->toBeFinal()
    ->toHaveMethod('execute');

arch('services are facades over actions')
    ->expect('App\Domains\Enrollment\Application\Services')
    ->toBeFinal();

arch('domain events are plain classes')
    ->expect('App\Domains\Enrollment\Domain\Events')
    ->toBeClasses();

arch('the enrollment status enum defines the full lifecycle')
    ->expect(EnrollmentStatus::cases())
    ->toHaveCount(5);

arch('exceptions extend the expected base class')
    ->expect('App\Domains\Enrollment\Domain\Exceptions')
    ->toBeFinal()
    ->toExtend(RuntimeException::class);
