<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve all category IDs
        $categoryIds = Category::pluck('id')->toArray();

        // Check if there are any categories to avoid errors
        if (empty($categoryIds)) {
            $this->command->error('No categories found. Please run the CategorySeeder first.');
            return;
        }

        // Create 10 books with random data
        foreach (range(1, 10) as $index) {
            Book::create([
                'title' => 'Book Title ' . $index,
                'author' => 'Author ' . Str::random(5),
                'description' => 'This is a description for Book Title ' . $index,
                'published_at' => now()->subYears(rand(1, 10))->format('d-m-Y'),
                'category_id' => $categoryIds[array_rand($categoryIds)],
            ]);
        }
    }
}
