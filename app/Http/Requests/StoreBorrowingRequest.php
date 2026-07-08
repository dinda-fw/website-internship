<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['admin', 'staff']) ?? false;
    }

    public function rules(): array
    {
        return [
            'borrower_name' => ['required', 'string', 'max:255'],
            'borrow_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:borrow_date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Pilih minimal satu barang yang akan dipinjam.',
            'items.*.quantity.min' => 'Jumlah pinjam minimal 1.',
        ];
    }
}
