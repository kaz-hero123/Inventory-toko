<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\AnalyticsService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_stock_projection_returns_null_when_threshold_is_not_met(): void
    {
        $product = $this->createProduct();

        $this->assertNull(app(AnalyticsService::class)->getStockProjection($product));
    }

    public function test_stock_projection_uses_floor_after_threshold_is_met(): void
    {
        $product = $this->createProduct();
        $stockService = app(StockService::class);
        $realNow = Carbon::now();

        Carbon::setTestNow($realNow->copy()->subDays(12));
        $stockService->recordTransaction($product, 'in', 100, null);

        Carbon::setTestNow($realNow->copy()->subDays(10));
        $stockService->recordTransaction($product, 'out', 10, null);

        Carbon::setTestNow($realNow->copy()->subDays(8));
        $stockService->recordTransaction($product, 'out', 8, null);

        Carbon::setTestNow($realNow->copy()->subDays(6));
        $stockService->recordTransaction($product, 'out', 7, null);

        Carbon::setTestNow();
        $product->refresh();

        // current_stock = 100 - 10 - 8 - 7 = 75
        // days_since_first_out = 10 (from 10 days ago)
        // total_out_qty = 25
        // daysRemaining = floor(75 / (25 / 10)) = floor(30.0) = 30
        $this->assertSame(30, app(AnalyticsService::class)->getStockProjection($product));
    }

    public function test_stock_projection_returns_zero_when_threshold_is_met_and_stock_is_empty(): void
    {
        $product = $this->createProduct();
        $stockService = app(StockService::class);
        $realNow = Carbon::now();

        Carbon::setTestNow($realNow->copy()->subDays(12));
        $stockService->recordTransaction($product, 'in', 25, null);

        Carbon::setTestNow($realNow->copy()->subDays(10));
        $stockService->recordTransaction($product, 'out', 10, null);

        Carbon::setTestNow($realNow->copy()->subDays(8));
        $stockService->recordTransaction($product, 'out', 8, null);

        Carbon::setTestNow($realNow->copy()->subDays(6));
        $stockService->recordTransaction($product, 'out', 7, null);

        Carbon::setTestNow();
        $product->refresh();

        // current_stock = 25 - 10 - 8 - 7 = 0
        // threshold met (3 out txns, 10 days) but stock is already 0 → return 0
        $this->assertSame(0, app(AnalyticsService::class)->getStockProjection($product));
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
