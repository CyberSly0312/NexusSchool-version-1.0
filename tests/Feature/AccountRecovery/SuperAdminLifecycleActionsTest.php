<?php

declare(strict_types=1);

use App\Domains\School\Models\School;
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

function lifecycleUiSchool(): School
{
    $id = Str::ulid()->toBase32();
    $domain = 'lifecycle-ui-'.Str::lower(Str::random(8)).'.nexusschool.test';
    $name = 'Lifecycle UI School '.Str::upper(Str::random(5));

    DB::table('tenants')->insert([
        'id' => $id,
        'uuid' => Str::ulid(),
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
        'database' => 'school_'.Str::lower(Str::random(8)),
        'status' => 'active',
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

function lifecycleUiActor(string $role = 'super_admin'): User
{
    Role::findOrCreate($role, 'web');

    $actor = User::factory()->create([
        'school_id' => null,
        'status' => UserStatus::Active,
    ]);
    $actor->assignRole($role);

    return $actor;
}

function lifecycleUiAdmin(School $school, string $email): User
{
    Role::findOrCreate('school_admin', 'web');

    $admin = User::factory()->create([
        'school_id' => $school->getKey(),
        'email' => $email,
        'status' => UserStatus::Active,
        'password' => Hash::make('AncienMotDePasse1!'),
    ]);
    $admin->assignRole('school_admin');

    return $admin;
}

function lifecycleUiEnterPanel(User $actor): void
{
    test()->actingAs($actor);
    Filament::setServingStatus();
    Filament::setCurrentPanel('super-admin');
}

function lifecycleUiPage(School $school)
{
    return Livewire::test(EditSuperAdminSchool::class, ['record' => $school->getKey()]);
}

/**
 * @return array<int, array<string, mixed>>
 */
function lifecycleUiNotifications(): array
{
    $component = new \Filament\Notifications\Livewire\Notifications();
    $component->mount();

    return $component->notifications
        ->values()
        ->map(fn ($notification): array => $notification->toArray())
        ->all();
}

/**
 * @param  array<int, array<string, mixed>>  $notifications
 * @return array{0: string, 1: string}
 */
function lifecycleUiNotification(array $notifications, string $title): array
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

function lifecycleUiRedirectToken($component): string
{
    $redirect = (string) ($component->effects['redirect'] ?? '');
    $fragment = parse_url($redirect, PHP_URL_FRAGMENT);

    if (! is_string($fragment)) {
        test()->fail('Aucun jeton trouvé dans le fragment de redirection.');
    }

    parse_str($fragment, $parts);

    return (string) ($parts['jeton'] ?? '');
}

test('recovery actions are visible only to an active super admin with an existing school admin', function (): void {
    $school = lifecycleUiSchool();
    $admin = lifecycleUiAdmin($school, 'ui-admin@example.test');

    lifecycleUiEnterPanel(lifecycleUiActor());

    lifecycleUiPage($school)
        ->assertActionVisible('initiate_school_admin_reset')
        ->assertActionVisible('suspend_school_admin')
        ->assertActionVisible('replace_school_admin')
        ->assertActionHidden('provision_school_admin');

    expect($admin->fresh()->status)->toBe(UserStatus::Active);
});

test('the super admin panel is forbidden for a non super admin actor', function (): void {
    $school = lifecycleUiSchool();
    lifecycleUiAdmin($school, 'ui-hidden@example.test');
    $actor = makeRoleUser($school->getKey(), 'school_admin');

    $this->actingAs($actor)
        ->get('http://localhost/super-admin')
        ->assertForbidden();
});

test('initiating a reset redirects to a one-time delivery page without persisting the token', function (): void {
    $school = lifecycleUiSchool();
    $admin = lifecycleUiAdmin($school, 'ui-reset@example.test');
    $actor = lifecycleUiActor();
    lifecycleUiEnterPanel($actor);

    $page = lifecycleUiPage($school);
    $page->callAction('initiate_school_admin_reset')
        ->assertRedirectContains('#jeton=');

    $deliveryUrl = route('central.capability.delivery', [
        'school' => $school->getKey(),
        'user' => $admin->getKey(),
        'purpose' => 'reset',
    ], false);
    $token = lifecycleUiRedirectToken($page);

    $invitation = UserInvitation::findByPlainToken($token);

    expect($token)->toHaveLength(64)
        ->and($invitation)->not->toBeNull()
        ->and($invitation->purpose)->toBe(UserInvitation::PURPOSE_SCHOOL_ADMIN_RESET)
        ->and($invitation->token_hash)->toBe(hash('sha256', $token))
        ->and(lifecycleUiNotifications())->toBe([]);

    $delivery = $this->actingAs($actor)->get('http://localhost'.$deliveryUrl);
    $delivery->assertOk()
        ->assertSee('Lien à usage unique — Réinitialisation du mot de passe')
        ->assertSee('Aucun e-mail ni SMS n’a été envoyé')
        ->assertDontSee($token)
        ->assertDontSee('AncienMotDePasse1!');

    $audit = AuditLog::query()->where('action', AuditAction::PasswordResetRequested->value)->firstOrFail();
    $serialized = json_encode($audit->toArray(), JSON_THROW_ON_ERROR);

    expect($serialized)->not->toContain($token)
        ->not->toContain((string) $admin->password)
        ->and($admin->fresh()->password)->toBe((string) $admin->password);
});

test('the page blocks the last active suspension and replaces the admin with a distinct account', function (): void {
    $school = lifecycleUiSchool();
    $former = lifecycleUiAdmin($school, 'ui-former@example.test');
    lifecycleUiEnterPanel(lifecycleUiActor());

    lifecycleUiPage($school)->callAction('suspend_school_admin');

    [$refusal] = lifecycleUiNotification(lifecycleUiNotifications(), 'Opération refusée');

    expect($refusal)->toContain('dernier administrateur d’établissement actif')
        ->and($former->fresh()->status)->toBe(UserStatus::Active);

    $actor = lifecycleUiActor();
    lifecycleUiEnterPanel($actor);

    $page = lifecycleUiPage($school);
    $page->callAction('replace_school_admin', data: [
        'first_name' => 'Nouvelle',
        'last_name' => 'Responsable',
        'email' => 'ui-replacement@example.test',
    ])->assertRedirectContains('#jeton=');

    $token = lifecycleUiRedirectToken($page);
    $replacement = User::query()->where('email', 'ui-replacement@example.test')->firstOrFail();
    $deliveryUrl = route('central.capability.delivery', [
        'school' => $school->getKey(),
        'user' => $replacement->getKey(),
        'purpose' => 'activation',
    ], false);

    expect($former->fresh()->status)->toBe(UserStatus::Suspended)
        ->and($replacement->status)->toBe(UserStatus::Pending)
        ->and((string) $replacement->getKey())->not->toBe((string) $former->getKey())
        ->and(UserInvitation::findByPlainToken($token)?->purpose)->toBe(UserInvitation::PURPOSE_SCHOOL_ADMIN_INITIAL)
        ->and(lifecycleUiNotifications())->toBe([]);

    $this->actingAs($actor)->get('http://localhost'.$deliveryUrl)
        ->assertOk()
        ->assertSee('Lien à usage unique — Activation du compte')
        ->assertSee('Aucun e-mail ni SMS n’a été envoyé')
        ->assertDontSee($token)
        ->assertDontSee('AncienMotDePasse1!');

    expect(AuditLog::query()->where('action', AuditAction::AccessSuspended->value)->count())->toBe(1)
        ->and(AuditLog::query()->where('action', AuditAction::SchoolAdminReplaced->value)->count())->toBe(1);
});
