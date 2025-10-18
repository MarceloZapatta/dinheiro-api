<?php

namespace App\Http\Controllers;

use App\Services\DashboardsService;
use Illuminate\Http\Request;

class DashboardsController extends Controller
{
    private $dashboardsService;

    public function __construct(DashboardsService $dashboardsService)
    {
        $this->dashboardsService = $dashboardsService;
    }

    public function porCategoria(Request $request)
    {
        $this->validate($request, [
            'data_inicio' => 'required|date_format:d/m/Y',
            'data_fim' => 'required|date_format:d/m/Y',
            'tipo' => 'required'
        ]);

        return response()->json([
            'data' => $this->dashboardsService->porCategoria($request)
        ]);
    }

    public function movimentacoesAnual(Request $request)
    {
        $this->validate($request, [
            'data_inicio' => 'required|date_format:d/m/Y',
            'data_fim' => 'required|date_format:d/m/Y'
        ]);

        return response()->json([
            'data' => $this->dashboardsService->movimentacoesAnual($request)
        ]);
    }
}
