<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategoryRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:255',
                'unique:categories,name,' . $this->route('category'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.sometimes' => 'حقل :attribute مطلوب ',
            'name.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'name.min' => 'عدد محارف :attribute لا يجب ان يقل عن 3 محارف',
            'name.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'name.unique' => 'لا يمكن تكرار :attribute , هذا الكتاب موجود بالفعل في بياناتنا',
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'أسم التصنيف'
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => ucwords(strtolower($this->input('name'))),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
