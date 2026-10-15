<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'slug'                => $this->slug ?: Str::slug($this->name),
            'is_active'           => $this->has('is_active') ? $this->boolean('is_active') : true,
            'is_featured'         => $this->boolean('is_featured'),
            'new_arrival'         => $this->boolean('new_arrival'),
            'best_seller'         => $this->boolean('best_seller'),
            'highlight'           => $this->boolean('highlight'),
            'fashionist_choice'   => $this->boolean('fashionist_choice'),
            'has_color_variation' => $this->boolean('has_color_variation'),
            'is_size_visible'     => $this->boolean('is_size_visible'),
            'is_color_visible'    => $this->boolean('is_color_visible'),
        ]);
    }
}
