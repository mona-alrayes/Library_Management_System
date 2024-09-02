<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\Request;

/**
 * Class BookService
 * 
 * This service handles operations related to books, including fetching, storing, updating, and deleting books.
 */
class BookService
{
    /**
     * Retrieve all books with optional filters and sorting.
     * 
     * @param Request $request
     * The request object containing optional filters (author) and sorting options (sort_by, sort_order).
     * 
     * @return array
     * An array containing paginated book resources.
     */
    public function getAllBooks(Request $request): array
    {
        // Create a query builder instance for the Book model
        //$query = Book::with('ratings');
        $query = Book::query(); // Use query() instead of all()

        // Apply filters based on request parameters
        $query->when($request->author, function ($q, $author) {
            return $q->where('author', $author);
        });

        // Apply sorting if specified
        if ($request->sort_by) {
            $sortOrder = $request->sort_order ?? 'asc';
            $query->orderBy($request->sort_by, $sortOrder);
        }

        // Paginate the results
        $books = $query->paginate(5);

        // Return the paginated books as an array
        return [
            'data' => $books->items(), // the items on the current page
            'current_page' => $books->currentPage(),
            'last_page' => $books->lastPage(),
            'per_page' => $books->perPage(),
            'total' => $books->total(),
        ];
    }

    /**
     * Store a new book.
     * 
     * @param array $data
     * An associative array containing 'title', 'author', 'published_at', and 'description'.
     * 
     * @return array
     * An array containing the created book resource.
     * 
     * @throws \Exception
     * Throws an exception if the book creation fails.
     */
    public function storeBook(array $data): array
    {
        // Create a new book using the provided data
        $book = Book::create($data);

        // Check if the book was created successfully
        if (!$book) {
            throw new \Exception('Failed to create the book.');
        }

        // Return the created book as an array
        return $book->toArray();
    }

    /**
     * Retrieve a specific book by its ID.
     * 
     * @param int $id
     * The ID of the book to retrieve.
     * 
     * @return array
     * An array containing the book resource.
     * 
     * @throws \Exception
     * Throws an exception if the book is not found.
     */
    public function showBook(int $id): array
    {
        // Find the book by ID
        $book = Book::find($id);

        // If no book is found, throw an exception
        if (!$book) {
            throw new \Exception('Book not found.');
        }

        // Return the found book as an array
        return $book->toArray();
    }

    /**
     * Update an existing book.
     * 
     * @param Request $request
     * The request object containing the fields to update.
     * @param string $id
     * The ID of the book to update.
     * 
     * @return array
     * An array containing the updated book resource.
     * 
     * @throws \Exception
     * Throws an exception if the book is not found or update fails.
     */
    public function updateBook(array $data, string $id): Book
    {
        // Find the book by ID or fail if not found
        $book = Book::findOrFail($id);

        // Update only the fields that are provided in the data array
        $book->update(array_filter($data));

        // Return the updated book as an array
        return $book;
    }

    /**
     * Delete a book by its ID.
     * 
     * @param string $id
     * The ID of the book to delete.
     * 
     * @return string
     * A message confirming the deletion.
     * 
     * @throws \Exception
     * Throws an exception if the book is not found.
     */
    public function deleteBook(string $id): string
    {
        // Find the book by ID or fail if not found
        $book = Book::findOrFail($id);

        // Delete the book
        $book->delete();

        return "Book deleted successfully.";
    }
}
