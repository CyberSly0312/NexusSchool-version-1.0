<?php

declare(strict_types=1);

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Models\School;
use App\Domains\School\Models\SchoolAdminSetupToken;
use App\Domains\School\Services\SchoolAdminProvisioningService;
use App\Domains\School\Services\SchoolProvisioningService;
use App\Domains\School\Services\SchoolService;
use App\Domains\System\Enums\AuditAction;
use App\Domains\User\Enums\UserStatus;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| School Admin provisioning and recovery
|--------------------------------------------------------------------------
|
| Creating a school must provision its initial School Admin, and an existing
| school must be recoverable without recreating it. These tests lock down the
| security invariants: tenant association, intended role/permissions, one-time
| hashed setup links, no duplicate administrators, no plaintext passwords, no
| password change before redemption, and actual Filament authentication.
|
*/

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('tenancy.tenant_base_domain', 'nexusschool.test');
    $this->seed(\Database\Seeders\PermissionSeeder::class);
});

/**
 * Extract the plaintext token from a generated one-time setup URL.
 */
function schoolAdminTokenFromUrl(string $url): string
{
    return rawurldecode(basename((string) parse_url($url, PHP_URL_PATH)));
}

/**
 * Create a school with its initial administrator through the provisioning
 * service and return the result together with the plaintext token.
 *
 * @return array{school: School, admin: User, token: string, result: \App\Domains\School\DTOs\SchoolAdminProvisioningResult}
 */
function provisionedSchoolAdmin(
    string $name = 'Aurore Academy',
    string $slug = 'aurore',
    string $adminName = 'Awa Traore',
    string $adminEmail = 'awa@aurore.test',
    SchoolStatus $status = SchoolStatus::Active,
): array {
    $result = app(SchoolProvisioningService::class)->create(
        SchoolData::fromArray([
            'name' => $name,
            'slug' => $slug,
            'status' => $status->value,
        ]),
        $adminName,
        $adminEmail,
    );

    return [
        'school' => $result->school,
        'admin' => $result->administrator->user,
        'token' => schoolAdminTokenFromUrl((string) $result->administrator->setupUrl),
        'result' => $result->administrator,
    ];
}

// ---------------------------------------------------------------------------
// Automatic provisioning and tenant association
// ---------------------------------------------------------------------------

test('creating a school provisions a pending school admin with a one-time setup link', function (): void {
    $result = app(SchoolProvisioningService::class)->create(
        SchoolData::fromArray(['name' => 'Aurore Academy', 'slug' => 'aurore']),
        'Awa Traore',
        'Awa@Aurore.test',
    );

    $admin = $result->administrator->user;
    $host = tenantHost($result->school->getKey());

    expect($result->administrator->created)->toBeTrue()
        ->and($result->administrator->setupUrl)->toStartWith("http://{$host}/school-admin/setup/")
        ->and($result->administrator->expiresAt)->not->toBeNull()
        ->and($admin->school_id)->toBe($result->school->getKey())
        ->and($admin->email)->toBe('awa@aurore.test')
        ->and($admin->status)->toBe(UserStatus::Pending)
        ->and($admin->hasRole('school_admin'))->toBeTrue()
        ->and($admin->password)->not->toBe('password')
        ->and(Hash::check('password', $admin->password))->toBeFalse()
        ->and(SchoolAdminSetupToken::query()->count())->toBe(1)
        ->and(SchoolAdminSetupToken::query()->first()->token_hash)->toBe(hash('sha256', schoolAdminTokenFromUrl((string) $result->administrator->setupUrl)))
        ->and(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(1);
});

test('the provisioned administrator is associated with its own school only', function (): void {
    $first = provisionedSchoolAdmin('First Academy', 'first-academy', 'First Admin', 'first@example.test');
    $second = provisionedSchoolAdmin('Second Academy', 'second-academy', 'Second Admin', 'second@example.test');

    expect($first['admin']->school_id)->toBe($first['school']->getKey())
        ->and($second['admin']->school_id)->toBe($second['school']->getKey())
        ->and($first['admin']->school_id)->not->toBe($second['admin']->school_id);
});

test('the school admin receives school-level permissions but no platform-only permissions', function (): void {
    ['admin' => $admin] = provisionedSchoolAdmin();

    expect($admin->can('user.create'))->toBeTrue()
        ->and($admin->can('school.view'))->toBeTrue()
        ->and($admin->can('school.update'))->toBeTrue()
        ->and($admin->can('student.create'))->toBeTrue()
        ->and($admin->can('school.create'))->toBeFalse()
        ->and($admin->can('school.delete'))->toBeFalse()
        ->and($admin->can('permission.create'))->toBeFalse()
        ->and($admin->can('permission.update'))->toBeFalse()
        ->and($admin->can('permission.delete'))->toBeFalse()
        ->and($admin->can('audit_log.create'))->toBeFalse()
        ->and($admin->can('audit_log.update'))->toBeFalse()
        ->and($admin->can('audit_log.delete'))->toBeFalse()
        ->and($admin->can('api.view'))->toBeFalse()
        ->and($admin->can('backup.create'))->toBeFalse()
        ->and($admin->can('maintenance_window.view'))->toBeFalse();
});

test('permission granting is additive and preserves deliberate extra grants', function (): void {
    ['admin' => $admin] = provisionedSchoolAdmin();

    $role = Role::findByName('school_admin', 'web');
    $role->givePermissionTo('api.view');

    expect($role->hasPermissionTo('api.view'))->toBeTrue()
        ->and($role->hasPermissionTo('user.create'))->toBeTrue();

    // A later provisioning pass must not revoke the operator's extra grant.
    app(SchoolAdminProvisioningService::class)->provision(
        School::findOrFail($admin->school_id),
        'Another Admin',
        'another@example.test',
    );

    expect($role->fresh()->hasPermissionTo('api.view'))->toBeTrue();
});

// ---------------------------------------------------------------------------
// One-time setup link: page, validation and redemption
// ---------------------------------------------------------------------------

test('the setup page is reachable on the school domain and never displays a password', function (): void {
    ['school' => $school, 'admin' => $admin, 'token' => $token] = provisionedSchoolAdmin();

    $response = $this->get('http://'.tenantHost($school->getKey())."/school-admin/setup/{$token}");

    $response->assertOk()
        ->assertSee($admin->email)
        ->assertSee('autocomplete="new-password"', false)
        ->assertDontSee('name="current_password"', false)
        ->assertDontSee($admin->password);
});

test('a school admin can choose their own password through the one-time setup link', function (): void {
    ['school' => $school, 'admin' => $admin, 'token' => $token] = provisionedSchoolAdmin();
    $host = tenantHost($school->getKey());
    $password = 'Str0ng-Passw0rd!2026';

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertRedirect("http://{$host}/school-admin/setup/done");

    $admin->refresh();

    expect($admin->status)->toBe(UserStatus::Active)
        ->and(Hash::check($password, $admin->password))->toBeTrue()
        ->and($admin->email_verified_at)->not->toBeNull()
        ->and(AuditLog::query()->where('action', AuditAction::PasswordSet->value)->count())->toBe(1);

    $this->get("http://{$host}/school-admin/setup/done")->assertOk();
});

test('the setup link is single-use', function (): void {
    ['school' => $school, 'token' => $token] = provisionedSchoolAdmin();
    $host = tenantHost($school->getKey());
    $password = 'Str0ng-Passw0rd!2026';

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertRedirect();

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => 'Another-Passw0rd!2026',
        'password_confirmation' => 'Another-Passw0rd!2026',
    ])->assertNotFound();
});

test('an expired setup link is rejected', function (): void {
    ['school' => $school, 'admin' => $admin, 'token' => $token] = provisionedSchoolAdmin();
    $host = tenantHost($school->getKey());

    SchoolAdminSetupToken::query()->update(['expires_at' => now()->subMinute()]);

    $this->get("http://{$host}/school-admin/setup/{$token}")->assertNotFound();
    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => 'Str0ng-Passw0rd!2026',
        'password_confirmation' => 'Str0ng-Passw0rd!2026',
    ])->assertNotFound();

    expect($admin->fresh()->status)->toBe(UserStatus::Pending);
});

test('a weak or unconfirmed password is rejected without consuming the link', function (): void {
    ['school' => $school, 'admin' => $admin, 'token' => $token] = provisionedSchoolAdmin();
    $host = tenantHost($school->getKey());

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => 'Str0ng-Passw0rd!2026',
        'password_confirmation' => 'Different-Passw0rd!2026',
    ])->assertSessionHasErrors('password');

    expect($admin->fresh()->status)->toBe(UserStatus::Pending)
        ->and(SchoolAdminSetupToken::query()->whereNull('used_at')->count())->toBe(1);
});

test('a setup token is rejected on another school domain', function (): void {
    ['token' => $token] = provisionedSchoolAdmin('First Academy', 'first-academy', 'First Admin', 'first@example.test');

    $otherSchool = app(SchoolService::class)->create(SchoolData::fromArray([
        'name' => 'Other Academy',
        'slug' => 'other-academy',
    ]));

    $otherHost = tenantHost($otherSchool->getKey());

    $this->get("http://{$otherHost}/school-admin/setup/{$token}")->assertNotFound();
    $this->post("http://{$otherHost}/school-admin/setup/{$token}", [
        'password' => 'Str0ng-Passw0rd!2026',
        'password_confirmation' => 'Str0ng-Passw0rd!2026',
    ])->assertNotFound();
});

test('the setup route is denied for inactive and suspended schools', function (SchoolStatus $status): void {
    ['school' => $school, 'token' => $token] = provisionedSchoolAdmin();

    $school->update(['status' => $status]);

    $this->get('http://'.tenantHost($school->getKey())."/school-admin/setup/{$token}")
        ->assertForbidden();
})->with([
    'inactive' => [SchoolStatus::Inactive],
    'suspended' => [SchoolStatus::Suspended],
]);

test('the setup route is not served on a central domain', function (): void {
    ['token' => $token] = provisionedSchoolAdmin();

    $this->get("http://localhost/school-admin/setup/{$token}")->assertNotFound();
});

// ---------------------------------------------------------------------------
// Actual panel authentication
// ---------------------------------------------------------------------------

test('a provisioned school admin can authenticate to the school-admin panel after setup', function (): void {
    ['school' => $school, 'admin' => $admin, 'token' => $token] = provisionedSchoolAdmin();
    $host = tenantHost($school->getKey());
    $password = 'Str0ng-Passw0rd!2026';

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertRedirect();

    Filament::setServingStatus();
    Filament::setCurrentPanel('school-admin');
    Tenancy::initialize($school->fresh());

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $admin->email,
            'password' => $password,
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    $this->assertAuthenticatedAs($admin->fresh());
});

test('a pending school admin cannot authenticate before redeeming the setup link', function (): void {
    ['school' => $school, 'admin' => $admin] = provisionedSchoolAdmin();

    Filament::setServingStatus();
    Filament::setCurrentPanel('school-admin');
    Tenancy::initialize($school);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $admin->email,
            'password' => 'Str0ng-Passw0rd!2026',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

test('a school admin of another school cannot authenticate to this school panel', function (): void {
    ['school' => $firstSchool, 'admin' => $firstAdmin, 'token' => $firstToken] = provisionedSchoolAdmin('First Academy', 'first-academy', 'First Admin', 'first@example.test');
    ['school' => $secondSchool] = provisionedSchoolAdmin('Second Academy', 'second-academy', 'Second Admin', 'second@example.test');
    $password = 'Str0ng-Passw0rd!2026';

    $this->post('http://'.tenantHost($firstSchool->getKey())."/school-admin/setup/{$firstToken}", [
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertRedirect();

    $firstAdmin->refresh();

    // The first school's admin is valid for their own school...
    expect($firstAdmin->canAccessPanel(Filament::getPanel('school-admin')))->toBeTrue();

    // ...but the request host remains authoritative: another school's panel is denied.
    $this->actingAs($firstAdmin)
        ->get('http://'.tenantHost($secondSchool->getKey()).'/school-admin')
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Idempotency and existing-school recovery
// ---------------------------------------------------------------------------

test('provisioning is idempotent and never creates a duplicate school admin', function (): void {
    $school = app(SchoolService::class)->create(SchoolData::fromArray([
        'name' => 'Idempotent Academy',
        'slug' => 'idempotent-academy',
    ]));

    $service = app(SchoolAdminProvisioningService::class);

    $first = $service->provision($school, 'Admin One', 'admin1@example.test');
    $second = $service->provision($school, 'Admin Two', 'admin2@example.test');

    expect($second->created)->toBeFalse()
        ->and($second->setupUrl)->toBeNull()
        ->and($second->user->getKey())->toBe($first->user->getKey())
        ->and($school->users()->count())->toBe(1)
        ->and($school->users()->whereHas('roles', fn ($query) => $query->where('name', 'school_admin'))->count())->toBe(1)
        ->and(SchoolAdminSetupToken::query()->count())->toBe(1)
        ->and(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(1);
});

test('an existing school can be recovered without recreating the school or duplicating users', function (): void {
    $school = app(SchoolService::class)->create(SchoolData::fromArray([
        'name' => 'Legacy Academy',
        'slug' => 'legacy-academy',
        'email' => 'contact@legacy.test',
    ]));

    $service = app(SchoolAdminProvisioningService::class);

    expect($service->findSchoolAdmin($school))->toBeNull();

    $provisioned = $service->provision($school, 'Recovered Admin', 'recovered@legacy.test');
    $oldPasswordHash = $provisioned->user->password;

    $recovered = $service->issueSetupLink($school);

    expect($recovered->created)->toBeFalse()
        ->and($recovered->user->getKey())->toBe($provisioned->user->getKey())
        ->and($recovered->setupUrl)->not->toBe($provisioned->setupUrl)
        ->and($recovered->user->password)->toBe($oldPasswordHash)
        ->and($service->findValidByToken(schoolAdminTokenFromUrl((string) $provisioned->setupUrl)))->toBeNull()
        ->and($service->findValidByToken(schoolAdminTokenFromUrl((string) $recovered->setupUrl)))->not->toBeNull()
        ->and(School::query()->where('slug', 'legacy-academy')->count())->toBe(1)
        ->and($school->users()->count())->toBe(1)
        ->and(AuditLog::query()->where('action', AuditAction::SetupLinkIssued->value)->count())->toBe(1);
});

test('issuing a recovery link never resets an existing password until it is redeemed', function (): void {
    $school = app(SchoolService::class)->create(SchoolData::fromArray([
        'name' => 'Active Admin Academy',
        'slug' => 'active-admin-academy',
    ]));

    $role = Role::findOrCreate('school_admin', 'web');
    $admin = User::factory()->create([
        'school_id' => $school->getKey(),
        'email' => 'existing@active.test',
        'password' => Hash::make('Existing-Passw0rd!2026'),
        'status' => UserStatus::Active,
    ]);
    $admin->assignRole($role);

    $before = $admin->password;

    $result = app(SchoolAdminProvisioningService::class)->issueSetupLink($school);

    expect($result->user->password)->toBe($before)
        ->and($result->user->status)->toBe(UserStatus::Active)
        ->and($school->users()->count())->toBe(1);

    $token = schoolAdminTokenFromUrl((string) $result->setupUrl);
    $host = tenantHost($school->getKey());
    $newPassword = 'Brand-New-Passw0rd!2026';

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ])->assertRedirect();

    $admin->refresh();

    expect(Hash::check($newPassword, $admin->password))->toBeTrue()
        ->and(Hash::check('Existing-Passw0rd!2026', $admin->password))->toBeFalse()
        ->and($admin->status)->toBe(UserStatus::Active);
});

test('recovery refuses to act when no school admin exists', function (): void {
    $school = app(SchoolService::class)->create(SchoolData::fromArray([
        'name' => 'No Admin Academy',
        'slug' => 'no-admin-academy',
    ]));

    expect(fn () => app(SchoolAdminProvisioningService::class)->issueSetupLink($school))
        ->toThrow(ValidationException::class);
});

// ---------------------------------------------------------------------------
// Super Admin panel wiring
// ---------------------------------------------------------------------------

test('the super admin school creation page provisions the initial school admin', function (): void {
    $superAdmin = User::factory()->create(['email' => 'root@platform.test']);
    $superAdmin->assignRole(Role::findOrCreate('super_admin', 'web'));

    $this->actingAs($superAdmin);

    Filament::setServingStatus();
    Filament::setCurrentPanel('super-admin');

    Livewire::test(\App\Filament\Resources\SuperAdminSchoolResource\Pages\CreateSuperAdminSchool::class)
        ->fillForm([
            'name' => 'Wired Academy',
            'slug' => 'wired-academy',
            'status' => SchoolStatus::Pending->value,
            'admin_name' => 'Wired Admin',
            'admin_email' => 'wired@example.test',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $school = School::query()->where('slug', 'wired-academy')->first();
    $admin = User::query()->where('email', 'wired@example.test')->first();

    expect($school)->not->toBeNull()
        ->and($admin)->not->toBeNull()
        ->and($admin->school_id)->toBe($school->getKey())
        ->and($admin->hasRole('school_admin'))->toBeTrue()
        ->and($admin->status)->toBe(UserStatus::Pending)
        ->and(SchoolAdminSetupToken::query()->where('user_id', $admin->getKey())->count())->toBe(1);
});

// ---------------------------------------------------------------------------
// Failure, rollback and input validation
// ---------------------------------------------------------------------------

test('provisioning rolls back when the administrator email is already in use', function (): void {
    User::factory()->create(['email' => 'taken@example.test']);

    expect(fn () => app(SchoolProvisioningService::class)->create(
        SchoolData::fromArray(['name' => 'Rollback Academy', 'slug' => 'rollback-academy']),
        'Taken Admin',
        'taken@example.test',
    ))->toThrow(ValidationException::class);

    expect(School::withTrashed()->where('slug', 'rollback-academy')->exists())->toBeFalse()
        ->and(DB::table('domains')->where('domain', 'rollback-academy.nexusschool.test')->exists())->toBeFalse()
        ->and(User::query()->where('email', 'taken@example.test')->count())->toBe(1)
        ->and(SchoolAdminSetupToken::query()->count())->toBe(0);
});

test('an initial administrator name and email are mandatory', function (): void {
    expect(fn () => app(SchoolProvisioningService::class)->create(
        SchoolData::fromArray(['name' => 'Missing Admin Academy', 'slug' => 'missing-admin-academy']),
        ' ',
        'admin@example.test',
    ))->toThrow(ValidationException::class);

    expect(fn () => app(SchoolProvisioningService::class)->create(
        SchoolData::fromArray(['name' => 'Missing Email Academy', 'slug' => 'missing-email-academy']),
        'Admin Name',
        ' ',
    ))->toThrow(ValidationException::class);

    expect(School::withTrashed()->count())->toBe(0);
});

test('provisioning refuses to take over an unrelated existing account', function (): void {
    $school = app(SchoolService::class)->create(SchoolData::fromArray([
        'name' => 'Conflict Academy',
        'slug' => 'conflict-academy',
    ]));

    $existing = User::factory()->create(['email' => 'someone@example.test']);

    expect(fn () => app(SchoolAdminProvisioningService::class)->provision(
        $school,
        'Someone Else',
        'someone@example.test',
    ))->toThrow(ValidationException::class);

    expect($existing->fresh()->hasRole('school_admin'))->toBeFalse()
        ->and($existing->fresh()->school_id)->toBeNull()
        ->and($school->users()->count())->toBe(0)
        ->and(SchoolAdminSetupToken::query()->count())->toBe(0);
});

// ---------------------------------------------------------------------------
// Audit trail
// ---------------------------------------------------------------------------

test('provisioning, recovery and password setup are audited without storing credentials', function (): void {
    ['school' => $school, 'admin' => $admin, 'token' => $token] = provisionedSchoolAdmin();
    $host = tenantHost($school->getKey());
    $password = 'Str0ng-Passw0rd!2026';

    app(SchoolAdminProvisioningService::class)->issueSetupLink($school);

    $this->post("http://{$host}/school-admin/setup/{$token}", [
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertNotFound();

    // The recovery link issued above is the only valid one now.
    $latestToken = SchoolAdminSetupToken::query()->whereNull('used_at')->firstOrFail();

    expect($latestToken->token_hash)->not->toContain($password);
});

test('audit entries reference the school, the administrator and the acting super admin', function (): void {
    $superAdmin = User::factory()->create(['email' => 'root@platform.test']);
    $superAdmin->assignRole(Role::findOrCreate('super_admin', 'web'));

    $result = app(SchoolProvisioningService::class)->create(
        SchoolData::fromArray(['name' => 'Audited Academy', 'slug' => 'audited-academy']),
        'Audited Admin',
        'audited@example.test',
        $superAdmin,
    );

    $audit = AuditLog::query()->where('action', AuditAction::Provisioned->value)->firstOrFail();

    expect($audit->school_id)->toBe($result->school->getKey())
        ->and($audit->auditable_id)->toBe((string) $result->administrator->user->getKey())
        ->and((int) $audit->user_id)->toBe((int) $superAdmin->getKey())
        ->and($audit->user_type)->toBe(User::class)
        ->and($audit->new_values)->not->toHaveKey('password')
        ->and(json_encode($audit->new_values))->not->toContain('password');
});
