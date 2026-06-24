<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\AnalyticsService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly AnalyticsService $analyticsService,
    ) {
    }

    public function index(): View
    {
        return view('products.index', [
            'products' => $this->productService->getPaginatedProducts(),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => $this->productService->getFormCategories(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct($request->validated());

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        return view('products.show', [
            'product'        => $this->productService->getProductDetail($product),
            'stockHistory'   => $this->productService->getStockHistory($product),
            'stockProjection' => $this->analyticsService->getStockProjection($product),
        ]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product'    => $this->productService->getProductDetail($product),
            'categories' => $this->productService->getFormCategories(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->updateProduct($product, $request->validated());

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->deleteProduct($product);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
