<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function index(): View
    {
        $data = $this->dashboardService->getDashboardData();

        return view('dashboard', [
            'lowStockProducts'    => $data['lowStockProducts'],
            'topSellingProducts'  => $data['topSellingProducts'],
            'slowSellingProducts' => $data['slowSellingProducts'],
            'stockProjections'    => $data['stockProjections'],
            'summary'             => $data['summary'],
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        return response()->json(
            $this->dashboardService->getAnalyticsData((string) $request->query('period', 'weekly'))
        );
    }
}
