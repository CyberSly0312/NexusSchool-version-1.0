<?php

namespace Tests\Unit\Domains\Library;

use App\Domains\Library\DTOs\AuthorData;
use App\Domains\Library\DTOs\BookCategoryData;
use App\Domains\Library\DTOs\BookCopyData;
use App\Domains\Library\DTOs\BookData;
use App\Domains\Library\DTOs\PublisherData;
use App\Domains\Library\Services\LibraryService;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LibraryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LibraryService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new LibraryService;

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

    public function test_can_create_author(): void
    {
        $data = AuthorData::fromArray([
            'school_id' => $this->schoolId,
            'first_name' => 'Chinua',
            'last_name' => 'Achebe',
        ]);

        $author = $this->service->createAuthor($data);

        $this->assertInstanceOf(Author::class, $author);
        $this->assertEquals('Chinua', $author->first_name);
        $this->assertEquals('Achebe', $author->last_name);
        $this->assertEquals($this->schoolId, $author->school_id);
    }

    public function test_can_update_author(): void
    {
        $author = Author::factory()
            ->create(['school_id' => $this->schoolId, 'first_name' => 'Chinua']);

        $updated = $this->service->updateAuthor($author, ['first_name' => 'Chinedu']);

        $this->assertEquals('Chinedu', $updated->first_name);
    }

    public function test_can_delete_author(): void
    {
        $author = Author::factory()
            ->create(['school_id' => $this->schoolId]);

        $deleted = $this->service->deleteAuthor($author);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('authors', ['id' => $author->id]);
    }

    public function test_duplicate_author_name_is_rejected(): void
    {
        Author::factory()->create([
            'school_id' => $this->schoolId,
            'first_name' => 'Chinua',
            'last_name' => 'Achebe',
        ]);

        $data = AuthorData::fromArray([
            'school_id' => $this->schoolId,
            'first_name' => 'Chinua',
            'last_name' => 'Achebe',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->createAuthor($data);
    }

    public function test_can_create_publisher(): void
    {
        $data = PublisherData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Penguin Books',
        ]);

        $publisher = $this->service->createPublisher($data);

        $this->assertInstanceOf(Publisher::class, $publisher);
        $this->assertEquals('Penguin Books', $publisher->name);
        $this->assertEquals($this->schoolId, $publisher->school_id);
    }

    public function test_can_update_publisher(): void
    {
        $publisher = Publisher::factory()
            ->create(['school_id' => $this->schoolId, 'name' => 'Penguin Books']);

        $updated = $this->service->updatePublisher($publisher, ['name' => 'Penguin Classics']);

        $this->assertEquals('Penguin Classics', $updated->name);
    }

    public function test_can_create_book_category(): void
    {
        $data = BookCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Fiction',
        ]);

        $category = $this->service->createBookCategory($data);

        $this->assertInstanceOf(BookCategory::class, $category);
        $this->assertEquals('Fiction', $category->name);
        $this->assertEquals($this->schoolId, $category->school_id);
    }

    public function test_can_create_book(): void
    {
        $data = BookData::fromArray([
            'school_id' => $this->schoolId,
            'title' => 'Things Fall Apart',
            'isbn' => '978-0-435-91352-0',
            'language' => 'English',
        ]);

        $book = $this->service->createBook($data);

        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals('Things Fall Apart', $book->title);
        $this->assertEquals('978-0-435-91352-0', $book->isbn);
        $this->assertEquals($this->schoolId, $book->school_id);
    }

    public function test_can_update_book(): void
    {
        $book = Book::factory()->create([
            'school_id' => $this->schoolId,
            'title' => 'Things Fall Apart',
        ]);

        $updated = $this->service->updateBook($book, ['title' => 'Things Fall Apart (Revised)']);

        $this->assertEquals('Things Fall Apart (Revised)', $updated->title);
    }

    public function test_duplicate_isbn_is_rejected(): void
    {
        Book::factory()->create([
            'school_id' => $this->schoolId,
            'isbn' => '978-0-435-91352-0',
        ]);

        $data = BookData::fromArray([
            'school_id' => $this->schoolId,
            'title' => 'Another Book',
            'isbn' => '978-0-435-91352-0',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->createBook($data);
    }

    public function test_can_create_book_copy(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);

        $data = BookCopyData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'barcode' => 'BC-00001',
            'inventory_number' => 'INV-00001',
        ]);

        $copy = $this->service->createBookCopy($data);

        $this->assertInstanceOf(BookCopy::class, $copy);
        $this->assertEquals('BC-00001', $copy->barcode);
        $this->assertEquals('INV-00001', $copy->inventory_number);
        $this->assertEquals($book->id, $copy->book_id);
    }

    public function test_duplicate_barcode_is_rejected(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);

        BookCopy::factory()->create([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'barcode' => 'BC-DUPLICATE',
            'inventory_number' => 'INV-00001',
        ]);

        $data = BookCopyData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'barcode' => 'BC-DUPLICATE',
            'inventory_number' => 'INV-00002',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->createBookCopy($data);
    }

    public function test_duplicate_inventory_number_is_rejected(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);

        BookCopy::factory()->create([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'barcode' => 'BC-00001',
            'inventory_number' => 'INV-DUPLICATE',
        ]);

        $data = BookCopyData::fromArray([
            'school_id' => $this->schoolId,
            'book_id' => $book->id,
            'barcode' => 'BC-00002',
            'inventory_number' => 'INV-DUPLICATE',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->createBookCopy($data);
    }

    public function test_can_paginate_books(): void
    {
        Book::factory()
            ->count(5)
            ->create(['school_id' => $this->schoolId]);

        $result = $this->service->paginateBooks([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_search_books_by_title(): void
    {
        Book::factory()->create([
            'school_id' => $this->schoolId,
            'title' => 'Things Fall Apart',
        ]);
        Book::factory()
            ->count(3)
            ->create(['school_id' => $this->schoolId]);

        $result = $this->service->paginateBooks(['search' => 'Things Fall'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_books_by_isbn(): void
    {
        Book::factory()->create([
            'school_id' => $this->schoolId,
            'isbn' => '978-1234-5678-9',
        ]);
        Book::factory()
            ->count(3)
            ->create(['school_id' => $this->schoolId]);

        $result = $this->service->paginateBooks(['search' => '978-1234'], 10);

        $this->assertEquals(1, $result->total());
    }
}
