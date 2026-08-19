<?php

namespace App\Http\Requests\Backend;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class ColorSetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required'
        ];
    }


    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }
}
