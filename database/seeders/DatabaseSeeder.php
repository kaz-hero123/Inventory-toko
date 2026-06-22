<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner',
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
    }
}
