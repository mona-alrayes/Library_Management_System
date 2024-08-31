<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method creates the `borrow_records` table, which records information about
     * books borrowed by users. It tracks the book being borrowed, the user who borrowed it,
     * and the dates related to borrowing and returning the book.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'borrow_records' table
        Schema::create('borrow_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Foreign key to the 'books' table, representing the borrowed book
            $table->foreignId('book_id')
                ->constrained('books')
                ->onUpdate('cascade')  // Update if the referenced book's id changes
                ->onDelete('cascade'); // Delete borrow record if the book is deleted

            // Foreign key to the 'users' table, representing the user who borrowed the book
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')  // Update if the referenced user's id changes
                ->onDelete('cascade'); // Delete borrow record if the user is deleted

            // Date when the book was borrowed
            $table->date('borrowed_at');

            // Date by which the book should be returned
            $table->date('due_date');

            // Date when the book was returned, nullable because it might not be returned yet
            $table->date('returned_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * This method drops the `borrow_records` table, typically used when rolling back a migration.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'borrow_records' table if it exists
        Schema::dropIfExists('borrow_records');
    }
};
