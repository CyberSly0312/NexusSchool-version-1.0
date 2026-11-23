<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\PanelAccessRegistry;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    RateLimiter::clear('school-gateway-login:ip:127.0.0.1');
});

function gatewayTenant(string $name = 'Gateway School'): string
{
    $schoolId = makeTenant($name);

    return $schoolId;
}

function gatewayUser(string $schoolId, array $roles = [], array $attributes = []): User
{
    $user = User::factory()->create(array_merge([
        'school_id' => $schoolId,
        'status' => \App\Domains\User\Enums\UserStatus::Active,
        'password' => 'password123',
    ], $attributes));

    foreach ($roles as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($role);
    }

    return $user;
}

test('successful login redirects single-role user to authorized panel', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['director']);

    $response = $this->post(route('school.gateway.login.submit'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('http://'.tenantHost($schoolId).'/director');
    $this->assertAuthenticatedAs($user);
});

test('invalid credentials are rejected', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['director']);

    $response = $this->from(route('school.gateway.login'))->post(route('school.gateway.login.submit'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('school.gateway.login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login does not require role selection', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['teacher']);

    $response = $this->post(route('school.gateway.login.submit'), [
        'email' => $user->email,
        'password' => 'password123',
        'role' => 'director',
        'panel' => 'director',
    ]);

    $response->assertRedirect('http://'.tenantHost($schoolId).'/teacher');
    $this->assertAuthenticatedAs($user);
});

test('multi-role user sees only authorized panel selector', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['director', 'teacher']);

    $this->post(route('school.gateway.login.submit'), [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirect(route('school.gateway.select-panel'));

    $response = $this->actingAs($user)->get(route('school.gateway.select-panel'));

    $response->assertOk();
    $response->assertSee('Director');
    $response->assertSee('Teacher');
    $response->assertDontSee('School Admin');
    $response->assertDontSee('Super Admin');
});

test('unauthorized panel selection is rejected', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['teacher']);

    $response = $this->actingAs($user)->post(route('school.gateway.enter-panel'), [
        'panel' => 'director',
    ]);

    $response->assertForbidden();
});

test('direct unauthorized panel URL is rejected', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['teacher']);

    $response = $this->actingAs($user)->get('http://'.tenantHost($schoolId).'/director');

    $response->assertForbidden();
});

test('unmapped role is denied', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['librarian']);

    $this->post(route('school.gateway.login.submit'), [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirect(route('school.gateway.no-access'));

    $response = $this->actingAs($user)->get(route('school.gateway.no-access'));

    $response->assertOk();
    $response->assertSee(__('Your account is authenticated, but no authorized panel is available for this school.'), false);
});

test('cross-tenant login context is denied for authenticated user on another tenant host', function (): void {
    $schoolA = gatewayTenant('School A');
    $schoolB = gatewayTenant('School B');
    $userA = gatewayUser($schoolA, ['director']);

    useTenantHost($schoolB);

    $response = $this->actingAs($userA)->get('http://'.tenantHost($schoolB).'/login');

    $response->assertForbidden();
});

test('missing tenant fails closed', function (): void {
    $response = $this->get('http://unknown-school.nexusschool.test/login');

    $response->assertNotFound();
});

test('session is regenerated on login and invalidated on logout', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['director']);

    $this->get(route('school.gateway.login'));
    $oldSessionId = session()->getId();

    $this->post(route('school.gateway.login.submit'), [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirect();

    expect(session()->getId())->not->toBe($oldSessionId);
    $this->assertAuthenticatedAs($user);

    $loggedInSessionId = session()->getId();
    $this->post(route('school.gateway.logout'))->assertRedirect(route('school.gateway.login'));

    expect(session()->getId())->not->toBe($loggedInSessionId);
    $this->assertGuest();
});

test('login throttling blocks repeated failed attempts', function (): void {
    $schoolId = gatewayTenant();
    $user = gatewayUser($schoolId, ['director']);

    foreach (range(1, 5) as $attempt) {
        $this->from(route('school.gateway.login'))->post(route('school.gateway.login.submit'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->from(route('school.gateway.login'))->post(route('school.gateway.login.submit'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('school.gateway.login'));
    $response->assertSessionHasErrors('email');
    expect((string) session('errors')->first('email'))->toMatch('/Too many login attempts|Trop de tentatives/');
});

test('existing panel access registry preserves legacy role mapping', function (): void {
    $schoolId = gatewayTenant();
    $admin = gatewayUser($schoolId, ['school_admin']);
    $registry = app(PanelAccessRegistry::class);

    expect($registry->canUserAccessPanel($admin, 'school-admin'))->toBeTrue();
    expect($registry->canUserAccessPanel($admin, 'admin'))->toBeTrue();
    expect($registry->canUserAccessPanel($admin, 'director'))->toBeFalse();
});

test('existing tenant isolation remains enforced on panel routes', function (): void {
    $schoolA = gatewayTenant('School A');
    $schoolB = gatewayTenant('School B');
    $userA = gatewayUser($schoolA, ['director']);

    useTenantHost($schoolB);

    $response = $this->actingAs($userA)->get('http://'.tenantHost($schoolB).'/director');

    $response->assertForbidden();
});

test('gateway login page is available on tenant host and does not require role selection', function (): void {
    gatewayTenant();

    $response = $this->get(route('school.gateway.login'));

    $response->assertOk();
    $response->assertSee(__('Sign in'));
    $response->assertDontSee('Select the profile', false);
    $response->assertDontSee('name="role"', false);
    $response->assertDontSee('name="panel"', false);
});
