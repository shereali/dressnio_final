<?php

namespace App\Http\Requests\Backend;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'slug'                => Str::slug($this->name),
            'is_active'           => $this->is_active           == 'on' ? true : false,
            'is_featured'         => $this->is_featured         == 'on' ? true : false,
            'new_arrival'         => $this->new_arrival         == 'on' ? true : false,
            'best_seller'         => $this->best_seller         == 'on' ? true : false,
            'stock_in'            => $this->stock_in            == 'on' ? true : false,
            'stock_out'           => $this->stock_out           == 'on' ? true : false,
            'highlight'           => $this->highlight           == 'on' ? true : false,
            'fashionist_choice'   => $this->fashionist_choice   == 'on' ? true : false,
            'has_color_variation' => $this->has_color_variation == 'on' ? true : false,
            'is_size_visible'     => $this->is_size_visible     == 'on' ? true : false,
            'is_color_visible'    => $this->is_color_visible    == 'on' ? true : false,
        ]);
    }
}
