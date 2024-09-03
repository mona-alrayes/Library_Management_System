<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

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
        try {
            // Create a query builder instance for the Book model
            $query = Book::with('category');

            // Apply filters based on request parameters
            $query->when($request->author, function ($q, $author) {
                return $q->where('author', $author);
            });
            // Apply filter based on category name
            $query->when($request->category_name, function ($q, $category) {
                return $q->whereHas('category', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            });
            // Filter books and brings only avaliable books
            $query->when($request->has('available') && $request->available == 'true', function ($q) {
                $q->whereDoesntHave('borrowRecords', function ($q) {
                    $q->whereNotNull('returned_at');
                });
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
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve books: ' . $e->getMessage());
        }
    }

    /**
     * Store a new book.
     * 
     * @param array $data
     * An associative array containing 'title', 'author', 'published_at', and 'description'.
     * 
     * @return Book
     * The created book resource.
     * 
     * @throws \Exception
     * Throws an exception if the book creation fails.
     */
    public function storeBook(array $data): Book
    {
        try {
            $book = Book::create($data);

            if (!$book) {
                throw new Exception('Failed to create the book.');
            }

            return $book;
        } catch (Exception $e) {
            throw new Exception('Book creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a specific book by its ID.
     * 
     * @param int $id
     * The ID of the book to retrieve.
     * 
     * @return Book
     * The book resource.
     * 
     * @throws \Exception
     * Throws an exception if the book is not found.
     */
    public function showBook(int $id): Book
    {
        try {
            $book = Book::findOrFail($id);
            return $book;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Book not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve book: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing book.
     * 
     * @param array $data
     * The data array containing the fields to update.
     * @param string $id
     * The ID of the book to update.
     * 
     * @return Book
     * The updated book resource.
     * 
     * @throws \Exception
     * Throws an exception if the book is not found or update fails.
     */
    public function updateBook(array $data, string $id): Book
    {
        try {
            $book = Book::findOrFail($id);

            $book->update(array_filter($data));

            return $book;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Book not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update book: ' . $e->getMessage());
        }
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
        try {
            $book = Book::findOrFail($id);

            $book->delete();

            return "Book deleted successfully.";
        } catch (ModelNotFoundException $e) {
            throw new Exception('Book not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete book: ' . $e->getMessage());
        }
    }
}
