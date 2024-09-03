<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define a list of category names
        $categories = ['Fiction', 'Non-Fiction', 'Science', 'Biography', 'Fantasy', 'Mystery', 'Romance'];

        // Loop through the list and create each category
        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }
    }
}
