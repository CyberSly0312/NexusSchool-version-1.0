<?php

declare(strict_types=1);

use App\Domains\Academic\Infrastructure\Models\AcademicCalendarYear;
use App\Domains\School\Models\School;
use App\Domains\User\Enums\UserStatus;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Routing security baseline (Phase 1A) + domain routing (Phase 2)
|--------------------------------------------------------------------------
|
| These tests exercise the HTTP routing / panel authorization behaviour.
|
| Phase 2 makes the request host authoritative for school (tenant) panels:
| tenancy is resolved from the explicit `domains` table row matching the host,
| never from the authenticated user's school. Central domains and unknown /
| unregistered hosts fail closed; inactive schools are denied; an authenticated
| user may only use the panel of the school they belong to.
|
| The platform (super-admin) panel stays central and is reachable on any host.
|
*/

uses(TestCase::class, RefreshDatabase::class);

/**
 * Create an active user carrying the given spatie role for a school.
 */
function routingUser(string $schoolId, string $role, UserStatus $status = UserStatus::Active): User
{
    $roleModel = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);

    $user = User::factory()->create([
        'school_id' => $schoolId,
        'status' => $status,
    ]);

    $user->assignRole($roleModel);

    return $user;
}

beforeEach(function (): void {
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web'])
        ->syncPermissions(Permission::all());
});

// ---------------------------------------------------------------------------
// Authentication / login separation
// ---------------------------------------------------------------------------

test('a guest is redirected from the school-admin panel to its login page', function (): void {
    $school = makeTenant();

    $this->get('http://'.tenantHost($school).'/school-admin')
        ->assertRedirect('/school-admin/login');
});

test('a guest is redirected from the central super-admin panel to its login page', function (): void {
    URL::forceRootUrl('http://localhost');

    $this->get('/super-admin')->assertRedirect('/super-admin/login');
});

// ---------------------------------------------------------------------------
// Authorised panel access and role separation
// ---------------------------------------------------------------------------

test('a school admin can reach their own panel', function (): void {
    $school = makeTenant();
    $user = routingUser($school, 'school_admin');

    $this->actingAs($user)
        ->get('/school-admin')
        ->assertSuccessful();
});

test('role to panel authorization is mapped correctly by canAccessPanel', function (): void {
    $school = makeTenant();
    $director = routingUser($school, 'director');
    $teacher = routingUser($school, 'teacher');

    // Authorization only — deliberately does not render the Director dashboard,
    // which currently references a missing widget class (see Phase 0 report).
    expect($director->canAccessPanel(Filament::getPanel('director')))->toBeTrue()
        ->and($director->canAccessPanel(Filament::getPanel('school-admin')))->toBeFalse()
        ->and($teacher->canAccessPanel(Filament::getPanel('teacher')))->toBeTrue()
        ->and($teacher->canAccessPanel(Filament::getPanel('school-admin')))->toBeFalse();
});

test('a teacher cannot reach the school-admin panel', function (): void {
    $school = makeTenant();
    $teacher = routingUser($school, 'teacher');

    $this->actingAs($teacher)
        ->get('/school-admin')
        ->assertForbidden();
});

test('a director cannot reach the school-admin panel', function (): void {
    $school = makeTenant();
    $director = routingUser($school, 'director');

    $this->actingAs($director)
        ->get('/school-admin')
        ->assertForbidden();
});

test('a school admin cannot reach the teacher panel', function (): void {
    $school = makeTenant();
    $admin = routingUser($school, 'school_admin');

    $this->actingAs($admin)
        ->get('/teacher')
        ->assertForbidden();
});

test('a super admin can reach the central panel', function (): void {
    $school = makeTenant();
    $superAdmin = routingUser($school, 'super_admin');

    $this->actingAs($superAdmin)
        ->get('/super-admin')
        ->assertSuccessful();
});

// ---------------------------------------------------------------------------
// Login / session behaviour across central and school panels
// ---------------------------------------------------------------------------

test('an authenticated school user is denied (not redirected) on the central panel', function (): void {
    $school = makeTenant();
    $admin = routingUser($school, 'school_admin');

    // A 403 (rather than a redirect to /super-admin/login) proves the same
    // session cookie is recognised by both panels; only panel access is gated.
    $this->actingAs($admin)
        ->get('/super-admin')
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Cross-tenant data exposure (IDOR) — enforced today
// ---------------------------------------------------------------------------

test('a school admin can edit a user belonging to their own school', function (): void {
    $schoolA = makeTenant('School A');
    $adminA = routingUser($schoolA, 'school_admin');
    $target = User::factory()->create(['school_id' => $schoolA]);

    $this->actingAs($adminA)
        ->get('/school-admin/users/'.$target->getRouteKey().'/edit')
        ->assertSuccessful();
});

test('a school admin cannot edit a user from another school through a direct URL', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');
    $adminA = routingUser($schoolA, 'school_admin');
    $targetB = User::factory()->create(['school_id' => $schoolB]);

    useTenantHost($schoolA);

    $this->actingAs($adminA)
        ->get('/school-admin/users/'.$targetB->getRouteKey().'/edit')
        ->assertNotFound();
});

test('the UserResource query is scoped to the initialised tenant', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    User::factory()->create(['school_id' => $schoolA]);
    User::factory()->create(['school_id' => $schoolB]);

    Filament::setServingStatus();
    Filament::setCurrentPanel('school-admin');
    Tenancy::initialize(School::find($schoolA));

    $foreign = UserResource::getEloquentQuery()
        ->where('school_id', '!=', $schoolA)
        ->count();

    expect($foreign)->toBe(0);

    Tenancy::end();
});

// ---------------------------------------------------------------------------
// Domain resolution — the host is authoritative for school panels
// ---------------------------------------------------------------------------

test('an unknown, unregistered host fails closed on a school panel', function (): void {
    $school = makeTenant();
    $admin = routingUser($school, 'school_admin');

    // The host does not match any `domains` row, so no tenant is resolved.
    $this->actingAs($admin)
        ->get('http://unknown.school.test/school-admin')
        ->assertNotFound();
});

test('a central host never serves a school panel', function (): void {
    $school = makeTenant();
    $admin = routingUser($school, 'school_admin');

    $this->actingAs($admin)
        ->get('http://localhost/school-admin')
        ->assertNotFound();
});

test('a school admin cannot use another school host (host is authoritative)', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');
    $adminA = routingUser($schoolA, 'school_admin');

    useTenantHost($schoolB);

    $this->actingAs($adminA)
        ->get('/school-admin')
        ->assertForbidden();
});

test('a guest can reach the login screen on a registered school host', function (): void {
    $school = makeTenant();

    $this->get('http://'.tenantHost($school).'/school-admin/login')
        ->assertSuccessful();
});

test('a guest cannot reach a school login on an unregistered host', function (): void {
    $this->get('http://unregistered.school.test/school-admin/login')
        ->assertNotFound();
});

// ---------------------------------------------------------------------------
// Enforced security properties (Phase 1A)
// ---------------------------------------------------------------------------

test('a super admin is denied access to school panels (platform separation)', function (): void {
    $school = makeTenant();
    $superAdmin = routingUser($school, 'super_admin');

    // The platform administrator is a central user: only the super-admin panel
    // is reachable, and school panels are never granted.
    $this->actingAs($superAdmin)
        ->get('/school-admin')
        ->assertForbidden();
});

test('a user whose school no longer exists is denied panel access', function (): void {
    // A perfectly valid school host, but the authenticated user has no school.
    $school = makeTenant();
    $user = routingUser('routing-ghost-school', 'school_admin');

    $this->actingAs($user)
        ->get('/school-admin/users')
        ->assertForbidden();
});

test('a deleted school blocks its users from panel access', function (): void {
    $school = makeTenant();
    $admin = routingUser($school, 'school_admin');

    School::find($school)->delete();

    // The user's school is gone, so the authenticated gate denies access
    // before the panel (and therefore domain resolution) is reached.
    $this->actingAs($admin)
        ->get('/school-admin/users')
        ->assertForbidden();
});

test('a deleted school domain no longer resolves to a panel', function (): void {
    $school = makeTenant();
    $host = 'http://'.tenantHost($school);

    School::find($school)->delete();

    // The registered domain still exists, but its soft-deleted tenant is
    // excluded from resolution, so the host fails closed even for a guest.
    $this->get($host.'/school-admin/login')
        ->assertNotFound();
});

test('a suspended school blocks its users from panel access', function (): void {
    $school = makeTenant('Suspended School', 'suspended');
    $admin = routingUser($school, 'school_admin');

    $this->actingAs($admin)
        ->get('/school-admin/users')
        ->assertForbidden();
});

test('a pending school blocks its users from panel access', function (): void {
    $school = makeTenant('Pending School', 'pending');
    $admin = routingUser($school, 'school_admin');

    $this->actingAs($admin)
        ->get('/school-admin/users')
        ->assertForbidden();
});

test('tenant-scoped resource queries do not leak data when no tenant is initialised', function (): void {
    $schoolA = makeTenant('School A');
    $schoolB = makeTenant('School B');

    AcademicCalendarYear::create([
        'school_id' => $schoolA,
        'name' => '2026-2027 A',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]);
    AcademicCalendarYear::create([
        'school_id' => $schoolB,
        'name' => '2026-2027 B',
        'start_date' => '2026-09-01',
        'end_date' => '2027-07-31',
    ]);

    expect(tenant())->toBeNull();

    $visible = \App\Domains\Academic\Presentation\Filament\Resources\AcademicCalendarYearResource::getEloquentQuery()->count();

    // ScopedByTenant fails closed: an uninitialised tenant yields zero rows
    // rather than exposing every school's records.
    expect($visible)->toBe(0);
});

// ---------------------------------------------------------------------------
// Role / panel authorization matrix
// ---------------------------------------------------------------------------

test('role to panel authorization matrix is enforced', function (): void {
    $school = makeTenant();

    $allowed = [
        'super_admin' => ['super-admin'],
        'school_admin' => ['school-admin'],
        'director' => ['director'],
        'teacher' => ['teacher'],
        'student' => ['student'],
        'parent' => ['parent'],
        'accountant' => ['accountant'],
        'secretary' => ['secretary'],
    ];

    $panels = ['super-admin', 'school-admin', 'director', 'teacher', 'student', 'parent', 'accountant', 'secretary'];

    foreach ($allowed as $role => $permittedPanels) {
        $user = routingUser($school, $role);

        foreach ($panels as $panelId) {
            expect($user->canAccessPanel(Filament::getPanel($panelId)))
                ->toBe(in_array($panelId, $permittedPanels, true), "role {$role} / panel {$panelId}");
        }
    }
});

test('school panel access requires an active school', function (string $status): void {
    $school = makeTenant('Status School', $status);
    $admin = routingUser($school, 'school_admin');

    expect($admin->canAccessPanel(Filament::getPanel('school-admin')))->toBeFalse();
})->with(['pending', 'inactive', 'suspended']);
