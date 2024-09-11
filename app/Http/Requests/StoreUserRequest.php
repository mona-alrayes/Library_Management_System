<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * This method determines if the user making the request is authorized
     * to perform this action. By default, it returns true, allowing all
     * requests to pass authorization. Override this method to implement
     * custom authorization logic.
     *
     * @return bool True if authorized, otherwise false.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * This method returns an array of validation rules that apply to the
     * request. Each key in the array represents an input field, and each
     * value is an array of validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> Array of validation rules.
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

    /**
     * Get the custom error messages for validation rules.
     *
     * This method returns an array of custom error messages for validation
     * rules. The array keys should correspond to the validation rule names,
     * and the values are the custom error messages.
     *
     * @return array<string, string> Array of custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'حقل :attribute مطلوب ',
            'name.string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'name.max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'email.required' => 'حقل :attribute مطلوب لا يمكن ان يكون فارغا',
            'email.string' => 'حقل :attribute يجب ان يكون بصيغة نصية',
            'email.email' => 'حقل :attribute يجب ان يكون بصيغة صحيحة مثل test@example.com',
            'email.max' => 'حقل :attribute يجب ان لا يتجاوز 255 محرفا ',
            'email.unique' => 'هذا :attribute موجود بالفعل في بياناتنا',
            'password.required' => 'حقل :attribute مطلوب',
            'password.string' => 'حقل :attribute مطلوب',
            'password.min' => 'حقل :attribute يجب ان يكون 8 محارف على الاقل',
            'password.confirmed' => 'حقل تأكيد :attribute غير مطابق لحقل :attribute',
            'role.required' => 'حقل :attribute مطلوب',
            'role.exists' => 'حقل :attribute غير موجود في قاعدة البيانات',
        ];
    }

    /**
     * Get the custom attribute names for validator errors.
     *
     * This method returns an array of custom attribute names that should
     * be used in error messages. The keys are the input field names, and
     * the values are the custom names to be used in error messages.
     *
     * @return array<string, string> Array of custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'الأسم',
            'email' => 'البريد الالكتروني',
            'password' => 'كلمة المرور',
            'role' => 'الصلاحية'
        ];
    }

    /**
     * Handle actions to be performed before validation passes.
     *
     * This method is called before validation performed . You can use this
     * method to modify the request data before it is processed by the controller.
     *
     * For example, you might want to format or modify the input data.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => ucwords(strtolower($this->input('name'))),
        ]);
    }


    /**
     * Handle a failed validation attempt.
     *
     * This method is called when validation fails. It customizes the
     * response that is returned when validation fails, including the
     * status code and error messages.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }

    /**
     * Handle actions to be performed after validation passes.
     *
     * This method is called after validation has passed. You can use this
     * method to modify the request data before it is processed by the controller.
     *
     * For example, you might want to format or modify the input data.
     */
    protected function passedValidation()
    {
         $this->merge([
            'password' => Hash::make($this->input('password'),)
         ]);
    }

    public function validated($key=null,$default=null): array
    {
        $validatedData = parent::validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        return $validatedData;
    }

    public function ValidationWithHasing(){
        return $this->safe()->merge([
            'password' => Hash::make('password') ,
        ]);
    }

}
