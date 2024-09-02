<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:2000',
            'book_id' => [
                'required',
                'integer',
                Rule::exists('books', 'id'),
            ],
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('id', auth()->id());
                }),
            ],
        ];
    }

    /**
     * Modify the data before validation runs.
     */
    protected function prepareForValidation()
    {
        // Merge route parameters into request data before validation
        $this->merge([
            'book_id' => $this->route('bookId'),
            'user_id' => auth()->id(),
        ]);
    }
}