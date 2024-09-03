<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Authorization logic can be added here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required','integer','between:1,5'],
            'review' => ['nullable','string','max:2000'],
            'book_id' => ['required','integer','exists:books,id'],  // Book ID must exist in the books table
            'user_id' => ['required','integer','exists:users,id'], // User ID must exist in the users table
        ];
    }

    /**
     * Modify the data before validation runs.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Merge route parameters and authenticated user ID into the request data
        $this->merge([
            'book_id' => $this->route('bookId'), //'bookId' is a route parameter
            'user_id' => auth()->id(), // Current authenticated user's ID
        ]);
    }
}
