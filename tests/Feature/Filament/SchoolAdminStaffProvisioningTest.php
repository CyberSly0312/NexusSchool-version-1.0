<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\School\Models\School;
use App\Domains\User\DTOs\AccountProvisioningCommand;
use App\Domains\User\DTOs\AccountProvisioningContext;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Exceptions\AccountProvisioningException;
use App\Domains\User\Services\AccountProvisioningService;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use App\Models\UserInvitation;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Facades\Filament;
use Filament\Notifications\Livewire\Notifications;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Livewire\Livewire;
use ReflectionObject;
use ReflectionProperty;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class SchoolAdminStaffProvisioningTest extends TestCase
{
    use RefreshDatabase;

    protected string $schoolId;

    protected string $domain;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domain = 'staff-canonical.nexusschool.test';
        $this->schoolId = makeTenant('Staff Canonical School', 'active', $this->domain);

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->ensureRoles();

        $this->admin = $this->makeSchoolAdmin($this->schoolId);
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
        Tenancy::initialize(School::query()->findOrFail($this->schoolId));
    }

    private function ensureRoles(): void
    {
        foreach ([
            'school_admin',
            'teacher',
            'secretary',
            'accountant',
            'director',
            'student',
            'parent',
            'super_admin',
        ] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }

    private function makeSchoolAdmin(string $schoolId, string $status = 'active'): User
    {
        $actor = User::factory()->create([
            'school_id' => $schoolId,
            'status' => $status,
        ]);

        $role = Role::query()->where('name', 'school_admin')->where('guard_name', 'web')->firstOrFail();
        $role->syncPermissions(Permission::query()->get());
        $actor->assignRole($role);

        return $actor;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Personnel',
            'last_name' => 'Exemple',
            'email' => 'personnel-exemple@example.test',
            'phone' => '+33123456789',
            'role' => 'teacher',
        ], $overrides);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sentNotifications(): array
    {
        $component = new Notifications();
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
    private function notificationByTitle(array $notifications, string $title): array
    {
        foreach ($notifications as $notification) {
            if (($notification['title'] ?? null) === $title) {
                return [
                    (string) ($notification['body'] ?? ''),
                    (string) ($notification['duration'] ?? ''),
                ];
            }
        }

        $this->fail("Notification [{$title}] not found.");
    }

    private function assertNoRoleAssignmentsOutsideAdmin(int|string ...$allowedUserKeys): void
    {
        $allowedUserKeys = [$this->admin->getKey(), ...$allowedUserKeys];

        $this->assertSame(
            0,
            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereNotIn('model_id', $allowedUserKeys)
                ->count(),
        );
    }

    public function test_create_form_exposes_only_required_french_fields_and_three_allowed_roles(): void
    {
        $page = Livewire::test(CreateUser::class);
        $components = array_values($page->instance()->form->getComponents());

        $this->assertSame(
            ['first_name', 'last_name', 'email', 'phone', 'role'],
            array_map(fn ($component): string => (string) $component->getName(), $components),
        );
        $this->assertSame(
            ['Prénom', 'Nom', 'Adresse e-mail', 'Téléphone', 'Rôle'],
            array_map(fn ($component): string => (string) $component->getLabel(), $components),
        );
        $this->assertSame([
            'teacher' => 'Enseignant',
            'secretary' => 'Secrétaire',
            'accountant' => 'Comptable',
        ], $components[4]->getOptions());
        $this->assertSame(
            AccountProvisioningService::SCHOOL_ADMIN_ALLOWED_ROLES,
            array_keys($components[4]->getOptions()),
        );

        foreach (['school_id', 'status', 'password', 'password_confirmation'] as $forbiddenField) {
            $this->assertNotContains($forbiddenField, array_map(fn ($component): string => (string) $component->getName(), $components));
        }
    }

    public function test_active_school_admin_can_provision_each_allowed_role(): void
    {
        foreach (AccountProvisioningService::SCHOOL_ADMIN_ALLOWED_ROLES as $roleName) {
            $email = 'staff-'.$roleName.'@example.test';

            Livewire::test(CreateUser::class)
                ->fillForm($this->payload([
                    'email' => $email,
                    'role' => $roleName,
                ]))
                ->call('create')
                ->assertHasNoFormErrors();

            $user = User::query()->where('email', $email)->firstOrFail();
            $invitation = UserInvitation::query()->where('user_id', $user->getKey())->firstOrFail();

            $this->assertSame(UserStatus::Pending, $user->status);
            $this->assertSame($this->schoolId, (string) $user->school_id);
            $this->assertSame([$roleName], $user->getRoleNames()->all());
            $this->assertSame(UserInvitation::PURPOSE_STAFF_INITIAL, $invitation->purpose);
            $this->assertTrue($invitation->isUsable());
        }
    }

    public function test_unauthorized_user_cannot_access_or_use_the_provisioning_flow(): void
    {
        $outsider = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active->value,
        ]);

        $this->actingAs($outsider)
            ->get('/school-admin/users/create')
            ->assertForbidden();

        try {
            AccountProvisioningContext::forSchoolAdmin($outsider);
            $this->fail('A non-school-admin actor must not be accepted by the provisioning context.');
        } catch (InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        $this->assertDatabaseMissing('users', ['email' => 'personnel-exemple@example.test']);
    }

    public function test_inactive_school_admin_cannot_provision_an_account(): void
    {
        $inactiveAdmin = $this->makeSchoolAdmin($this->schoolId, UserStatus::Inactive->value);

        $this->actingAs($inactiveAdmin)
            ->get('/school-admin/users/create')
            ->assertForbidden();

        try {
            app(AccountProvisioningService::class)->provision(
                new AccountProvisioningCommand(
                    schoolId: $this->schoolId,
                    firstName: 'Inactif',
                    lastName: 'Admin',
                    email: 'inactive-admin-provisioning@example.test',
                    roleName: 'teacher',
                ),
                AccountProvisioningContext::forSchoolAdmin($inactiveAdmin),
            );
            $this->fail('An inactive school admin must not be able to provision.');
        } catch (AccountProvisioningException $exception) {
            $this->assertSame(AccountProvisioningException::REASON_ACTOR, $exception->reason);
        }

        $this->assertDatabaseMissing('users', ['email' => 'inactive-admin-provisioning@example.test']);
        $this->assertNoRoleAssignmentsOutsideAdmin($inactiveAdmin->getKey());
    }

    public function test_school_admin_from_another_school_cannot_act_in_current_tenant(): void
    {
        $otherSchoolId = makeTenant('Other Staff School', 'active', 'other-staff.nexusschool.test');
        $otherAdmin = $this->makeSchoolAdmin($otherSchoolId);

        $this->actingAs($otherAdmin)
            ->get('/school-admin/users/create')
            ->assertForbidden();

        try {
            app(AccountProvisioningService::class)->provision(
                new AccountProvisioningCommand(
                    schoolId: $this->schoolId,
                    firstName: 'Autre',
                    lastName: 'Établissement',
                    email: 'other-school-provisioning@example.test',
                    roleName: 'teacher',
                ),
                AccountProvisioningContext::forSchoolAdmin($otherAdmin),
            );
            $this->fail('A school admin must not provision into another tenant.');
        } catch (AccountProvisioningException $exception) {
            $this->assertSame(AccountProvisioningException::REASON_SCHOOL, $exception->reason);
        }

        $this->assertDatabaseMissing('users', ['email' => 'other-school-provisioning@example.test']);
        $this->assertNoRoleAssignmentsOutsideAdmin($otherAdmin->getKey());
    }

    public function test_manual_school_id_status_and_password_injections_are_ignored(): void
    {
        $injectedSchoolId = makeTenant('Injected School', 'active', 'injected-school.nexusschool.test');
        $injectedPassword = 'InjectedPlainPassword123!';

        Livewire::test(CreateUser::class)
            ->fillForm($this->payload([
                'school_id' => $injectedSchoolId,
                'status' => 'active',
                'password' => $injectedPassword,
                'password_confirmation' => $injectedPassword,
            ]))
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::query()->where('email', 'personnel-exemple@example.test')->firstOrFail();

        $this->assertSame($this->schoolId, (string) $user->school_id);
        $this->assertSame(UserStatus::Pending, $user->status);
        $this->assertFalse(Hash::check($injectedPassword, $user->password));
        $this->assertSame(['teacher'], $user->getRoleNames()->all());
        $this->assertDatabaseMissing('users', [
            'email' => 'personnel-exemple@example.test',
            'school_id' => $injectedSchoolId,
        ]);
    }

    public function test_manual_unauthorized_role_injections_are_rejected_by_form_and_service(): void
    {
        foreach (['director', 'school_admin', 'student', 'parent', 'super_admin'] as $roleName) {
            $email = 'injected-'.$roleName.'@example.test';

            Livewire::test(CreateUser::class)
                ->fillForm($this->payload([
                    'email' => $email,
                    'role' => $roleName,
                ]))
                ->call('create')
                ->assertHasFormErrors(['role']);

            $this->assertDatabaseMissing('users', ['email' => $email]);

            try {
                app(AccountProvisioningService::class)->provision(
                    new AccountProvisioningCommand(
                        schoolId: $this->schoolId,
                        firstName: 'Rôle',
                        lastName: 'Interdit',
                        email: 'service-'.$roleName.'@example.test',
                        roleName: $roleName,
                    ),
                    AccountProvisioningContext::forSchoolAdmin($this->admin),
                );
                $this->fail("Service must reject role [{$roleName}].");
            } catch (AccountProvisioningException $exception) {
                $this->assertSame(AccountProvisioningException::REASON_ROLE, $exception->reason);
            }
        }

        $this->assertNoRoleAssignmentsOutsideAdmin();
    }

    public function test_invitation_is_hashed_expires_in_48_hours_and_uses_canonical_url_once(): void
    {
        Log::spy();

        $page = Livewire::test(CreateUser::class)
            ->fillForm($this->payload(['email' => 'ADA.Example@Example.Test']))
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::query()->where('email', 'ada.example@example.test')->firstOrFail();
        $invitation = UserInvitation::query()->where('user_id', $user->getKey())->firstOrFail();
        $notifications = $this->sentNotifications();
        [$body, $duration] = $this->notificationByTitle($notifications, 'Lien d’activation');

        preg_match('/account\/activate\/([a-f0-9]{64})/', $body, $matches);
        $plainToken = $matches[1] ?? '';

        $this->assertSame('ada.example@example.test', $user->email);
        $this->assertSame($this->schoolId, (string) $invitation->school_id);
        $this->assertSame((string) $this->admin->getKey(), (string) $invitation->invited_by_user_id);
        $this->assertSame(hash('sha256', $plainToken), $invitation->token_hash);
        $this->assertSame(UserInvitation::PURPOSE_STAFF_INITIAL, $invitation->purpose);
        $this->assertGreaterThanOrEqual(47, now()->diffInHours($invitation->expires_at));
        $this->assertLessThanOrEqual(48, now()->diffInHours($invitation->expires_at));
        $this->assertArrayNotHasKey('plain_token', $invitation->getAttributes());
        $this->assertArrayNotHasKey('plain_token', $invitation->toArray());
        $this->assertArrayNotHasKey('token_hash', $invitation->toArray());
        $this->assertStringNotContainsString($plainToken, json_encode($invitation) ?: '');

        $this->assertStringContainsString($this->domain, $body);
        $this->assertStringContainsString('/account/activate/'.$plainToken, $body);
        $this->assertStringContainsString('personnel', $body);
        $this->assertStringContainsString('usage unique', $body);
        $this->assertStringContainsString('48 heures', $body);
        $this->assertStringContainsString('définir son propre mot de passe', $body);
        $this->assertStringContainsString('après activation', $body);
        $this->assertStringContainsString('Aucun e-mail ni SMS', $body);
        $this->assertStringNotContainsString($user->password, $body);
        $this->assertStringNotContainsString('password=', $body);
        $this->assertNotSame('persistent', $duration);
        $this->assertSame(1, collect($notifications)->filter(fn (array $notification): bool => str_contains((string) ($notification['body'] ?? ''), '/account/activate/'))->count());
        $this->assertSame([], $this->sentNotifications());

        $instance = $page->instance();
        foreach ((new ReflectionObject($instance))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isInitialized($instance)) {
                $this->assertStringNotContainsString($plainToken, json_encode($property->getValue($instance)) ?: '');
            }
        }
        $this->assertStringNotContainsString($plainToken, json_encode($instance->form->getRawState()) ?: '');

        foreach (['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'] as $level) {
            Log::shouldNotHaveReceived($level);
        }
    }

    public function test_duplicate_active_and_soft_deleted_emails_are_rejected(): void
    {
        $existing = User::factory()->create([
            'school_id' => $this->schoolId,
            'email' => 'duplicate@example.test',
        ]);

        Livewire::test(CreateUser::class)
            ->fillForm($this->payload(['email' => 'DUPLICATE@Example.Test']))
            ->call('create')
            ->assertHasFormErrors(['email']);

        $this->assertSame(1, User::withTrashed()->where('email', 'duplicate@example.test')->count());

        $existing->delete();

        Livewire::test(CreateUser::class)
            ->fillForm($this->payload(['email' => 'duplicate@example.test']))
            ->call('create')
            ->assertHasFormErrors(['email']);

        $this->assertSame(1, User::withTrashed()->where('email', 'duplicate@example.test')->count());
        $this->assertSame(0, UserInvitation::query()->where('user_id', $existing->getKey())->count());
        $this->assertNoRoleAssignmentsOutsideAdmin();
    }

    public function test_invitation_failure_rolls_back_user_and_role(): void
    {
        UserInvitation::creating(function (): void {
            throw new \RuntimeException('Invitation failure');
        });

        try {
            Livewire::test(CreateUser::class)
                ->fillForm($this->payload(['email' => 'rollback-invitation@example.test']))
                ->call('create')
                ->assertNotified('Création impossible');
        } finally {
            UserInvitation::clearBootedModels();
        }

        $this->assertDatabaseMissing('users', ['email' => 'rollback-invitation@example.test']);
        $this->assertSame(0, UserInvitation::query()->count());
        $this->assertNoRoleAssignmentsOutsideAdmin();
    }

    public function test_role_assignment_failure_rolls_back_user_and_invitation(): void
    {
        DB::listen(function ($query): void {
            $sql = strtolower($query->sql);
            if (str_contains($sql, 'insert') && str_contains($sql, 'model_has_roles')) {
                throw new \RuntimeException('Role assignment failure');
            }
        });

        try {
            Livewire::test(CreateUser::class)
                ->fillForm($this->payload(['email' => 'rollback-role@example.test']))
                ->call('create')
                ->assertNotified('Création impossible');
        } finally {
            DB::getEventDispatcher()?->forget('Illuminate\Database\Events\QueryExecuted');
        }

        $this->assertDatabaseMissing('users', ['email' => 'rollback-role@example.test']);
        $this->assertSame(0, UserInvitation::query()->count());
        $this->assertNoRoleAssignmentsOutsideAdmin();
    }

    public function test_editing_an_existing_active_account_keeps_status_and_password_behavior(): void
    {
        $user = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active->value,
            'first_name' => 'Avant',
            'last_name' => 'Modification',
            'password' => Hash::make('ancien-mot-de-passe'),
        ]);
        $user->assignRole('teacher');

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'first_name' => 'Après',
                'last_name' => 'Modification',
                'email' => $user->email,
                'status' => 'active',
                'password' => 'nouveau-mot-de-passe',
                'password_confirmation' => 'nouveau-mot-de-passe',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Après', $user->first_name);
        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertTrue(Hash::check('nouveau-mot-de-passe', $user->password));
        $this->assertSame(0, UserInvitation::query()->where('user_id', $user->getKey())->count());
        $this->assertTrue($user->fresh()->isActive());
    }

    public function test_edit_page_still_exposes_french_role_management_for_existing_accounts(): void
    {
        $user = User::factory()->create([
            'school_id' => $this->schoolId,
            'status' => UserStatus::Active->value,
        ]);
        $user->assignRole('teacher');

        $this->actingAs($this->admin)
            ->get('/school-admin/users/'.$user->getRouteKey().'/edit')
            ->assertSuccessful();

        $title = new ReflectionProperty(RolesRelationManager::class, 'title');
        $this->assertSame('Rôles', $title->getValue());

        $component = Livewire::test(RolesRelationManager::class, [
            'ownerRecord' => $user,
            'pageClass' => \App\Filament\Resources\UserResource\Pages\EditUser::class,
        ])->assertSuccessful();

        $table = $component->instance()->getTable();

        $this->assertInstanceOf(AttachAction::class, $table->getHeaderActions()[0]);
        $this->assertInstanceOf(DetachAction::class, $table->getActions()[0]);
        $this->assertInstanceOf(DetachBulkAction::class, $table->getBulkActions()[0]);
        $this->assertSame('Attribuer un rôle existant', $table->getHeaderActions()[0]->getLabel());
        $this->assertSame('Retirer', $table->getActions()[0]->getLabel());
        $this->assertSame('Retirer la sélection', $table->getBulkActions()[0]->getLabel());
        $this->assertSame(['teacher'], $user->fresh()->getRoleNames()->all());
    }
}
