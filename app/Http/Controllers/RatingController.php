<?php
namespace App\Http\Controllers;

use App\Services\RatingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Requests\UpdateRatingRequest;

/**
 * Class RatingController
 *
 * This controller handles HTTP requests related to ratings, utilizing the RatingService to perform business logic.
 * It does not perform any error handling itself, relying on Laravel's global exception handler.
 */
class RatingController extends Controller
{
    protected $ratingService;

    /**
     * Constructor to inject the RatingService dependency.
     *
     * @param RatingService $ratingService
     */
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Store a newly created rating in storage.
     *
     * @param StoreRatingRequest $request
     * Validated request data for creating a rating.
     *
     * @return JsonResponse
     * A JSON response containing the created rating or an error message.
     */
    public function store(StoreRatingRequest $request): JsonResponse
    {
        // Get validated data from the request
        $validatedData = $request->validated();

        // Call the service to store the rating and return the result
        $rating = $this->ratingService->storeRating($validatedData);

        // Respond with the created rating and success message
        return response()->json([
            'status' => 'success',
            'message' => 'Rating created successfully',
            'book' => $rating,
        ], 201); // Created
    }

    /**
     * Update the specified rating in storage.
     *
     * @param UpdateRatingRequest $request
     * Validated request data for updating a rating.
     * @param int $movieId
     * The ID of the movie (book) to be updated.
     *
     * @return JsonResponse
     * A JSON response containing the updated rating or an error message.
     */
    public function update(UpdateRatingRequest $request, $movieId): JsonResponse
    {
        // Get validated data from the request
        $validatedData = $request->validated();

        // Get the ID of the authenticated user
        $userId = auth()->id();

        // Call the service to update the rating and return the result
        $updatedRating = $this->ratingService->updateRating($validatedData, $movieId, $userId);

        // Respond with the updated rating and success message
        return response()->json([
            'status' => 'success',
            'message' => 'Book updated successfully',
            'book' => RatingResource::make($updatedRating),
        ], 200); // OK
    }

    /**
     * Remove the specified rating from storage.
     *
     * @param int $movieId
     * The ID of the movie (book) to be deleted.
     *
     * @return JsonResponse
     * A JSON response indicating success or failure.
     */
    public function destroy($movieId): JsonResponse
    {
        // Get the ID of the authenticated user
        $userId = auth()->id();

        // Call the service to delete the rating
        $this->ratingService->deleteRating($movieId, $userId);

        // Respond with a success message
        return response()->json([
            'status' => 'success',
            'message' => 'Rating deleted successfully',
        ], 200); // OK
    }
}
