<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'hilmanhamzi@gmail.com'],
            [
                'name' => 'hilman',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $categories = [
            'Makanan Ringan',
            'Minuman',
            'Kebutuhan Harian',
        ];

        foreach ($categories as $categoryName) {
            Category::query()->firstOrCreate(['name' => $categoryName]);
        }

        $snacks = Category::query()->where('name', 'Makanan Ringan')->firstOrFail();
        $drinks = Category::query()->where('name', 'Minuman')->firstOrFail();
        $dailyNeeds = Category::query()->where('name', 'Kebutuhan Harian')->firstOrFail();

        $products = [
            [
                'category_id' => $snacks->id,
                'name' => 'Keripik Singkong',
                'purchase_price' => 6000,
                'selling_price' => 8000,
                'min_stock_threshold' => 10,
            ],
            [
                'category_id' => $snacks->id,
                'name' => 'Biskuit Cokelat',
                'purchase_price' => 4500,
                'selling_price' => 6500,
                'min_stock_threshold' => 12,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Air Mineral 600ml',
                'purchase_price' => 2500,
                'selling_price' => 4000,
                'min_stock_threshold' => 24,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Teh Botol',
                'purchase_price' => 3500,
                'selling_price' => 5000,
                'min_stock_threshold' => 18,
            ],
            [
                'category_id' => $dailyNeeds->id,
                'name' => 'Sabun Mandi',
                'purchase_price' => 7000,
                'selling_price' => 9500,
                'min_stock_threshold' => 8,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->firstOrCreate(
                ['name' => $product['name']],
                $product,
            );
        }

        if (! StockTransaction::query()->exists()) {
            $this->seedStockHistory(app(StockService::class));
        }
    }

    private function seedStockHistory(StockService $stockService): void
    {
        $keripik = Product::query()->where('name', 'Keripik Singkong')->firstOrFail();
        $biskuit = Product::query()->where('name', 'Biskuit Cokelat')->firstOrFail();
        $airMineral = Product::query()->where('name', 'Air Mineral 600ml')->firstOrFail();
        $tehBotol = Product::query()->where('name', 'Teh Botol')->firstOrFail();

        $transactions = [
            [$keripik, 'in', 100, 'Stok awal Keripik Singkong', now()->subDays(12)],
            [$keripik, 'out', 10, 'Penjualan Keripik Singkong', now()->subDays(10)],
            [$keripik, 'out', 8, 'Penjualan Keripik Singkong', now()->subDays(8)],
            [$keripik, 'out', 7, 'Penjualan Keripik Singkong', now()->subDays(6)],
            [$biskuit, 'in', 80, 'Stok awal Biskuit Cokelat', now()->subDays(14)],
            [$biskuit, 'out', 12, 'Penjualan Biskuit Cokelat', now()->subDays(12)],
            [$biskuit, 'out', 9, 'Penjualan Biskuit Cokelat', now()->subDays(9)],
            [$biskuit, 'out', 6, 'Penjualan Biskuit Cokelat', now()->subDays(5)],
            [$airMineral, 'in', 120, 'Stok awal Air Mineral', now()->subDays(8)],
            [$airMineral, 'out', 20, 'Penjualan Air Mineral', now()->subDays(6)],
            [$tehBotol, 'in', 60, 'Stok awal Teh Botol', now()->subDays(3)],
            [$tehBotol, 'out', 5, 'Penjualan Teh Botol', now()->subDays(1)],
        ];

        foreach ($transactions as [$product, $type, $quantity, $note, $createdAt]) {
            Carbon::setTestNow($createdAt);
            $stockService->recordTransaction($product, $type, $quantity, $note);
        }

        Carbon::setTestNow();
    }
}
