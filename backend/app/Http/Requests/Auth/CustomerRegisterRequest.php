<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegisterRequest extends FormRequest
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
            'name'       => 'required|string',
            'email'  => 'string|unique:customers',
            'mobile_no'  => 'required|string|min:11|unique:customers',
            'login_code' => 'required|numeric|min:4',
        ];
    }

    public function prepareForValidation()
    {
        $login_code = 1234;
        $this->merge([
            'email'      => $this->email == ""?$this->mobile_no.'@dressnio.com': $this->email,
            'login_code' => $login_code,
            'password'   => $login_code,
        ]);
    }
}
