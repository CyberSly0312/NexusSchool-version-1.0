<?php

namespace Tests\Unit\Domains\Library;

use App\Domains\Library\DTOs\BookLoanData;
use App\Domains\Library\DTOs\ReservationData;
use App\Domains\Library\Enums\BookCopyStatus;
use App\Domains\Library\Enums\BookLoanStatus;
use App\Domains\Library\Services\CirculationService;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookLoan;
use App\Models\Reservation;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CirculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CirculationService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CirculationService;

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

    protected function createAvailableCopy(): BookCopy
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);

        return BookCopy::factory()->available()->create([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
        ]);
    }

    protected function createBorrower(): Student
    {
        return Student::factory()->create(['school_id' => $this->schoolId]);
    }

    public function test_can_borrow_book(): void
    {
        $copy = $this->createAvailableCopy();
        $borrower = $this->createBorrower();

        $data = BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]);

        $loan = $this->service->borrowBook($data);

        $this->assertInstanceOf(BookLoan::class, $loan);
        $this->assertEquals(BookLoanStatus::Active, $loan->status);
        $this->assertEquals($copy->id, $loan->book_copy_id);

        $copy->refresh();
        $this->assertEquals(BookCopyStatus::Borrowed, $copy->status);
        $this->assertFalse($copy->is_available);
    }

    public function test_cannot_borrow_unavailable_copy(): void
    {
        $copy = $this->createAvailableCopy();
        $borrower = $this->createBorrower();

        $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $otherBorrower = $this->createBorrower();

        $this->expectException(ValidationException::class);

        $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $otherBorrower->id,
            'borrower_type' => 'student',
        ]));
    }

    public function test_cannot_borrow_lost_book(): void
    {
        $copy = $this->createAvailableCopy();
        $copy->update(['status' => BookCopyStatus::Archived, 'is_available' => false]);
        $borrower = $this->createBorrower();

        $this->expectException(ValidationException::class);

        $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));
    }

    public function test_can_return_book(): void
    {
        $copy = $this->createAvailableCopy();
        $borrower = $this->createBorrower();

        $loan = $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $returned = $this->service->returnBook($loan);

        $this->assertEquals(BookLoanStatus::Returned, $returned->status);
        $this->assertNotNull($returned->return_date);

        $copy->refresh();
        $this->assertEquals(BookCopyStatus::Returned, $copy->status);
        $this->assertTrue($copy->is_available);
    }

    public function test_cannot_return_already_returned_loan(): void
    {
        $copy = $this->createAvailableCopy();
        $borrower = $this->createBorrower();

        $loan = $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $this->service->returnBook($loan);

        $this->expectException(ValidationException::class);

        $this->service->returnBook($loan->refresh());
    }

    public function test_can_mark_lost(): void
    {
        $copy = $this->createAvailableCopy();
        $borrower = $this->createBorrower();

        $loan = $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $lost = $this->service->markLost($loan);

        $this->assertEquals(BookLoanStatus::Lost, $lost->status);

        $copy->refresh();
        $this->assertEquals(BookCopyStatus::Archived, $copy->status);
        $this->assertFalse($copy->is_available);
    }

    public function test_can_renew_loan(): void
    {
        $copy = $this->createAvailableCopy();
        $borrower = $this->createBorrower();

        $loan = $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $originalDueDate = $loan->due_date->copy();

        $renewed = $this->service->renewLoan($loan);

        $this->assertEquals(1, $renewed->renewal_count);
        $this->assertTrue($renewed->due_date->greaterThan($originalDueDate));
    }

    public function test_cannot_renew_beyond_limit(): void
    {
        $copy = $this->createAvailableCopy();
        $borrower = $this->createBorrower();

        $loan = $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $this->service->renewLoan($loan);
        $this->service->renewLoan($loan->refresh());
        $this->service->renewLoan($loan->refresh());

        $this->expectException(ValidationException::class);

        $this->service->renewLoan($loan->refresh());
    }

    public function test_can_create_reservation(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);
        $borrower = $this->createBorrower();

        $data = ReservationData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]);

        $reservation = $this->service->createReservation($data);

        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertEquals('pending', $reservation->status->value);
        $this->assertEquals($book->id, $reservation->book_id);
    }

    public function test_duplicate_pending_reservation_is_rejected(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);
        $borrower = $this->createBorrower();

        $this->service->createReservation(ReservationData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $this->expectException(ValidationException::class);

        $this->service->createReservation(ReservationData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));
    }

    public function test_can_fulfill_reservation(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);
        $borrower = $this->createBorrower();

        $reservation = $this->service->createReservation(ReservationData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $fulfilled = $this->service->fulfillReservation($reservation);

        $this->assertEquals('fulfilled', $fulfilled->status->value);
        $this->assertNotNull($fulfilled->fulfilled_date);
    }

    public function test_can_cancel_reservation(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);
        $borrower = $this->createBorrower();

        $reservation = $this->service->createReservation(ReservationData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $cancelled = $this->service->cancelReservation($reservation);

        $this->assertEquals('cancelled', $cancelled->status->value);
    }

    public function test_active_loans_are_tracked(): void
    {
        $borrower = $this->createBorrower();
        $copy1 = $this->createAvailableCopy();
        $copy2 = $this->createAvailableCopy();

        $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy1->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $this->service->borrowBook(BookLoanData::fromArray([
            'school_id' => $this->schoolId,
            'book_copy_id' => $copy2->id,
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
        ]));

        $activeLoans = $this->service->paginateLoans([
            'borrower_id' => $borrower->id,
            'borrower_type' => 'student',
            'status' => BookLoanStatus::Active,
        ]);

        $this->assertEquals(2, $activeLoans->total());
    }
}
