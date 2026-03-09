<?php

namespace App\Http\Controllers;

use App\Http\Resources\ColorResourceCollection;
use App\Services\CoresService;

/**
 * @group Cores
 *
 * Cores
 */
class CoresController extends Controller
{
    private $coresService;

    public function __construct(CoresService $coresService)
    {
        $this->coresService = $coresService;
    }

    /**
     * Listagem
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ColorResourceCollection($this->coresService->get());
    }
}
