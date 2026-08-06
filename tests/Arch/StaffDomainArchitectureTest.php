<?php

declare(strict_types=1);

use App\Domains\Staff\Domain\Enums\StaffSpecialization;

arch('staff domain exposes its canonical entry points')
    ->expect([
        'App\Domains\Staff\StaffServiceProvider',
        'App\Domains\Staff\Application\Actions\CreateStaffAction',
        'App\Domains\Staff\Application\Actions\StartEmploymentAction',
        'App\Domains\Staff\Application\Actions\TransferStaffAction',
        'App\Domains\Staff\Application\DTOs\CreateStaffData',
        'App\Domains\Staff\Application\DTOs\EmploymentData',
        'App\Domains\Staff\Application\Services\StaffService',
        'App\Domains\Staff\Domain\ValueObjects\StaffNumber',
        'App\Domains\Staff\Infrastructure\Models\Staff',
        'App\Domains\Staff\Infrastructure\Models\Employment',
        'App\Domains\Staff\Presentation\Filament\Resources\StaffResource',
    ])
    ->toBeClasses();

arch('the staff status enum is defined in the domain layer')
    ->expect('App\Domains\Staff\Domain\Enums\StaffStatus')
    ->toBeEnums();

arch('value objects are final readonly classes that never touch persistence')
    ->expect('App\Domains\Staff\Domain\ValueObjects')
    ->toBeFinal()
    ->toBeReadonly()
    ->not->toUse('App\Domains\Staff\Infrastructure')
    ->not->toUse('Illuminate\Database\Eloquent');

arch('DTOs are final readonly classes')
    ->expect('App\Domains\Staff\Application\DTOs')
    ->toBeFinal()
    ->toBeReadonly();

arch('actions are final classes exposing an execute method')
    ->expect('App\Domains\Staff\Application\Actions')
    ->toBeFinal()
    ->toHaveMethod('execute');

arch('services are facades over actions')
    ->expect('App\Domains\Staff\Application\Services')
    ->toBeFinal();

arch('domain events are plain classes')
    ->expect('App\Domains\Staff\Domain\Events')
    ->toBeClasses();

arch('the staff specialization enum covers the canonical workforce roles')
    ->expect(StaffSpecialization::cases())
    ->toHaveCount(8);

arch('exceptions extend the expected base class')
    ->expect('App\Domains\Staff\Domain\Exceptions')
    ->toBeFinal()
    ->toExtend(RuntimeException::class);
