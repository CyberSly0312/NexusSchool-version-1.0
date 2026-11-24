<?php

declare(strict_types=1);

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Enums\SchoolStatus;
use App\Domains\School\Exceptions\InitialSchoolAdminProvisioningException;
use App\Domains\School\Models\School;
use App\Domains\School\Models\SchoolAdminSetupToken;
use App\Domains\School\Services\InitialSchoolAdminProvisioningService;
use App\Domains\School\Services\SchoolProvisioningService;
use App\Domains\System\Enums\AuditAction;
use App\Domains\User\Enums\UserStatus;
use App\Filament\Resources\SuperAdminSchoolResource\Pages\EditSuperAdminSchool;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserInvitation;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(\Database\Seeders\PermissionSeeder::class);
});

/**
 * @return School
 */
function initialProvisioningSchool(string $status = 'active', ?string $domain = null): School
{
    // Filament route-binds the edit record through School::resolveRouteBindingQuery(),
    // which (via HasUlids) rejects the non-ULID ids created by makeTenant().
    $id = Str::ulid()->toBase32();
    $domain ??= Str::lower($id).'.nexusschool.test';
    $name = 'Initial Admin School '.Str::upper(Str::random(5));

    DB::table('tenants')->insert([
        'id' => $id,
        'uuid' => Str::ulid(),
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
        'database' => 'school_'.Str::lower(Str::random(8)),
        'status' => $status,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('domains')->insert([
        'domain' => $domain,
        'tenant_id' => $id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    if (! app()->bound('testing.default_tenant_host')) {
        useTenantHost($id);
        app()->instance('testing.default_tenant_host', $domain);
    }

    return School::query()->findOrFail($id);
}

function initialProvisioningActor(string $status = 'active', string $role = 'super_admin'): User
{
    Role::findOrCreate($role, 'web');

    $actor = User::factory()->create([
        'school_id' => null,
        'status' => $status,
    ]);
    $actor->assignRole($role);

    return $actor;
}

function initialProvisioningEnterPanel(User $actor): void
{
    test()->actingAs($actor);
    Filament::setServingStatus();
    Filament::setCurrentPanel('super-admin');
}

function initialProvisioningPage(School $school)
{
    return Livewire::test(EditSuperAdminSchool::class, ['record' => $school->getKey()]);
}

/**
 * Read the one-shot notification queue the same way Filament does.
 *
 * @return array<int, array<string, mixed>>
 */
function initialProvisioningSentNotifications(): array
{
    $component = new \Filament\Notifications\Livewire\Notifications();
    $component->mount();

    return $component->notifications
        ->values()
        ->map(fn ($notification): array => $notification->toArray())
        ->all();
}

/**
 * @param array<int, array<string, mixed>> $notifications
 * @return array{0: string, 1: string}
 */
function initialProvisioningNotification(array $notifications, string $title): array
{
    foreach ($notifications as $notification) {
        if (($notification['title'] ?? null) === $title) {
            return [
                (string) ($notification['body'] ?? ''),
                (string) ($notification['duration'] ?? ''),
            ];
        }
    }

    test()->fail("Notification [{$title}] not found.");
}

test('the action exposes only the three required French form fields', function (): void {
    $school = initialProvisioningSchool();
    initialProvisioningEnterPanel(initialProvisioningActor());

    $page = initialProvisioningPage($school)->assertActionVisible('provision_school_admin');
    $action = $page->instance()->getAction('provision_school_admin');

    expect($action)->not->toBeNull();

    $schema = (new \ReflectionProperty($action, 'schema'))->getValue($action);

    expect($schema)->toBeArray()
        ->and(array_map(fn ($component): string => (string) $component->getName(), $schema))->toBe(['first_name', 'last_name', 'email'])
        ->and(array_map(fn ($component): string => (string) $component->getLabel(), $schema))->toBe(['Prénom', 'Nom', 'Adresse e-mail'])
        ->and($action->getLabel())->toBe('Créer l’administrateur de l’établissement')
        ->and($action->getModalSubmitActionLabel())->toBe('Créer le compte');
});

test('an active super admin provisions a pending school admin with the canonical activation link', function (): void {
    $school = initialProvisioningSchool('active', 'initial-admin-canonical.test');
    $actor = initialProvisioningActor();
    initialProvisioningEnterPanel($actor);

    initialProvisioningPage($school)
        ->assertActionVisible('provision_school_admin')
        ->callAction('provision_school_admin', data: [
            'first_name' => 'Ada',
            'last_name' => 'Admin',
            'email' => 'ADA.Admin@Example.Test',
        ])
        ->assertHasNoFormErrors();

    $admin = User::query()->where('email', 'ada.admin@example.test')->firstOrFail();
    $invitation = UserInvitation::query()->where('user_id', $admin->getKey())->firstOrFail();

    expect($admin->status)->toBe(UserStatus::Pending)
        ->and((string) $admin->school_id)->toBe($school->getKey())
        ->and($admin->getRoleNames()->all())->toBe(['school_admin'])
        ->and($invitation->purpose)->toBe(UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL)
        ->and((string) $invitation->school_id)->toBe($school->getKey())
        ->and((string) $invitation->invited_by_user_id)->toBe((string) $actor->getKey())
        ->and($invitation->isUsable())->toBeTrue()
        ->and(now()->diffInHours($invitation->expires_at))->toBeGreaterThanOrEqual(47)
        ->and($invitation->getAttributes())->not->toHaveKey('plain_token')
        ->and($invitation->toArray())->not->toHaveKey('token_hash');

    $notifications = initialProvisioningSentNotifications();
    [$body, $duration] = initialProvisioningNotification($notifications, 'Lien d’activation');
    preg_match('/account\/activate\/([a-f0-9]{64})/', $body, $matches);
    $plainToken = $matches[1] ?? '';

    expect($plainToken)->toHaveLength(64)
        ->and($invitation->token_hash)->toBe(hash('sha256', $plainToken))
        ->and($body)->toContain('initial-admin-canonical.test')
        ->toContain('/account/activate/'.$plainToken)
        ->toContain('Transmettre ce lien de manière sécurisée')
        ->toContain('Aucun e-mail ni SMS n’a été envoyé')
        ->toContain('Le compte restera inaccessible jusqu’à son activation')
        ->toContain('se connecter normalement')
        ->and($duration)->not->toBe('persistent')
        ->and(collect($notifications)->filter(fn (array $notification): bool => str_contains((string) ($notification['body'] ?? ''), '/account/activate/'))->count())->toBe(1)
        ->and(json_encode($invitation))->not->toContain($plainToken);

    // Filament notifications are pulled once from the session queue: a second
    // read must not expose the one-time link again.
    expect(initialProvisioningSentNotifications())->toBe([]);

    $audit = AuditLog::query()->where('event', 'school_admin.provisioned')->firstOrFail();

    expect(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(1)
        ->and((string) $audit->school_id)->toBe($school->getKey())
        ->and((string) $audit->auditable_id)->toBe((string) $admin->getKey())
        ->and((string) $audit->user_id)->toBe((string) $actor->getKey())
        ->and($audit->new_values)->not->toHaveKey('password')
        ->not->toHaveKey('token')
        ->not->toHaveKey('activation_url')
        ->and(json_encode($audit))->not->toContain($plainToken);
});

test('the created admin receives the same school permissions as the historical flow and no platform permissions', function (): void {
    $school = initialProvisioningSchool();
    $actor = initialProvisioningActor();
    initialProvisioningEnterPanel($actor);

    initialProvisioningPage($school)->callAction('provision_school_admin', data: [
        'first_name' => 'School',
        'last_name' => 'Admin',
        'email' => 'permissions@example.test',
    ])->assertHasNoFormErrors();

    $admin = User::query()->where('email', 'permissions@example.test')->firstOrFail();

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
        ->and($admin->can('backup.view'))->toBeFalse()
        ->and($admin->can('maintenance_window.view'))->toBeFalse();
});

test('permission granting preserves deliberate extra grants without broadening the default set', function (): void {
    $school = initialProvisioningSchool();
    $actor = initialProvisioningActor();
    initialProvisioningEnterPanel($actor);

    $role = Role::findOrCreate('school_admin', 'web');
    $role->givePermissionTo('api.view');

    initialProvisioningPage($school)->callAction('provision_school_admin', data: [
        'first_name' => 'Additive',
        'last_name' => 'Grants',
        'email' => 'additive.grants@example.test',
    ])->assertHasNoFormErrors();

    $admin = User::query()->where('email', 'additive.grants@example.test')->firstOrFail();

    expect($role->fresh()->hasPermissionTo('api.view'))->toBeTrue()
        ->and($role->fresh()->hasPermissionTo('user.create'))->toBeTrue()
        ->and($admin->can('api.view'))->toBeTrue()
        ->and($admin->can('school.create'))->toBeFalse();
});

test('form supplied school id, role, status and password cannot override server-side values', function (): void {
    $school = initialProvisioningSchool('active', 'displayed-school.test');
    $otherSchool = initialProvisioningSchool('active', 'other-school.test');
    initialProvisioningEnterPanel(initialProvisioningActor());

    initialProvisioningPage($school)->callAction('provision_school_admin', data: [
        'first_name' => 'Fixed',
        'last_name' => 'Values',
        'email' => 'fixed.values@example.test',
        'school_id' => $otherSchool->getKey(),
        'role' => 'super_admin',
        'status' => 'active',
        'password' => 'ClientProvided123!',
    ])->assertHasNoFormErrors();

    $admin = User::query()->where('email', 'fixed.values@example.test')->firstOrFail();
    $invitation = UserInvitation::query()->where('user_id', $admin->getKey())->firstOrFail();

    expect((string) $admin->school_id)->toBe($school->getKey())
        ->and($admin->getRoleNames()->all())->toBe(['school_admin'])
        ->and($admin->status)->toBe(UserStatus::Pending)
        ->and(Hash::check('ClientProvided123!', $admin->password))->toBeFalse()
        ->and((string) $invitation->school_id)->toBe($school->getKey())
        ->and(UserInvitation::query()->where('school_id', $otherSchool->getKey())->count())->toBe(0);
});

test('a pending school is refused', function (): void {
    $school = initialProvisioningSchool('pending');
    $actor = initialProvisioningActor();
    initialProvisioningEnterPanel($actor);

    initialProvisioningPage($school)->assertActionHidden('provision_school_admin');

    expect(fn () => app(InitialSchoolAdminProvisioningService::class)->provision(
        $school,
        'Pending',
        'School',
        'pending.school@example.test',
        $actor,
    ))->toThrow(InitialSchoolAdminProvisioningException::class, 'n’est pas disponible');

    expect(User::query()->where('email', 'pending.school@example.test')->exists())->toBeFalse()
        ->and(UserInvitation::query()->count())->toBe(0);
});

test('an inactive or suspended school is refused', function (string $status): void {
    $school = initialProvisioningSchool($status);
    $actor = initialProvisioningActor();
    initialProvisioningEnterPanel($actor);

    initialProvisioningPage($school)->assertActionHidden('provision_school_admin');

    expect(fn () => app(InitialSchoolAdminProvisioningService::class)->provision(
        $school,
        'Closed',
        'School',
        $status.'.school@example.test',
        $actor,
    ))->toThrow(InitialSchoolAdminProvisioningException::class, 'n’est pas disponible');

    expect(User::query()->where('email', $status.'.school@example.test')->exists())->toBeFalse()
        ->and(UserInvitation::query()->count())->toBe(0);
})->with([
    'inactive' => ['inactive'],
    'suspended' => ['suspended'],
]);

test('a non super admin actor is refused without technical details', function (): void {
    $school = initialProvisioningSchool();
    $actor = initialProvisioningActor('active', 'school_admin');

    expect(fn () => app(InitialSchoolAdminProvisioningService::class)->provision(
        $school,
        'No',
        'Access',
        'no.access@example.test',
        $actor,
    ))->toThrow(InitialSchoolAdminProvisioningException::class, 'administrateurs plateforme actifs');
});

test('an inactive super admin actor is refused', function (): void {
    $school = initialProvisioningSchool();
    $actor = initialProvisioningActor('inactive');

    expect(fn () => app(InitialSchoolAdminProvisioningService::class)->provision(
        $school,
        'Inactive',
        'Actor',
        'inactive.actor@example.test',
        $actor,
    ))->toThrow(InitialSchoolAdminProvisioningException::class, 'administrateurs plateforme actifs');

    expect(User::query()->where('email', 'inactive.actor@example.test')->exists())->toBeFalse();
});

test('an unauthenticated actor is refused by the orchestration service', function (): void {
    $school = initialProvisioningSchool();

    expect(fn () => app(InitialSchoolAdminProvisioningService::class)->provision(
        $school,
        'Guest',
        'Actor',
        'guest.actor@example.test',
        null,
    ))->toThrow(InitialSchoolAdminProvisioningException::class, 'administrateurs plateforme actifs');
});

test('an existing school admin makes the action unnecessary', function (): void {
    $school = initialProvisioningSchool();
    $actor = initialProvisioningActor();
    initialProvisioningEnterPanel($actor);
    Role::findOrCreate('school_admin', 'web');

    $existing = User::factory()->create([
        'school_id' => $school->getKey(),
        'email' => 'existing.admin@example.test',
        'status' => UserStatus::Active->value,
    ]);
    $existing->assignRole('school_admin');

    initialProvisioningPage($school)->assertActionHidden('provision_school_admin');

    expect(fn () => app(InitialSchoolAdminProvisioningService::class)->provision(
        $school,
        'Duplicate',
        'Admin',
        'duplicate.admin@example.test',
        $actor,
    ))->toThrow(InitialSchoolAdminProvisioningException::class, 'existe déjà');

    expect(User::query()->where('email', 'duplicate.admin@example.test')->exists())->toBeFalse()
        ->and(UserInvitation::query()->count())->toBe(0)
        ->and(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(0);
});

test('an email already used by an active account is rejected on the email field', function (): void {
    $school = initialProvisioningSchool();
    initialProvisioningEnterPanel(initialProvisioningActor());
    User::factory()->create(['email' => 'taken@example.test']);

    $page = initialProvisioningPage($school)->callAction('provision_school_admin', data: [
        'first_name' => 'Taken',
        'last_name' => 'Email',
        'email' => 'TAKEN@example.test',
    ]);
    $page->assertHasErrors(['email']);

    expect(User::query()->where('email', 'taken@example.test')->count())->toBe(1)
        ->and(UserInvitation::query()->count())->toBe(0)
        ->and(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(0);
});

test('an email belonging to a soft deleted account is rejected on the email field', function (): void {
    $school = initialProvisioningSchool();
    initialProvisioningEnterPanel(initialProvisioningActor());
    $deleted = User::factory()->create(['email' => 'deleted@example.test']);
    $deleted->delete();

    initialProvisioningPage($school)->callAction('provision_school_admin', data: [
        'first_name' => 'Deleted',
        'last_name' => 'Email',
        'email' => 'deleted@example.test',
    ])->assertHasErrors(['email']);

    expect(User::withTrashed()->where('email', 'deleted@example.test')->count())->toBe(1)
        ->and(UserInvitation::query()->count())->toBe(0)
        ->and(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(0);
});

test('a failure while issuing the invitation leaves no partial account, invitation or audit', function (): void {
    $school = initialProvisioningSchool();
    initialProvisioningEnterPanel(initialProvisioningActor());

    UserInvitation::creating(function (): void {
        throw new RuntimeException('invitation backend unavailable');
    });

    $page = initialProvisioningPage($school);

    try {
        $page->callAction('provision_school_admin', data: [
            'first_name' => 'Rollback',
            'last_name' => 'Failure',
            'email' => 'rollback.failure@example.test',
        ]);
    } finally {
        UserInvitation::clearBootedModels();
    }

    expect(User::withTrashed()->where('email', 'rollback.failure@example.test')->exists())->toBeFalse()
        ->and(UserInvitation::query()->count())->toBe(0)
        ->and(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(0);

    [$body] = initialProvisioningNotification(initialProvisioningSentNotifications(), 'Création impossible');

    expect($body)->toBe('Le compte n’a pas pu être créé. Aucune modification n’a été conservée.')
        ->not->toContain('invitation backend unavailable');
});

test('the historical school admin setup link action still works after the new provisioning flow', function (): void {
    $school = initialProvisioningSchool();
    $actor = initialProvisioningActor();
    initialProvisioningEnterPanel($actor);

    initialProvisioningPage($school)->callAction('provision_school_admin', data: [
        'first_name' => 'Legacy',
        'last_name' => 'Recovery',
        'email' => 'legacy.recovery@example.test',
    ])->assertHasNoFormErrors();

    initialProvisioningPage($school)
        ->assertActionVisible('issue_school_admin_setup_link')
        ->callAction('issue_school_admin_setup_link');

    expect(SchoolAdminSetupToken::query()->count())->toBe(1)
        ->and(AuditLog::query()->where('action', AuditAction::SetupLinkIssued->value)->count())->toBe(1)
        ->and(AuditLog::query()->where('action', AuditAction::Provisioned->value)->count())->toBe(1);
});

test('the general school creation workflow still uses the historical setup mechanism', function (): void {
    $actor = initialProvisioningActor();

    $result = app(SchoolProvisioningService::class)->create(
        SchoolData::fromArray([
            'name' => 'Legacy Academy',
            'slug' => 'legacy-academy',
        ]),
        'Legacy Admin',
        'legacy.admin@example.test',
        $actor,
    );

    expect($result->school->status)->toBe(SchoolStatus::Pending)
        ->and($result->administrator->user->status)->toBe(UserStatus::Pending)
        ->and(SchoolAdminSetupToken::query()->count())->toBe(1)
        ->and(UserInvitation::query()->count())->toBe(0);
});

test('existing active accounts are not affected by initial school admin provisioning', function (): void {
    $school = initialProvisioningSchool();
    initialProvisioningEnterPanel(initialProvisioningActor());

    Role::findOrCreate('teacher', 'web');
    $existing = User::factory()->create([
        'school_id' => $school->getKey(),
        'email' => 'existing.teacher@example.test',
        'status' => UserStatus::Active->value,
        'password' => Hash::make('ExistingPassword123!'),
    ]);
    $existing->assignRole('teacher');
    $originalPassword = $existing->password;

    initialProvisioningPage($school)->callAction('provision_school_admin', data: [
        'first_name' => 'New',
        'last_name' => 'Admin',
        'email' => 'new.admin@example.test',
    ])->assertHasNoFormErrors();

    $fresh = $existing->fresh();

    expect($fresh->status)->toBe(UserStatus::Active)
        ->and($fresh->password)->toBe($originalPassword)
        ->and(UserInvitation::query()->where('user_id', $fresh->getKey())->count())->toBe(0)
        ->and($fresh->getRoleNames()->all())->toBe(['teacher']);
});

test('result debug output never exposes the plaintext activation token', function (): void {
    $school = initialProvisioningSchool('active', 'debug-token.test');
    $actor = initialProvisioningActor();

    $result = app(InitialSchoolAdminProvisioningService::class)->provision(
        $school,
        'Debug',
        'Safety',
        'debug.safety@example.test',
        $actor,
    );

    $token = $result->plainToken();

    expect($token)->toBeString()->toHaveLength(64)
        ->and($result->__debugInfo()['activationUrl'])->toBe('[redacted]')
        ->and($result->__debugInfo()['provisioning']->__debugInfo()['plainToken'])->toBe('[redacted]')
        ->and(print_r($result, true))->not->toContain((string) $token)
        ->and(json_encode($result))->not->toContain((string) $token);
});
