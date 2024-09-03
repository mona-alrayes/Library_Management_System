<?php

namespace App\Services;

use App\Models\Rating;
use App\Http\Resources\RatingResource;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

/**
 * Class RatingService
 *
 * This service handles the business logic related to ratings, including storing, updating, and deleting ratings.
 * Any exceptions are caught, logged, and rethrown for the controller or global handler to manage.
 */
class RatingService
{
    /**
     * Store a new rating.
     *
     * @param array $data
     * An associative array containing 'book_id', 'user_id', 'rating', and 'review'.
     *
     * @return array
     * An array representation of the created rating resource.
     *
     * @throws Exception
     * Throws an exception if the rating creation fails.
     */
    public function storeRating(array $data): Rating
    {
        try {
            // Create a new rating using the provided data
            $rating = Rating::create($data);

            // Ensure the rating was created successfully
            if (!$rating) {
                throw new Exception('Failed to create the Rating.');
            }

            // Load related data (book and user) for the rating
            $rating->load(['book', 'user']);

            // Return the created rating as object
            return $rating;
        } catch (Throwable $e) {
            // Log the error and rethrow it
            Log::error('Failed to create rating: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing rating.
     *
     * @param array $data
     * An associative array containing 'rating' and/or 'review' to be updated.
     * @param int $bookId
     * The ID of the book associated with the rating.
     * @param int $userId
     * The ID of the user who created the rating.
     *
     * @return Rating
     * The updated Rating model.
     *
     * @throws Exception
     * Throws an exception if the rating update fails.
     */
    public function updateRating(array $data, int $bookId, int $userId): Rating
    {
        try {
            // Find the existing rating based on book_id and user_id
            $rating = Rating::where('book_id', $bookId)->where('user_id', $userId)->first();

            // If the rating is not found, throw an exception
            if (!$rating) {
                throw new Exception('Rating not found.');
            }

            // Update only the fields that are provided in the data array
            if (isset($data['rating'])) {
                $rating->rating = $data['rating'];
            }
            if (isset($data['review'])) {
                $rating->review = $data['review'];
            }

            // Save the updated rating and load related data
            $rating->save();
            $rating->load(['book', 'user']);

            // Return the updated rating
            return $rating;
        } catch (Throwable $e) {
            // Log the error and rethrow it
            Log::error('Failed to update rating: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an existing rating.
     *
     * @param int $bookId
     * The ID of the book associated with the rating to be deleted.
     * @param int $userId
     * The ID of the user who created the rating to be deleted.
     *
     * @return void
     *
     * @throws Exception
     * Throws an exception if the rating deletion fails.
     */
    public function deleteRating(int $bookId, int $userId): void
    {
        try {
            // Find the rating that matches the provided book_id and user_id
            $rating = Rating::where('book_id', $bookId)->where('user_id', $userId)->first();

            // If the rating is not found, throw an exception
            if (!$rating) {
                throw new Exception('Rating not found.');
            }

            // Delete the rating from the database
            $rating->delete();
        } catch (Throwable $e) {
            // Log the error and rethrow it
            Log::error('Failed to delete rating: ' . $e->getMessage());
            throw $e;
        }
    }
}
