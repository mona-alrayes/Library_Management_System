<?php

namespace App\Services;

use Exception;
use Throwable;
use App\Models\BorrowRecord;
use Illuminate\Support\Facades\Log;


class BorrowRecordsService
{
    /**
     * Borrow a book and create a new BorrowRecord.
     *
     * @param array $data
     * @return BorrowRecord
     * @throws Exception
     */
    public function BorrowBook(array $data): BorrowRecord
    {
        try {
            // check if there is record for this book in table borrow_records
            $borrowRecord = BorrowRecord::where('book_id', $data['book_id'])->first();
            // if there is record indeed and isReturned = true it means that returned_at has date which means it is still not available , once returned_at = null it means book is avaliable
            if ($borrowRecord && $borrowRecord->isReturned()) {
                throw new Exception('The book is currently not available for borrowing.');
            } else {
                // Create a new rating using the provided data
                $borrow = BorrowRecord::create($data);

                // Ensure the rating was created successfully
                if (!$borrow) {
                    throw new Exception('Failed to create the Borrow Record.');
                }

                // Load related data (book and user) for the borrowingRecord
                $borrow->load(['book', 'user']);

                // Return the created rating as a resource array
                return $borrow;
            }
        } catch (Throwable $e) {
            // Log the error and rethrow it
            Log::error('Failed to create rating: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Update a borrow record.
     *
     * @param array $data
     * @param int $id
     * @return BorrowRecord
     * @throws Exception
     */
    public function updateBorrowBook(array $data, int $id)
    {
        try {

            $borrowRecord = BorrowRecord::find($id);
            if (!$borrowRecord) {
                throw new Exception('The Borrow Record does not Exist !.');
            } else {
                $borrowRecord->update(array_filter($data));
               // $borrowRecord->update($data);
                return $borrowRecord;
            }
        } catch (Throwable $e) {
            // Log the error and rethrow it
            Log::error('Failed to update BorrowRecord: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * return a book .
     *
     * @param int $id
     * @return void
     * @throws Exception
     */

    public function ReturnBook(string $id): string
    {
        try {
        $BorrowedBook = BorrowRecord::findOrFail($id);
        $BorrowedBook->due_date = now();
        $BorrowedBook->returned_at = null;
        $BorrowedBook->save();
        return "Book returned successfully.";
        }catch (Exception $e) {
            Log::error('Error returning book: ' . $e->getMessage());
            throw $e;
        }
    }
}
