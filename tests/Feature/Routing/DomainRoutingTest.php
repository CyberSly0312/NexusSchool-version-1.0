<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Domain-based tenant routing (Phase 2)
|--------------------------------------------------------------------------
|
| The `domains` table is the canonical hostname registry. A school panel
| request must resolve its tenant from the registered domain matching the
| request host. These tests assert the resolution mechanism itself; the
| fail-closed and authorization behaviour lives in SecurityBaselineTest.
|
*/

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web'])
        ->syncPermissions(Permission::all());
});

function domainRoutingUser(string $schoolId): User
{
    $role = Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);

    $user = User::factory()->create([
        'school_id' => $schoolId,
        'status' => \App\Domains\User\Enums\UserStatus::Active,
    ]);

    $user->assignRole($role);

    return $user;
}

test('a registered host resolves its own tenant', function (): void {
    $school = makeTenant('Resolved School');

    $this->get('http://'.tenantHost($school).'/school-admin/login')
        ->assertSuccessful();

    expect(tenant()?->getKey())->toBe($school);
});

test('the request host selects the tenant, not the creation order', function (): void {
    makeTenant('First School');
    $schoolB = makeTenant('Second School');

    useTenantHost($schoolB);

    $this->get('http://'.tenantHost($schoolB).'/school-admin/login')
        ->assertSuccessful();

    expect(tenant()?->getKey())->toBe($schoolB);
});

test('a school can be served from more than one registered domain', function (): void {
    $school = makeTenant('Multi Domain School');

    DB::table('domains')->insert([
        'domain' => 'multi.example.test',
        'tenant_id' => $school,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get('http://multi.example.test/school-admin/login')
        ->assertSuccessful();

    expect(tenant()?->getKey())->toBe($school);
});

test('a domain can only be registered to a single school', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    expect(fn () => DB::table('domains')->insert([
        'domain' => tenantHost($schoolA),
        'tenant_id' => $schoolB,
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(QueryException::class);
});

test('central domains include the local development hosts', function (): void {
    expect(config('tenancy.central_domains'))
        ->toContain('localhost')
        ->toContain('127.0.0.1')
        ->toContain('nexusschool.test')
        ->toContain('www.nexusschool.test')
        ->toContain('nexusschool.com');
});

test('school panel login and logout paths are unchanged', function (): void {
    foreach (['school-admin', 'director', 'teacher', 'student', 'parent', 'accountant', 'secretary'] as $panel) {
        expect(Route::has("filament.{$panel}.auth.login"))->toBeTrue()
            ->and(Route::has("filament.{$panel}.auth.logout"))->toBeTrue();
    }
});

test('a school user can log out on their tenant host', function (): void {
    $school = makeTenant('Logout School');
    $admin = domainRoutingUser($school);

    $this->actingAs($admin)
        ->post('/school-admin/logout')
        ->assertRedirect();

    $this->assertGuest();
});
