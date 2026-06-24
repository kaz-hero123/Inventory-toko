<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P1EndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_call_core_p1_endpoints(): void
    {
        $user = User::factory()->create();
        $category = Category::query()->create(['name' => 'Makanan']);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Produk Tes',
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'min_stock_threshold' => 3,
        ]);

        $this->actingAs($user)->get('/products')->assertOk();
        $this->actingAs($user)->get('/products/create')->assertOk();
        $this->actingAs($user)->get("/products/{$product->id}")->assertOk();
        $this->actingAs($user)->get("/products/{$product->id}/edit")->assertOk();
        $this->actingAs($user)->get('/categories')->assertOk();
        $this->actingAs($user)->get("/products/{$product->id}/stock-transactions/create")->assertOk();
        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->actingAs($user)->get('/dashboard/analytics?period=weekly')->assertOk();
    }

    public function test_category_with_soft_deleted_product_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::query()->create(['name' => 'Makanan']);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Produk Tes',
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'min_stock_threshold' => 3,
        ]);

        $product->delete();

        $this->actingAs($user)
            ->delete("/categories/{$category->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}
