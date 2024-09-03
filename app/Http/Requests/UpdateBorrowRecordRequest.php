<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBorrowRecordRequest extends FormRequest
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
            'book_id' => ['sometimes', 'integer', 'exists:books,id'],
            'borrowed_at' => ['sometimes', 'date' ,'date_format:d-m-Y'],
            'due_date' => ['nullable', 'date','date_format:d-m-Y'],
            'returned_at' => ['nullable', 'date', 'date_format:d-m-Y'],
        ];
    }
        public function messages(): array
        {
            return [
                'book_id.integer' => 'حقل :attribute يجب أن يكون رقما وليس اي نوع اخر',
                'book_id.exists' => 'الكتاب المختار غير موجود في قاعدة البيانات',
                'borrowed_at.date' => 'حقل :attribute يجب ان يكون تاريخا صحيحا',
                'borrowed_at.date_format' => 'حقل :attribute يجب أن يكون تاريخا بالصيغة DD-MM-YYYY',
                'due_date.date' => 'حقل :attribute يجب ان يكون تاريخا صحيحا',
                'due_date.date_format' => 'حقل :attribute يجب أن يكون تاريخا بالصيغة DD-MM-YYYY',
                'returned_at.date' => 'حقل :attribute يجب ان يكون تاريخا صحيحا',
                'returned_at.date_format' => 'حقل :attribute يجب أن يكون تاريخا بالصيغة DD-MM-YYYY',
            ];
        }
        public function attributes(): array
        {
            return [
                'book_id' => 'رقم الكتاب',
                'borrowed_at' => 'تاريخ الاستعارة',
                'due_date' => 'تاريخ الاعادة',
                'returned_at' => 'تاريخ الإرجاع',
            ];
        }
        protected function prepareForValidation()
        {
            
            if ($this->has('borrowed_at')) {
                // Parse the borrowed_at date and add 14 days to set the returned_at date
                $borrowedAt = Carbon::parse($this->input('borrowed_at'));
                $returnedAt = $borrowedAt->addDays(14);
        
                // Merge the calculated returned_at into the request data
                $this->merge([
                    'book_id' => $this->route('bookId'),
                    'user_id' => auth()->id(),
                    'returned_at' => $returnedAt->format('d-m-Y'),
                ]);
            }
        }
    
        protected function failedValidation(Validator $validator)
        {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422));
        }
}
