<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_record_transaction_updates_stock_and_creates_history(): void
    {
        $product = $this->createProduct();
        $service = app(StockService::class);

        $service->recordTransaction($product, 'in', 10, 'Restock');
        $service->recordTransaction($product, 'out', 4, 'Sale');

        $product->refresh();

        $this->assertSame(6, $product->current_stock);
        $this->assertDatabaseHas('stock_transactions', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
        ]);
        $this->assertDatabaseHas('stock_transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 4,
        ]);
    }

    public function test_out_transaction_rejects_quantity_greater_than_current_stock(): void
    {
        $product = $this->createProduct();

        $this->expectException(InsufficientStockException::class);

        app(StockService::class)->recordTransaction($product, 'out', 1, null);
    }

    private function createProduct(): Product
    {
        $category = Category::query()->create([
            'name' => 'Test Category',
        ]);

        return Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'min_stock_threshold' => 5,
        ]);
    }
}
