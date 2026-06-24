<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    /**
     * @throws InsufficientStockException
     */
    public function recordTransaction(Product $product, string $type, int $qty, ?string $note): StockTransaction
    {
        if (! in_array($type, ['in', 'out'], true)) {
            throw new InvalidArgumentException('Tipe transaksi stok tidak valid.');
        }

        if ($qty < 1) {
            throw new InvalidArgumentException('Quantity transaksi stok harus lebih dari 0.');
        }

        return DB::transaction(function () use ($product, $type, $qty, $note): StockTransaction {
            $product->refresh();

            if ($type === 'out' && $qty > $product->current_stock) {
                throw new InsufficientStockException();
            }

            $product->current_stock = $type === 'in'
                ? $product->current_stock + $qty
                : $product->current_stock - $qty;

            $product->save();

            return StockTransaction::query()->create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $qty,
                'note' => $note,
            ]);
        });
    }
}
