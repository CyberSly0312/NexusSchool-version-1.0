<?php

declare(strict_types=1);

use App\Domains\CertificateAndTranscript\Domain\Enums\CompletionDocumentKind;
use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->schoolId = makeTenant();

    foreach (['view', 'create', 'update', 'generate', 'review', 'approve', 'publish', 'archive'] as $action) {
        Permission::firstOrCreate(['name' => "certificate-and-transcript.{$action}", 'guard_name' => 'web']);
    }
});

function completionGrantPermission(string $permission, string $schoolId): User
{
    $role = Role::create(['name' => 'ct-mgr-'.Str::random(8), 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);

    $user = User::factory()->create(['school_id' => $schoolId]);
    $user->assignRole($role);

    return $user;
}

function completionRequestFor(string $schoolId, bool $generated = false): CompletionRequest
{
    return CompletionRequest::factory()
        ->forSchool($schoolId)
        ->state(fn (): array => ['completion_document_kind' => CompletionDocumentKind::Transcript])
        ->when((bool) $generated, fn ($factory) => $factory->generated())
        ->create();
}

test('user with the view permission can list completion requests', function (): void {
    $user = completionGrantPermission('certificate-and-transcript.view', $this->schoolId);

    expect($user->can('viewAny', CompletionRequest::class))->toBeTrue();
});

test('user without permission cannot list completion requests', function (): void {
    $user = User::factory()->create(['school_id' => $this->schoolId]);

    expect($user->can('viewAny', CompletionRequest::class))->toBeFalse();
});

test('completion request access is school scoped', function (): void {
    $otherSchool = makeTenant();
    $request = completionRequestFor($this->schoolId);

    $owner = completionGrantPermission('certificate-and-transcript.view', $this->schoolId);
    $outsider = completionGrantPermission('certificate-and-transcript.view', $otherSchool);

    expect($owner->can('view', $request))->toBeTrue()
        ->and($outsider->can('view', $request))->toBeFalse();
});

test('each lifecycle capability requires its dedicated permission', function (): void {
    $request = completionRequestFor($this->schoolId);

    $withCreate = completionGrantPermission('certificate-and-transcript.create', $this->schoolId);
    $withUpdate = completionGrantPermission('certificate-and-transcript.update', $this->schoolId);
    $withGenerate = completionGrantPermission('certificate-and-transcript.generate', $this->schoolId);

    expect($withCreate->can('create', CompletionRequest::class))->toBeTrue()
        ->and($withCreate->can('update', $request))->toBeFalse()
        ->and($withUpdate->can('update', $request))->toBeTrue()
        ->and($withGenerate->can('generate', $request))->toBeTrue()
        ->and($withGenerate->can('publish', $request))->toBeFalse();
});

test('publishing, reviewing and approving need their own capabilities', function (): void {
    $request = completionRequestFor($this->schoolId);

    $withReview = completionGrantPermission('certificate-and-transcript.review', $this->schoolId);
    $withApprove = completionGrantPermission('certificate-and-transcript.approve', $this->schoolId);
    $withPublish = completionGrantPermission('certificate-and-transcript.publish', $this->schoolId);

    expect($withReview->can('review', $request))->toBeTrue()
        ->and($withReview->can('approve', $request))->toBeFalse()
        ->and($withApprove->can('approve', $request))->toBeTrue()
        ->and($withApprove->can('publish', $request))->toBeFalse()
        ->and($withPublish->can('publish', $request))->toBeTrue()
        ->and($withPublish->can('generate', $request))->toBeFalse();
});

test('archiving is a dedicated capability', function (): void {
    $request = completionRequestFor($this->schoolId, generated: true);

    $withArchive = completionGrantPermission('certificate-and-transcript.archive', $this->schoolId);

    expect($withArchive->can('archive', $request))->toBeTrue()
        ->and($withArchive->can('publish', $request))->toBeFalse();
});

test('completion capabilities are school scoped', function (): void {
    $otherSchool = makeTenant();
    $request = completionRequestFor($this->schoolId);

    $outsider = completionGrantPermission('certificate-and-transcript.publish', $otherSchool);

    expect($outsider->can('publish', $request))->toBeFalse();
});

test('super_admin bypasses school scoping', function (): void {
    $otherSchool = makeTenant();
    $request = completionRequestFor($otherSchool);

    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $superAdmin = User::factory()->create(['school_id' => $this->schoolId]);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('view', $request))->toBeTrue()
        ->and($superAdmin->can('generate', $request))->toBeTrue()
        ->and($superAdmin->can('review', $request))->toBeTrue()
        ->and($superAdmin->can('approve', $request))->toBeTrue()
        ->and($superAdmin->can('publish', $request))->toBeTrue()
        ->and($superAdmin->can('archive', $request))->toBeTrue();
});

test('completion requests are never deleted, restored or force deleted', function (): void {
    $request = completionRequestFor($this->schoolId);

    $user = completionGrantPermission('certificate-and-transcript.view', $this->schoolId);

    expect($user->can('delete', $request))->toBeFalse()
        ->and($user->can('restore', $request))->toBeFalse()
        ->and($user->can('forceDelete', $request))->toBeFalse();
});
