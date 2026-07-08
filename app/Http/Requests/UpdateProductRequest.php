<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['admin', 'staff']) ?? false;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'code' => ['required', 'string', 'max:30', Rule::unique('products', 'code')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'condition' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ];
    }
}
