<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getPaginatedProducts(): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->latest()
            ->paginate(10);
    }

    public function getFormCategories(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createProduct(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function getProductDetail(Product $product): Product
    {
        return $product->load('category');
    }

    public function getStockHistory(Product $product): LengthAwarePaginator
    {
        return $product->stockTransactions()
            ->latest()
            ->paginate(10);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->refresh();
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }
}
