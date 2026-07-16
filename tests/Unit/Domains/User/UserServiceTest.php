<?php

namespace Tests\Unit\Domains\User;

use App\Domains\User\DTOs\UserData;
use App\Domains\User\Enums\UserStatus;
use App\Domains\User\Services\UserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService;
    }

    public function test_can_create_user(): void
    {
        $data = UserData::fromArray([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'status' => 'active',
        ]);

        $user = $this->service->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals(UserStatus::Active, $user->status);
        $this->assertNull($user->school_id);
    }

    public function test_can_create_user_with_school(): void
    {
        $schoolId = DB::table('schools')->insertGetId([
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school',
            'database' => 'school_test',
            'status' => 'active',
            'school_type' => 'secondary',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'locale' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = UserData::fromArray([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => 'secret123',
            'school_id' => $schoolId,
            'status' => 'active',
        ]);

        $user = $this->service->create($data);

        $this->assertEquals($schoolId, $user->school_id);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $updated = $this->service->update($user, [
            'first_name' => 'Jane',
        ]);

        $this->assertEquals('Jane', $updated->first_name);
        $this->assertEquals('Doe', $updated->last_name);
    }

    public function test_can_toggle_status(): void
    {
        $user = User::factory()->active()->create();

        $toggled = $this->service->toggleStatus($user);

        $this->assertEquals(UserStatus::Inactive, $toggled->status);
    }

    public function test_can_toggle_status_from_inactive_to_active(): void
    {
        $user = User::factory()->inactive()->create();

        $toggled = $this->service->toggleStatus($user);

        $this->assertEquals(UserStatus::Active, $toggled->status);
    }

    public function test_can_paginate_users(): void
    {
        User::factory()->count(5)->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_users_by_status(): void
    {
        User::factory()->count(3)->active()->create();
        User::factory()->count(2)->inactive()->create();

        $result = $this->service->paginate(['status' => 'active'], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_users(): void
    {
        User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

        $result = $this->service->paginate(['search' => 'John'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_soft_delete_user(): void
    {
        $user = User::factory()->create();

        $deleted = $this->service->delete($user);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_can_restore_user(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $restored = $this->service->restore($user);

        $this->assertNotSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_full_name_accessor(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $user->full_name);
    }
}
