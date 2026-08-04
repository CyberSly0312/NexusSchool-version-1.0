<?php

declare(strict_types=1);

use App\Domains\Guardian\Domain\Enums\RelationshipFlag;

arch('guardian domain exposes its canonical entry points')
    ->expect([
        'App\Domains\Guardian\GuardianServiceProvider',
        'App\Domains\Guardian\Application\Actions\CreateGuardianAction',
        'App\Domains\Guardian\Application\Actions\LinkGuardianAction',
        'App\Domains\Guardian\Application\DTOs\GuardianData',
        'App\Domains\Guardian\Application\Services\GuardianService',
        'App\Domains\Guardian\Domain\ValueObjects\PhoneNumber',
        'App\Domains\Guardian\Infrastructure\Models\Guardian',
        'App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship',
        'App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource',
    ])
    ->toBeClasses();

arch('the guardian status enum is defined in the domain layer')
    ->expect('App\Domains\Guardian\Domain\Enums\GuardianStatus')
    ->toBeEnums();

arch('value objects are final readonly classes that never touch persistence')
    ->expect('App\Domains\Guardian\Domain\ValueObjects')
    ->toBeFinal()
    ->toBeReadonly()
    ->not->toUse('App\Domains\Guardian\Infrastructure')
    ->not->toUse('Illuminate\Database\Eloquent');

arch('DTOs are final readonly classes')
    ->expect('App\Domains\Guardian\Application\DTOs')
    ->toBeFinal()
    ->toBeReadonly();

arch('actions are final classes exposing an execute method')
    ->expect('App\Domains\Guardian\Application\Actions')
    ->toBeFinal()
    ->toHaveMethod('execute');

arch('services are facades over actions')
    ->expect('App\Domains\Guardian\Application\Services')
    ->toBeFinal();

arch('domain events are plain classes')
    ->expect('App\Domains\Guardian\Domain\Events')
    ->toBeClasses();

arch('the relationship flag enum mirrors the student_guardians columns')
    ->expect(RelationshipFlag::cases())
    ->toHaveCount(6);

arch('exceptions extend the expected base class')
    ->expect('App\Domains\Guardian\Domain\Exceptions')
    ->toBeFinal()
    ->toExtend(RuntimeException::class);
