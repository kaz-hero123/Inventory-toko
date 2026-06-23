<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\StockTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getStockProjection(Product $product): ?int
    {
        $outTransactions = $product->stockTransactions()
            ->where('type', 'out')
            ->oldest()
            ->get();

        $firstOutTransaction = $outTransactions->first();
        $outTransactionCount = $outTransactions->count();
        $daysSinceFirstOut = $firstOutTransaction === null
            ? 0
            : max(1, CarbonImmutable::parse($firstOutTransaction->created_at)->diffInDays(now()));

        if ($outTransactionCount < 3 || $daysSinceFirstOut < 7) {
            return null;
        }

        if ($product->current_stock <= 0) {
            return 0;
        }

        $totalOutQuantity = (float) $product->stockTransactions()
            ->where('type', 'out')
            ->sum('quantity');

        $averageOutPerDay = $totalOutQuantity / $daysSinceFirstOut;

        return (int) floor($product->current_stock / $averageOutPerDay);
    }

    public function getTopSellingProducts(string $period): Collection
    {
        return $this->getSellingProducts($period, 'desc');
    }

    public function getSlowSellingProducts(string $period): Collection
    {
        return $this->getSellingProducts($period, 'asc');
    }

    private function getSellingProducts(string $period, string $direction): Collection
    {
        $startDate = $this->getPeriodStartDate($period);

        return Product::query()
            ->select('products.*')
            ->selectSub(
                StockTransaction::query()
                    ->selectRaw('COALESCE(SUM(quantity), 0)')
                    ->whereColumn('stock_transactions.product_id', 'products.id')
                    ->where('type', 'out')
                    ->where('created_at', '>=', $startDate),
                'sold_quantity'
            )
            ->orderBy('sold_quantity', $direction)
            ->orderBy('name')
            ->limit(5)
            ->get();
    }

    private function getPeriodStartDate(string $period): CarbonImmutable
    {
        return match ($period) {
            'weekly', 'week' => CarbonImmutable::now()->subWeek(),
            'monthly', 'month' => CarbonImmutable::now()->subMonth(),
            default => CarbonImmutable::now()->subWeek(),
        };
    }
}
