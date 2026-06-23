<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class CategoryService
{
    public function getAllCategories(): Collection
    {
        return Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createCategory(array $data): Category
    {
        return Category::query()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->refresh();
    }

    public function deleteCategory(Category $category): void
    {
        $hasProducts = Product::withTrashed()
            ->where('category_id', $category->id)
            ->exists();

        if ($hasProducts) {
            throw new RuntimeException('Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        $category->delete();
    }
}
