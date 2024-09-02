<?php
namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Category; // Import the Category model

class UpdateBookRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'min:3', 'max:255', 'unique:books,title,' . $this->route('book')],
            'author' => ['sometimes', 'string', 'min:3', 'max:255'],
            'published_at' => ['sometimes', 'date', 'date_format:d-m-Y'],
            'description' => ['nullable', 'string'],
            'category_name' => ['sometimes', 'string', 'exists:categories,name'], // Validate category name
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'حقل :attribute مطلوب ',
            'title.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'title.min' => 'عدد محارف :attribute لا يجب ان يقل عن 3 محارف',
            'title.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'title.unique' => 'لا يمكن تكرار :attribute , هذا الكتاب موجود بالفعل في بياناتنا',
            'author.required' => 'حقل :attribute مطلوب ',
            'author.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'author.min' => 'عدد محارف :attribute لا يجب ان يقل عن 3 محارف',
            'author.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'published_at.required' => 'حقل :attribute يجب ان يكون تاريخا',
            'published_at.date' => 'حقل :attribute يجب ان يكون تاريخا صحيحا',
            'published_at.date_format' => 'حقل :attribute يجب أن يكون تاريخا بالصيغة DD-MM-YYYY',
            'description.string' => 'حقل :attribute يجب أن يكون نصا ',
            'category_name.exists' => 'القسم المختار غير موجود في قاعدة البيانات',
        ];
    }

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

    protected function prepareForValidation()
    {
        // Find category by name
        $category = Category::where('name', $this->input('category_name'))->first();

        if ($category) {
            $this->merge([
                'category_id' => $category->id, // Add the category_id to the request data
                'published_at' => Carbon::parse($this->published_at)->format('d-m-Y'),
            ]);
        } else {
            $this->merge([
                'category_id' => null, // Set to null if category is not found
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

    protected function passedValidation()
    {
        $this->merge([
            'title' => ucwords(strtolower($this->input('title'))),
            'author' => ucwords(strtolower($this->input('author'))),
            'description' => ucwords(strtolower($this->input('description'))),
        ]);
    }
}
