<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InsufficientStockException extends Exception
{
    public function __construct(string $message = 'Stok tidak cukup untuk transaksi keluar.')
    {
        parent::__construct($message);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
            ], 422);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $this->getMessage());
    }
}
