<?php

namespace App\Http\Controllers;

use App\Models\BorrowRecord;
use App\Services\BorrowRecordsService;
use App\Http\Resources\BorrowRecordsResource;
use App\Http\Requests\StoreBorrowRecordRequest;
use App\Http\Requests\UpdateBorrowRecordRequest;

class BorrowRecordController extends Controller
{
    protected $BorrowService;

   
    public function __construct(BorrowRecordsService $BorrowService)
    {
        $this->BorrowService = $BorrowService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowRecords = BorrowRecord::with(['book', 'user'])->paginate(10);

        return BorrowRecordsResource::collection($borrowRecords);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowRecordRequest $request)
    {
        $validatedData=$request->validated();
        $borrowBook= $this->BorrowService->borrowBook($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Book borrowed successfully',
            'book' => BorrowRecordsResource::make($borrowBook),
        ], 201); // Created
    }

    /**
     * Display the specified resource.
     */
    public function show(BorrowRecord $borrowRecord)
    {
        return BorrowRecordsResource::make($borrowRecord->load(['book', 'user']));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBorrowRecordRequest $request, int $id)
    {
         // Get validated data from the request
         $validatedData = $request->validated();

         // Call the service to update the rating and return the result
         $updatedBorrow = $this->BorrowService->updateBorrowBook($validatedData , $id);
 
         // Respond with the updated rating and success message
         return response()->json([
             'status' => 'success',
             'message' => 'Book updated successfully',
             'book' => BorrowRecordsResource::make($updatedBorrow),
         ], 200); // OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->BorrowService->ReturnBook($id);
        // Respond with a success message
        return response()->json([
            'status' => 'success',
            'message' => 'Book was returned successfully',
        ], 200); // OK
    }
}
