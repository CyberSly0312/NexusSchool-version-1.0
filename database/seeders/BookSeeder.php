<?php

namespace Database\Seeders;

use App\Domains\School\Models\School;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();
        if (! $school) {
            return;
        }

        $fiction = BookCategory::where('school_id', $school->id)->where('name', 'Fiction')->first();
        $science = BookCategory::where('school_id', $school->id)->where('name', 'Science')->first();
        $math = BookCategory::where('school_id', $school->id)->where('name', 'Mathematics')->first();
        $history = BookCategory::where('school_id', $school->id)->where('name', 'History')->first();
        $literature = BookCategory::where('school_id', $school->id)->where('name', 'Literature')->first();

        $ghanaPub = Publisher::where('school_id', $school->id)->where('name', 'Ghana Publishing Corporation')->first();
        $africanMinds = Publisher::where('school_id', $school->id)->where('name', 'African Minds')->first();
        $sublime = Publisher::where('school_id', $school->id)->where('name', 'Sublime Books')->first();

        $kwame = Author::where('school_id', $school->id)->where('first_name', 'Kwame')->first();
        $ama = Author::where('school_id', $school->id)->where('first_name', 'Ama')->first();
        $kofi = Author::where('school_id', $school->id)->where('first_name', 'Kofi')->first();
        $efua = Author::where('school_id', $school->id)->where('first_name', 'Efua')->first();
        $yaw = Author::where('school_id', $school->id)->where('first_name', 'Yaw')->first();

        $books = [
            [
                'isbn' => '978-0001-2345-6',
                'title' => 'The Gold Coast Memoirs',
                'subtitle' => 'Stories from Independence',
                'description' => 'A sweeping novel about a family navigating the transition from colonial Gold Coast to independent Ghana. The narrative weaves through three generations, capturing the hopes and struggles of a nation finding its identity.',
                'language' => 'English',
                'edition' => '2nd',
                'publication_year' => 2018,
                'publisher_id' => $ghanaPub?->id,
                'book_category_id' => $fiction?->id,
                'shelf_location' => 'SH-01-01',
                'cover_image' => null,
                'status' => 'active',
                'author_id' => $kwame?->id,
            ],
            [
                'isbn' => '978-0002-3456-7',
                'title' => 'Ama and the Baobab Tree',
                'subtitle' => null,
                'description' => 'A young girl discovers the secret wisdom of the ancient baobab tree in her village. Through magical encounters, she learns about nature, community, and the importance of preserving Ghanaian traditions.',
                'language' => 'English',
                'edition' => '1st',
                'publication_year' => 2021,
                'publisher_id' => $sublime?->id,
                'book_category_id' => $fiction?->id,
                'shelf_location' => 'SH-01-02',
                'cover_image' => null,
                'status' => 'active',
                'author_id' => $ama?->id,
            ],
            [
                'isbn' => '978-0003-4567-8',
                'title' => 'Understanding West African Science',
                'subtitle' => 'From Traditional Knowledge to Modern Discovery',
                'description' => 'An exploration of scientific traditions in West Africa and their contributions to modern science. This textbook covers topics from traditional metallurgy to contemporary environmental science, with a focus on Ghanaian innovations.',
                'language' => 'English',
                'edition' => '3rd',
                'publication_year' => 2020,
                'publisher_id' => $africanMinds?->id,
                'book_category_id' => $science?->id,
                'shelf_location' => 'SH-02-01',
                'cover_image' => null,
                'status' => 'active',
                'author_id' => $kofi?->id,
            ],
            [
                'isbn' => '978-0004-5678-9',
                'title' => 'Kings and Kingdoms of Pre-Colonial Ghana',
                'subtitle' => null,
                'description' => 'A comprehensive account of the great kingdoms that shaped the history of present-day Ghana before colonial rule. Covers the Ashanti Empire, the Dagbon Kingdom, and the Fante Confederacy with detailed maps and illustrations.',
                'language' => 'English',
                'edition' => '1st',
                'publication_year' => 2019,
                'publisher_id' => $africanMinds?->id,
                'book_category_id' => $history?->id,
                'shelf_location' => 'SH-03-01',
                'cover_image' => null,
                'status' => 'active',
                'author_id' => $efua?->id,
            ],
            [
                'isbn' => null,
                'title' => 'Mastering Senior High Mathematics',
                'subtitle' => 'A Complete Guide for WASSCE Preparation',
                'description' => 'A comprehensive mathematics textbook aligned with the Ghana Education Service curriculum for Senior High Schools. Includes worked examples, practice problems, and past WASSCE questions with detailed solutions.',
                'language' => 'English',
                'edition' => '4th',
                'publication_year' => 2023,
                'publisher_id' => $ghanaPub?->id,
                'book_category_id' => $math?->id,
                'shelf_location' => 'SH-04-01',
                'cover_image' => null,
                'status' => 'active',
                'author_id' => $yaw?->id,
            ],
        ];

        foreach ($books as $bookData) {
            $authorId = $bookData['author_id'];
            unset($bookData['author_id']);

            $book = Book::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'title' => $bookData['title'],
                ],
                $bookData
            );

            if ($authorId && ! $book->authors()->where('author_id', $authorId)->exists()) {
                $book->authors()->attach($authorId);
            }
        }
    }
}
