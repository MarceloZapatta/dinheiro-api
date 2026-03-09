<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyReportRequest;
use App\Services\MonthlyReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MonthlyReportController extends Controller
{
    protected MonthlyReportService $monthlyReportService;

    public function __construct(MonthlyReportService $monthlyReportService)
    {
        $this->monthlyReportService = $monthlyReportService;
    }

    /**
     * Generate a monthly financial report for the authenticated user.
     *
     * @param MonthlyReportRequest $request
     * @return JsonResponse
     */
    public function generateReport(MonthlyReportRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $reportData = $this->monthlyReportService->generateMonthlyReport($userId, $startDate, $endDate);

        return response()->json([
            'data' => $reportData
        ]);
    }
}
