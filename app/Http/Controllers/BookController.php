<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BookResource;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $booksWithRatings = $this->bookService->getAllBooks($request);
        return response()->json([
            'status' => 'success',
            'message' => 'Books retrieved successfully',
            'books' => BookResource::collection($booksWithRatings),
        ], 200); // OK
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $validatedRequest = $request->validated();
        $book = $this->bookService->storeBook($validatedRequest);
        return response()->json([
            'status' => 'success',
            'message' => 'Book created successfully',
            'book' => $book,
        ], 201); // Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $fetchedData = $this->bookService->showBook($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Book retrieved successfully',
            'book' => BookResource::make($fetchedData),
        ], 200); // OK
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, string $id): JsonResponse
    {
        $validatedRequest = $request->validated();
        $book = $this->bookService->updateBook($validatedRequest, $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Book updated successfully',
            'book' => BookResource::make($book),
        ], 200); // OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $message = $this->bookService->deleteBook($id);

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 200); // OK
    }
}
