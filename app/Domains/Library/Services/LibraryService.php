<?php

declare(strict_types=1);

namespace App\Domains\Library\Services;

use App\Domains\Library\DTOs\AuthorData;
use App\Domains\Library\DTOs\BookCategoryData;
use App\Domains\Library\DTOs\BookCopyData;
use App\Domains\Library\DTOs\BookData;
use App\Domains\Library\DTOs\PublisherData;
use App\Domains\Library\Enums\AuthorStatus;
use App\Domains\Library\Enums\BookCopyStatus;
use App\Domains\Library\Enums\BookStatus;
use App\Domains\Library\Events\BookCreated;
use App\Domains\Library\Rules\AuthorRules;
use App\Domains\Library\Rules\BookCategoryRules;
use App\Domains\Library\Rules\BookCopyRules;
use App\Domains\Library\Rules\BookRules;
use App\Domains\Library\Rules\PublisherRules;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\Publisher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LibraryService
{
    public function __construct(
        protected Book $bookModel = new Book(),
        protected Author $authorModel = new Author(),
        protected Publisher $publisherModel = new Publisher(),
        protected BookCategory $categoryModel = new BookCategory(),
        protected BookCopy $copyModel = new BookCopy(),
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Author CRUD
    |--------------------------------------------------------------------------
    */

    public function createAuthor(AuthorData $data): Author
    {
        $validated = AuthorRules::validate($data->toArray(), 'create');

        $this->validateUniqueAuthorPerSchool($validated['school_id'], $validated['first_name'], $validated['last_name']);

        return DB::transaction(function () use ($validated) {
            $author = $this->authorModel->create($validated);

            return $author;
        });
    }

    public function updateAuthor(Author $author, array $data): Author
    {
        $validated = AuthorRules::validate($data, 'update', $author->id);

        if (isset($validated['first_name'], $validated['last_name'])) {
            $this->validateUniqueAuthorPerSchool($validated['school_id'] ?? $author->school_id, $validated['first_name'], $validated['last_name'], $author->id);
        }

        $author->update($validated);

        return $author->refresh();
    }

    public function archiveAuthor(Author $author): Author
    {
        $author->update(['status' => AuthorStatus::Inactive]);

        return $author->refresh();
    }

    public function activateAuthor(Author $author): Author
    {
        $author->update(['status' => AuthorStatus::Active]);

        return $author->refresh();
    }

    public function deleteAuthor(Author $author): bool
    {
        $author->delete();

        return true;
    }

    public function findAuthorById(int $id): Author
    {
        return $this->authorModel->findOrFail($id);
    }

    public function paginateAuthors(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->authorModel->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->orderBy('last_name')->orderBy('first_name')->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | Publisher CRUD
    |--------------------------------------------------------------------------
    */

    public function createPublisher(PublisherData $data): Publisher
    {
        $validated = PublisherRules::validate($data->toArray(), 'create');

        $this->validateUniquePublisherPerSchool($validated['school_id'], $validated['name']);

        return DB::transaction(function () use ($validated) {
            return $this->publisherModel->create($validated);
        });
    }

    public function updatePublisher(Publisher $publisher, array $data): Publisher
    {
        $validated = PublisherRules::validate($data, 'update', $publisher->id);

        if (isset($validated['name'])) {
            $this->validateUniquePublisherPerSchool($validated['school_id'] ?? $publisher->school_id, $validated['name'], $publisher->id);
        }

        $publisher->update($validated);

        return $publisher->refresh();
    }

    public function deletePublisher(Publisher $publisher): bool
    {
        $publisher->delete();

        return true;
    }

    public function findPublisherById(int $id): Publisher
    {
        return $this->publisherModel->findOrFail($id);
    }

    public function paginatePublishers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->publisherModel->newQuery();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | BookCategory CRUD
    |--------------------------------------------------------------------------
    */

    public function createBookCategory(BookCategoryData $data): BookCategory
    {
        $validated = BookCategoryRules::validate($data->toArray(), 'create');

        $this->validateUniqueCategoryPerSchool($validated['school_id'], $validated['name']);

        if (! empty($validated['parent_id'])) {
            $this->validateParentExists($validated['parent_id']);
        }

        return DB::transaction(function () use ($validated) {
            return $this->categoryModel->create($validated);
        });
    }

    public function updateBookCategory(BookCategory $category, array $data): BookCategory
    {
        $validated = BookCategoryRules::validate($data, 'update', $category->id);

        if (isset($validated['name'])) {
            $this->validateUniqueCategoryPerSchool($validated['school_id'] ?? $category->school_id, $validated['name'], $category->id);
        }

        if (isset($validated['parent_id']) && $validated['parent_id'] !== $category->parent_id) {
            $this->validateParentExists($validated['parent_id']);
            $this->validateNotSelfParent($category, $validated['parent_id']);
        }

        $category->update($validated);

        return $category->refresh();
    }

    public function deleteBookCategory(BookCategory $category): bool
    {
        $category->delete();

        return true;
    }

    public function findBookCategoryById(int $id): BookCategory
    {
        return $this->categoryModel->findOrFail($id);
    }

    public function paginateBookCategories(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->categoryModel->newQuery()->with('parent');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | Book CRUD
    |--------------------------------------------------------------------------
    */

    public function createBook(BookData $data): Book
    {
        $validated = BookRules::validate($data->toArray(), 'create');

        if (! empty($validated['isbn'])) {
            $this->validateUniqueIsbnPerSchool($validated['school_id'], $validated['isbn']);
        }

        $authorIds = $validated['author_ids'] ?? [];
        unset($validated['author_ids']);

        return DB::transaction(function () use ($validated, $authorIds) {
            $book = $this->bookModel->create($validated);

            if (! empty($authorIds)) {
                $book->authors()->sync($authorIds);
            }

            BookCreated::dispatch($book);

            return $book;
        });
    }

    public function updateBook(Book $book, array $data): Book
    {
        $validated = BookRules::validate($data, 'update', $book->id);

        if (isset($validated['isbn']) && ! empty($validated['isbn'])) {
            $this->validateUniqueIsbnPerSchool($validated['school_id'] ?? $book->school_id, $validated['isbn'], $book->id);
        }

        $authorIds = $validated['author_ids'] ?? null;
        unset($validated['author_ids']);

        $book->update($validated);

        if ($authorIds !== null) {
            $book->authors()->sync($authorIds);
        }

        return $book->refresh();
    }

    public function deleteBook(Book $book): bool
    {
        $book->delete();

        return true;
    }

    public function archiveBook(Book $book): Book
    {
        $book->update(['status' => BookStatus::Archived]);

        return $book->refresh();
    }

    public function activateBook(Book $book): Book
    {
        $book->update(['status' => BookStatus::Active]);

        return $book->refresh();
    }

    public function findBookById(int $id): Book
    {
        return $this->bookModel->with(['authors', 'publisher', 'category'])->findOrFail($id);
    }

    public function paginateBooks(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->bookModel->newQuery()->with(['authors', 'publisher', 'category']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['book_category_id'])) {
            $query->where('book_category_id', $filters['book_category_id']);
        }

        if (isset($filters['publisher_id'])) {
            $query->where('publisher_id', $filters['publisher_id']);
        }

        return $query->orderBy('title')->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | BookCopy CRUD
    |--------------------------------------------------------------------------
    */

    public function createBookCopy(BookCopyData $data): BookCopy
    {
        $validated = BookCopyRules::validate($data->toArray(), 'create');

        $this->validateUniqueBarcodePerSchool($validated['school_id'], $validated['barcode']);
        $this->validateUniqueInventoryNumberPerSchool($validated['school_id'], $validated['inventory_number']);

        return DB::transaction(function () use ($validated) {
            $copy = $this->copyModel->create($validated);

            return $copy;
        });
    }

    public function updateBookCopy(BookCopy $copy, array $data): BookCopy
    {
        $validated = BookCopyRules::validate($data, 'update', $copy->id);

        if (isset($validated['barcode'])) {
            $this->validateUniqueBarcodePerSchool($validated['school_id'] ?? $copy->school_id, $validated['barcode'], $copy->id);
        }

        if (isset($validated['inventory_number'])) {
            $this->validateUniqueInventoryNumberPerSchool($validated['school_id'] ?? $copy->school_id, $validated['inventory_number'], $copy->id);
        }

        $copy->update($validated);

        return $copy->refresh();
    }

    public function deleteBookCopy(BookCopy $copy): bool
    {
        $copy->delete();

        return true;
    }

    public function archiveBookCopy(BookCopy $copy): BookCopy
    {
        $copy->update(['status' => BookCopyStatus::Archived, 'is_available' => false]);

        return $copy->refresh();
    }

    public function findBookCopyById(int $id): BookCopy
    {
        return $this->copyModel->with('book')->findOrFail($id);
    }

    public function paginateBookCopies(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->copyModel->newQuery()->with('book');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                    ->orWhere('inventory_number', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['is_available'])) {
            $query->where('is_available', $filters['is_available']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | Private Validation Helpers
    |--------------------------------------------------------------------------
    */

    private function validateUniqueAuthorPerSchool(string $schoolId, string $firstName, string $lastName, ?int $excludeId = null): void
    {
        $query = $this->authorModel
            ->where('school_id', $schoolId)
            ->where('first_name', $firstName)
            ->where('last_name', $lastName);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'first_name' => 'An author with this name already exists in this school.',
            ]);
        }
    }

    private function validateUniquePublisherPerSchool(string $schoolId, string $name, ?int $excludeId = null): void
    {
        $query = $this->publisherModel
            ->where('school_id', $schoolId)
            ->where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A publisher with this name already exists in this school.',
            ]);
        }
    }

    private function validateUniqueCategoryPerSchool(string $schoolId, string $name, ?int $excludeId = null): void
    {
        $query = $this->categoryModel
            ->where('school_id', $schoolId)
            ->where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A category with this name already exists in this school.',
            ]);
        }
    }

    private function validateUniqueIsbnPerSchool(string $schoolId, string $isbn, ?int $excludeId = null): void
    {
        $query = $this->bookModel
            ->where('school_id', $schoolId)
            ->where('isbn', $isbn);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'isbn' => 'A book with this ISBN already exists in this school.',
            ]);
        }
    }

    private function validateUniqueBarcodePerSchool(string $schoolId, string $barcode, ?int $excludeId = null): void
    {
        $query = $this->copyModel
            ->where('school_id', $schoolId)
            ->where('barcode', $barcode);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'barcode' => 'A copy with this barcode already exists in this school.',
            ]);
        }
    }

    private function validateUniqueInventoryNumberPerSchool(string $schoolId, string $inventoryNumber, ?int $excludeId = null): void
    {
        $query = $this->copyModel
            ->where('school_id', $schoolId)
            ->where('inventory_number', $inventoryNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'inventory_number' => 'A copy with this inventory number already exists in this school.',
            ]);
        }
    }

    private function validateParentExists(int $parentId): void
    {
        if (! $this->categoryModel->find($parentId)) {
            throw ValidationException::withMessages([
                'parent_id' => 'The parent category does not exist.',
            ]);
        }
    }

    private function validateNotSelfParent(BookCategory $category, int $parentId): void
    {
        if ($category->id === $parentId) {
            throw ValidationException::withMessages([
                'parent_id' => 'A category cannot be its own parent.',
            ]);
        }
    }
}
