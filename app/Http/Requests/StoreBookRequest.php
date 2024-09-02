<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookRequest extends FormRequest
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
            'title' => ['required','string','min:3','max:255' , 'unique:books,title'],
            'author' => ['required','string','min:3','max:255'],
            'published_at' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'حقل :attribute مطلوب ',
            'title.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'title.min' => 'عدد محارف :attribute لا يجب ان يقل عن 3 محارف',
            'title.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'title.unique'=> 'لا يمكن تكرار :attribute , هذا الكتاب موجود بالفعل في بياناتنا',
            'author.required' => 'حقل :attribute مطلوب ',
            'author.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'author.min' => 'عدد محارف :attribute لا يجب ان يقل عن 3 محارف',
            'author.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'published_at.required' =>'حقل :attribute يجب ان يكون تاريخا',
            'published_at.date' => 'حقل :attribute يجب ان يكون تاريخا صحيحا',
            'published_at.date_format' => 'حقل :attribute يجب أن يكون تاريخا بالصيغة DD-MM-YYYY',
            'description.string' => 'حقل :attribute يجب أن يكون نصا ',
        ];
    }
    public function attributes(): array
    {
        return [
            'title' => 'عنوان الكتاب',
            'author' => 'الكاتب',
            'description' => 'الوصف',
            'published_at' => 'تاريخ النشر'
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'published_at' => Carbon::parse($this->published_at)->format('d-m-Y'),
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
    protected function passedValidation()
    {
        $this->merge([
            'title' => ucwords(strtolower($this->input('title'))),
            'author' => ucwords(strtolower($this->input('author'))),
            'description' => ucwords(strtolower($this->input('description'))),

        ]);
    }
}

