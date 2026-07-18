<?php

namespace Tests\Unit\Domains\Library;

use App\Domains\Library\DTOs\FineData;
use App\Domains\Library\Enums\FineStatus;
use App\Domains\Library\Services\FineService;
use App\Models\Fine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FineService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new FineService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->schoolId = $schoolId;
    }

    public function test_can_create_fine(): void
    {
        $data = FineData::fromArray([
            'school_id' => $this->schoolId,
            'borrower_id' => 1,
            'borrower_type' => 'student',
            'reason' => 'Overdue book',
            'amount' => 10.00,
        ]);

        $fine = $this->service->createFine($data);

        $this->assertInstanceOf(Fine::class, $fine);
        $this->assertEquals('Overdue book', $fine->reason);
        $this->assertEquals(10.00, (float) $fine->amount);
        $this->assertEquals(FineStatus::Pending, $fine->status);
        $this->assertEquals($this->schoolId, $fine->school_id);
    }

    public function test_can_pay_fine_in_full(): void
    {
        $fine = Fine::factory()->pending()->create([
            'school_id' => $this->schoolId,
            'amount' => 25.00,
            'paid_amount' => 0,
        ]);

        $paid = $this->service->payFine($fine, 25.00, 'cash', 'PAY-001');

        $this->assertEquals(FineStatus::Paid, $paid->status);
        $this->assertEquals(25.00, (float) $paid->paid_amount);
        $this->assertNotNull($paid->paid_date);
        $this->assertEquals('cash', $paid->payment_method);
        $this->assertEquals('PAY-001', $paid->reference_number);
    }

    public function test_can_pay_fine_partially(): void
    {
        $fine = Fine::factory()->pending()->create([
            'school_id' => $this->schoolId,
            'amount' => 50.00,
            'paid_amount' => 0,
        ]);

        $paid = $this->service->payFine($fine, 20.00, 'card');

        $this->assertEquals(FineStatus::PartiallyPaid, $paid->status);
        $this->assertEquals(20.00, (float) $paid->paid_amount);
        $this->assertEquals(30.00, (float) $paid->balance);
    }

    public function test_cannot_overpay_fine(): void
    {
        $fine = Fine::factory()->pending()->create([
            'school_id' => $this->schoolId,
            'amount' => 20.00,
            'paid_amount' => 0,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->payFine($fine, 25.00);
    }

    public function test_can_waive_fine(): void
    {
        $fine = Fine::factory()->pending()->create([
            'school_id' => $this->schoolId,
            'amount' => 30.00,
            'paid_amount' => 0,
        ]);

        $waived = $this->service->waiveFine($fine, 1, 'Student excused');

        $this->assertEquals(FineStatus::Waived, $waived->status);
        $this->assertNotNull($waived->waived_date);
        $this->assertEquals(1, $waived->processed_by);
    }

    public function test_cannot_pay_already_paid_fine(): void
    {
        $fine = Fine::factory()->paid()->create([
            'school_id' => $this->schoolId,
            'amount' => 15.00,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->payFine($fine, 15.00);
    }

    public function test_can_delete_fine(): void
    {
        $fine = Fine::factory()->create([
            'school_id' => $this->schoolId,
        ]);

        $deleted = $this->service->deleteFine($fine);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('fines', ['id' => $fine->id]);
    }

    public function test_can_paginate_fines(): void
    {
        Fine::factory()
            ->count(5)
            ->create(['school_id' => $this->schoolId]);

        $result = $this->service->paginateFines([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_outstanding_fines_are_calculated(): void
    {
        $borrowerId = 42;

        Fine::factory()->pending()->create([
            'school_id' => $this->schoolId,
            'borrower_id' => $borrowerId,
            'borrower_type' => 'student',
            'amount' => 100.00,
            'paid_amount' => 0,
        ]);

        Fine::factory()->partiallyPaid()->create([
            'school_id' => $this->schoolId,
            'borrower_id' => $borrowerId,
            'borrower_type' => 'student',
            'amount' => 60.00,
            'paid_amount' => 30.00,
        ]);

        Fine::factory()->paid()->create([
            'school_id' => $this->schoolId,
            'borrower_id' => $borrowerId,
            'borrower_type' => 'student',
            'amount' => 25.00,
        ]);

        $outstanding = $this->service->getBorrowerOutstandingFines($borrowerId, 'student');

        $this->assertEquals(130.00, $outstanding);
    }
}
