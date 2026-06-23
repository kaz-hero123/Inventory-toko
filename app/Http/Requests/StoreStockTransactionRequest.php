<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreStockTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $product = $this->route('product');

                if (! $product instanceof Product) {
                    return;
                }

                if ($this->input('type') === 'out' && (int) $this->input('quantity') > $product->current_stock) {
                    $validator->errors()->add('quantity', 'Stok tidak cukup untuk transaksi keluar.');
                }
            },
        ];
    }
}
