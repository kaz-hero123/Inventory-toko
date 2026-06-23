<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

class DashboardService
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(string $period = 'weekly'): array
    {
        $lowStockProducts = Product::query()
            ->with('category')
            ->whereColumn('current_stock', '<=', 'min_stock_threshold')
            ->orderBy('current_stock')
            ->get();

        $products = Product::query()
            ->orderBy('name')
            ->get();

        return [
            'lowStockProducts' => $lowStockProducts,
            'topSellingProducts' => $this->analyticsService->getTopSellingProducts($period),
            'slowSellingProducts' => $this->analyticsService->getSlowSellingProducts($period),
            'stockProjections' => $products->mapWithKeys(fn (Product $product): array => [
                $product->id => $this->analyticsService->getStockProjection($product),
            ]),
            'summary' => [
                'total_products' => $products->count(),
                'low_stock_count' => $lowStockProducts->count(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getAnalyticsData(string $period): array
    {
        $products = Product::query()
            ->orderBy('name')
            ->get();

        return [
            'topSelling' => $this->analyticsService->getTopSellingProducts($period),
            'slowSelling' => $this->analyticsService->getSlowSellingProducts($period),
            'projections' => $products->mapWithKeys(fn (Product $product): array => [
                $product->id => $this->analyticsService->getStockProjection($product),
            ]),
        ];
    }
}
