<?php

declare(strict_types=1);

use App\Domains\System\Enums\AuditAction;
use App\Domains\User\DTOs\PasswordChangeCommand;
use App\Domains\User\Enums\PasswordChangeFailure;
use App\Domains\User\Exceptions\PasswordChangeException;
use App\Domains\User\Services\UserPasswordChangeService;
use App\Filament\Pages\ChangePassword;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->schoolId = makeTenant('Password Change School', 'active', 'password-change.nexusschool.test');
    useTenantHost($this->schoolId);

    Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

    $this->user = makeRoleUser($this->schoolId, 'teacher');
    $this->user->update(['password' => Hash::make('AncienMotDePasse1!')]);
});

function insertSessionFor(User $user, string $id): void
{
    DB::table('sessions')->insert([
        'id' => $id,
        'user_id' => $user->getKey(),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'payload',
        'last_activity' => time(),
    ]);
}

test('a user can change their own password after providing the current password', function (): void {
    insertSessionFor($this->user, 'current-session');
    insertSessionFor($this->user, 'other-session');

    $result = app(UserPasswordChangeService::class)->change(new PasswordChangeCommand(
        actor: $this->user,
        currentPassword: 'AncienMotDePasse1!',
        newPassword: 'NouveauMotDePasse2!',
        newPasswordConfirmation: 'NouveauMotDePasse2!',
        currentSessionId: 'current-session',
    ));

    expect(Hash::check('NouveauMotDePasse2!', $this->user->fresh()->password))->toBeTrue()
        ->and(DB::table('sessions')->where('id', 'other-session')->exists())->toBeFalse()
        ->and(DB::table('sessions')->where('id', 'current-session')->exists())->toBeTrue()
        ->and($this->user->fresh()->remember_token)->toBeNull()
        ->and(AuditLog::query()->where('action', AuditAction::PasswordChanged->value)->count())->toBe(1);
});

test('an incorrect current password is rejected and the password is unchanged', function (): void {
    try {
        app(UserPasswordChangeService::class)->change(new PasswordChangeCommand(
            actor: $this->user,
            currentPassword: 'MauvaisMotDePasse1!',
            newPassword: 'NouveauMotDePasse2!',
            newPasswordConfirmation: 'NouveauMotDePasse2!',
        ));
        $this->fail('La modification aurait dû être refusée.');
    } catch (PasswordChangeException $exception) {
        expect($exception->failure)->toBe(PasswordChangeFailure::CurrentPassword)
            ->and($exception->getMessage())->toBe('Le mot de passe actuel est incorrect.');
    }

    expect(Hash::check('AncienMotDePasse1!', $this->user->fresh()->password))->toBeTrue()
        ->and(AuditLog::query()->where('action', AuditAction::SensitiveOperationRejected->value)->count())->toBe(1)
        ->and(AuditLog::query()->where('action', AuditAction::PasswordChanged->value)->count())->toBe(0);
});

test('password confirmation and minimum length failures are rejected', function (): void {
    try {
        app(UserPasswordChangeService::class)->change(new PasswordChangeCommand(
            actor: $this->user,
            currentPassword: 'AncienMotDePasse1!',
            newPassword: 'court',
            newPasswordConfirmation: 'autre',
        ));
        $this->fail('La validation aurait dû échouer.');
    } catch (PasswordChangeException $exception) {
        expect($exception->failure)->toBe(PasswordChangeFailure::Validation)
            ->and(implode(' ', $exception->validationMessages))
            ->toContain('au moins 8 caractères')
            ->toContain('confirmation du nouveau mot de passe ne correspond pas');
    }

    expect(Hash::check('AncienMotDePasse1!', $this->user->fresh()->password))->toBeTrue();
});

test('the personal password change feature cannot target another user', function (): void {
    $other = makeRoleUser($this->schoolId, 'teacher');
    $other->update(['password' => Hash::make('MotDePasseDAutre1!')]);

    try {
        app(UserPasswordChangeService::class)->change(new PasswordChangeCommand(
            actor: $this->user,
            currentPassword: 'MotDePasseDAutre1!',
            newPassword: 'NouveauMotDePasse2!',
            newPasswordConfirmation: 'NouveauMotDePasse2!',
        ));
        $this->fail('Le mot de passe d’un autre utilisateur ne doit pas pouvoir être utilisé.');
    } catch (PasswordChangeException $exception) {
        expect($exception->failure)->toBe(PasswordChangeFailure::CurrentPassword);
    }

    expect(Hash::check('MotDePasseDAutre1!', $other->fresh()->password))->toBeTrue()
        ->and(Hash::check('AncienMotDePasse1!', $this->user->fresh()->password))->toBeTrue();
});

test('the change result never exposes the stored hash', function (): void {
    $hash = (string) $this->user->password;

    $result = app(UserPasswordChangeService::class)->change(new PasswordChangeCommand(
        actor: $this->user,
        currentPassword: 'AncienMotDePasse1!',
        newPassword: 'NouveauMotDePasse2!',
        newPasswordConfirmation: 'NouveauMotDePasse2!',
    ));

    $serialized = json_encode($result, JSON_THROW_ON_ERROR);

    expect($serialized)->not->toContain($hash)
        ->and($serialized)->not->toContain('NouveauMotDePasse2!')
        ->and(array_keys($result->user->toArray()))->not->toContain('password');
});

test('the French Filament page changes only the authenticated user password', function (): void {
    $this->actingAs($this->user);
    Filament::setServingStatus();
    Filament::setCurrentPanel('school-admin');
    Tenancy::initialize(\App\Domains\School\Models\School::query()->findOrFail($this->schoolId));

    Livewire::test(ChangePassword::class)
        ->set('data.current_password', 'AncienMotDePasse1!')
        ->set('data.new_password', 'NouveauMotDePasse3!')
        ->set('data.new_password_confirmation', 'NouveauMotDePasse3!')
        ->call('save')
        ->assertHasNoErrors()
        ->assertNotified('Mot de passe modifié');

    expect(Hash::check('NouveauMotDePasse3!', $this->user->fresh()->password))->toBeTrue()
        ->and(AuditLog::query()->where('action', AuditAction::PasswordChanged->value)->count())->toBe(1);
});
