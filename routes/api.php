<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});
/**
 * User management routes with authentication and role-based access control using JWT.
 */
Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
    Route::apiResource('users', UserController::class);
});

Route::group(['middleware' => ['auth:api', 'role:admin']], function () {

    /**
     * Store a new Book.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    Route::post('books', [BookController::class, 'store']);

    /**
     * Update an existing book.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\JsonResponse
     */
    Route::put('books/{book}', [BookController::class, 'update']);

    /**
     * Delete a book.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\JsonResponse
     */
    Route::delete('books/{book}', [BookController::class, 'destroy']);
});

// Public routes for books
Route::apiResource('books', BookController::class)
    // Exclude store, update, and destroy methods from apiResource routes
    ->except('store', 'update', 'destroy');
