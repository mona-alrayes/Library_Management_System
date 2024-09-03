<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //call the RoleSeeder first 
        $this->call(RoleSeeder::class);

        // Then call the UserSeeder
        $this->call(UserSeeder::class);

        // then Call the CategorySeeder 
        $this->call(CategorySeeder::class);

        // Then call the BookSeeder
        $this->call(BookSeeder::class);
    }
}
