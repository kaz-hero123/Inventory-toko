<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockTransactionRequest;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockTransactionController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {
    }

    public function create(Product $product): View
    {
        return view('stock_transactions.create', [
            'product' => $product,
        ]);
    }

    public function store(StoreStockTransactionRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $this->stockService->recordTransaction(
            $product,
            $validated['type'],
            (int) $validated['quantity'],
            $validated['note'] ?? null,
        );

        return redirect()->route('products.show', $product)->with('success', 'Transaksi stok berhasil dicatat.');
    }
}
