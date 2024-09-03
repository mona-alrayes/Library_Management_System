<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Category;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
        $bookId = $this->route('book');

        return [
            'title' => ['sometimes','string','min:3','max:255','unique:books,title,' . $bookId],
            'author' => ['sometimes','string','min:3','max:255'],
            'published_at' => ['sometimes','date','date_format:d-m-Y'],
            'description' => ['nullable','string'],
            'category_name' => ['sometimes','string','exists:categories,name'],
        ];
    }

    /**
     * Get the custom error messages for the validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'title.min' => 'عدد محارف :attribute لا يجب ان يقل عن 3 محارف',
            'title.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'title.unique' => 'لا يمكن تكرار :attribute , هذا الكتاب موجود بالفعل في بياناتنا',
            'author.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'author.min' => 'عدد محارف :attribute لا يجب ان يقل عن 3 محارف',
            'author.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'published_at.date' => 'حقل :attribute يجب ان يكون تاريخا صحيحا',
            'published_at.date_format' => 'حقل :attribute يجب أن يكون تاريخا بالصيغة DD-MM-YYYY',
            'description.string' => 'حقل :attribute يجب أن يكون نصا ',
            'category_name.exists' => 'القسم المختار غير موجود في قاعدة البيانات',
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'عنوان الكتاب',
            'author' => 'الكاتب',
            'description' => 'الوصف',
            'published_at' => 'تاريخ النشر',
            'category_name' => 'اسم التصنيف',
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     * This method finds the category by name and includes its ID in the request data. 
     * It also transforms the case of text fields and formats the published_at date.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Find category by name
        $category = Category::where('name', $this->input('category_name'))->first();

        $this->merge([
            'category_id' => $category ? $category->id : null,
            'published_at' => $this->input('published_at') ? Carbon::parse($this->input('published_at'))->format('d-m-Y') : null,
            'title' => $this->input('title') ? ucwords(strtolower($this->input('title'))) : null,
            'author' => $this->input('author') ? ucwords(strtolower($this->input('author'))) : null,
            'description' => $this->input('description') ? ucwords(strtolower($this->input('description'))) : null,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
