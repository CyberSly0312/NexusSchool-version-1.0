<?php

declare(strict_types=1);

use App\Domains\School\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| School Access Portal — tenant-aware root routing
|--------------------------------------------------------------------------
|
| The root URL (`/`) on a registered school domain resolves to the School
| Access Portal. The portal is a navigation / access-entry page only: the
| profile cards link to the existing Filament panel login routes, where the
| existing `User::canAccessPanel()` authorization stays authoritative.
|
*/

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web'])
        ->syncPermissions(Permission::all());
});

test('a registered school domain resolves the root url to the school access portal', function (): void {
    $schoolId = makeTenant('Portal School');

    $this->get('/')->assertSuccessful();
});

test('the school access portal displays the current school name', function (): void {
    $schoolId = makeTenant('Portal School');

    $this->get('/')->assertSee('Portal School');
});

test('the school access portal displays the school logo when available', function (): void {
    $schoolId = makeTenant('Logo School');
    School::findOrFail($schoolId)->update(['logo_url' => 'https://nexusschool.test/logo.png']);

    $this->get('/')->assertSee('https://nexusschool.test/logo.png');
});

test('the school access portal falls back when the school has no logo', function (): void {
    $schoolId = makeTenant('Alpha School');

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('portal-logo-fallback');
});

test('the central domain does not resolve to the tenant portal', function (): void {
    $this->get('http://localhost/')->assertNotFound();

    expect(tenant())->toBeNull();
});

test('an unknown domain remains rejected for the school access portal', function (): void {
    $this->get('http://unknown.school.test/')->assertNotFound();
});

test('one school portal never displays another school identity', function (): void {
    makeTenant('Beta School');
    $secondSchoolId = makeTenant('Alpha School');
    useTenantHost($secondSchoolId);

    // The portal renders the resolved school identity only.
    $this->get('/')->assertSee('Alpha School');
    $this->get('/')->assertDontSee('Beta School');
});

test('the school access portal presents all seven profiles linking to their filament login routes', function (): void {
    $schoolId = makeTenant('Portal School');

    $response = $this->get('/');

    foreach (['school-admin', 'director', 'secretary', 'accountant', 'teacher', 'student', 'parent'] as $panel) {
        expect($response->getContent())->toContain(route("filament.{$panel}.auth.login"));
    }

    foreach (['School Admin', 'Director', 'Secretary', 'Accountant', 'Teacher', 'Student', 'Parent'] as $label) {
        expect($response->getContent())->toContain($label);
    }
});

test('the school access portal does not introduce dead login or dashboard links', function (): void {
    $response = $this->get('/');

    expect($response->getContent())->not->toContain('/dashboard');
});

test('a teacher cannot access the school admin panel', function (): void {
    $schoolId = makeTenant('Portal School');

    $teacher = User::factory()->create(['school_id' => $schoolId]);
    $teacher->assignRole(Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']));

    $this->actingAs($teacher)
        ->get('/school-admin')
        ->assertForbidden();
});

test('a student cannot access the director panel', function (): void {
    $schoolId = makeTenant('Portal School');

    $student = User::factory()->create(['school_id' => $schoolId]);
    $student->assignRole(Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']));

    $this->actingAs($student)
        ->get('/director')
        ->assertForbidden();
});

test('a super admin cannot access a tenant panel through a school domain', function (): void {
    $school = makeTenant('Super Admin Host Isolation School');

    $superAdmin = User::factory()->create(['school_id' => $school]);
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']));

    $this->actingAs($superAdmin)
        ->get('http://'.tenantHost($school).'/school-admin')
        ->assertForbidden();
});
