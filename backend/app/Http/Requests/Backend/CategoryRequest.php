<?php

namespace App\Http\Requests\Backend;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'required|string'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::lower(str_replace(' ', '-', $this->name)),
            'is_active' => $this->is_active == 'on'?true:false
        ]);
    }
}
