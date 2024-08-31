<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'], // Validate role name
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'حقل Name مطلوب ',
            'name.string' => 'Name يجب أن يكون نصا وليس اي نوع اخر',
            'name.max' => 'عدد محارف Name لا يجب ان تتجاوز 255 محرفا',
            'email.required' => 'Email مطلوب لا يمكن ان يكون فارغا',
            'email.string' => 'Email يجب ان يكون بصيغة نصية',
            'email.email' => 'حقل Email يجب ان يكون بصيغة صحيحة مثل test@example.com',
            'email.max' => 'حقل Email يجب ان لا يتجاوز 255 محرفا ',
            'email.unique' => 'هذا Email موجود بالفعل في بياناتنا',
            'password.required' => 'حقل Password مطلوب',
            'password.string' => 'حقل Password مطلوب',
            'password.min' => 'حقل Password يجب ان يكون 8 محارف على الاقل',
            'password.confirmed' => 'حقل تأكيد Password غير مطابق لحقل Password',
            'role.required' => 'حقل Role مطلوب',
            'role.exists' => 'حقل Role غير موجود في قاعدة البيانات',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الأسم',
            'email' => 'البريد الالكتروني',
            'password' => 'كلمة المرور',
            'role' => 'الصلاحية'
        ];
    }

    // Customize response on validation failure
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
            'name' => strtolower($this->input('name')),
        ]);
    }
}
