<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method creates the `ratings` table, which stores user ratings and reviews for books.
     * Each record links a user to a book, ensuring that a user can rate a book only once.
     * The table also supports optional textual reviews along with the numerical rating.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'ratings' table
        Schema::create('ratings', function (Blueprint $table) {
            // Auto-incrementing primary key 'id'
            $table->id();

            // Timestamps to automatically manage created_at and updated_at columns
            $table->timestamps();

            // Foreign key to the 'books' table, representing the rated book
            $table->unsignedBigInteger('book_id');

            // Foreign key to the 'users' table, representing the user who gave the rating
            $table->unsignedBigInteger('user_id');

            // Rating given by the user, restricted to positive integers
            $table->unsignedInteger('rating');

            // Optional text review provided by the user
            $table->text('review')->nullable();

            // Set foreign key constraints with cascading delete
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Ensure that a user can rate a book only once
            $table->unique(['book_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method drops the `ratings` table, typically used when rolling back a migration.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'ratings' table if it exists
        Schema::dropIfExists('ratings');
    }
};
