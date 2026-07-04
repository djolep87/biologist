<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }

    public function stats(Request $request, AdminDashboardStatsService $service): JsonResponse
    {
        // Lagani polling poziv — samo sveže liste (bez keša) da se novi unosi vide odmah.
        if ($request->query('only') === 'recent') {
            return response()->json([
                'recentActivity' => $service->recentActivity(),
                'recentDeliveries' => $service->recentDeliveries(),
                'generatedAt' => now()->toIso8601String(),
            ]);
        }

        $period = $service->resolvePeriod($request->only(['period', 'start', 'end']));

        $cacheKey = 'admin_dashboard_stats:'.md5(json_encode([
            $period['key'],
            $period['start']->toDateString(),
            $period['end']->toDateString(),
        ]));

        $data = Cache::remember($cacheKey, now()->addMinutes(5), fn () => $service->build($period));

        return response()->json($data);
    }
}
