<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method is responsible for creating the `books` table in the database.
     * The table will store information about different books including their title, author,
     * description, and publication date. The `timestamps` method automatically adds
     * `created_at` and `updated_at` fields.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'books' table
        Schema::create('books', function (Blueprint $table) {
            // Auto-incrementing primary key 'id'
            $table->id();

            // Title of the book, unique to avoid duplicate titles
            $table->string('title')->unique();

            // Author of the book
            $table->string('author');

            // Description of the book, stored as text because it can be lengthy
            $table->text('description');

            // Publication date of the book
            $table->date('published_at');

            $table->foreignId('category_id')
            ->constrained('categories')
            ->onUpdate('cascade')  // Update if the referenced book's id changes
            ->onDelete('cascade'); // Delete borrow record if the book is deleted


            // Timestamps to automatically manage created_at and updated_at columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method is responsible for dropping the `books` table from the database.
     * It is typically used when rolling back a migration.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'books' table if it exists
        Schema::dropIfExists('books');
    }
};
