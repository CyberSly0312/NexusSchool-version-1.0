<?php

declare(strict_types=1);

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Models\School;
use App\Domains\School\Services\SchoolService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('tenancy.tenant_base_domain', 'nexusschool.test');
});

test('school provisioning atomically creates the canonical domain registry entry', function (): void {
    $school = app(SchoolService::class)->create(SchoolData::fromArray([
        'name' => 'Zinda Academy',
        'slug' => 'Zinda Academy',
        'status' => SchoolStatus::Active->value,
    ]));

    expect($school->slug)->toBe('zinda-academy')
        ->and($school->domain)->toBe('zinda-academy.nexusschool.test')
        ->and($school->domains()->pluck('domain')->all())->toBe(['zinda-academy.nexusschool.test']);
});

test('a reserved or unavailable slug is rejected without creating a school', function (): void {
    $service = app(SchoolService::class);

    expect(fn () => $service->create(SchoolData::fromArray(['name' => 'Admin', 'slug' => 'admin'])))
        ->toThrow(ValidationException::class);

    $service->create(SchoolData::fromArray(['name' => 'Zinda Academy', 'slug' => 'zinda']));

    expect(fn () => $service->create(SchoolData::fromArray(['name' => 'Another Zinda', 'slug' => 'zinda'])))
        ->toThrow(ValidationException::class)
        ->and(School::withTrashed()->count())->toBe(1)
        ->and(DB::table('domains')->count())->toBe(1);
});

test('renaming a school does not alter or reassign its canonical domain', function (): void {
    $service = app(SchoolService::class);
    $school = $service->create(SchoolData::fromArray(['name' => 'Zinda Academy', 'slug' => 'zinda']));

    $updated = $service->update($school, ['name' => 'Zinda International Academy', 'domain' => 'other.nexusschool.test']);

    expect($updated->domain)->toBe('zinda.nexusschool.test')
        ->and($updated->domains()->pluck('domain')->all())->toBe(['zinda.nexusschool.test']);
});
