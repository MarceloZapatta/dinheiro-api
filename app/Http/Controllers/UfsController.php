<?php

namespace App\Http\Controllers;

use App\Http\Resources\UfResourceCollection;
use App\Uf;

class UfsController extends Controller
{
    /**
     * Retorna os estados
     *
     * @return \App\Http\Resources\UfResourceCollection
     */
    public function index(): \App\Http\Resources\UfResourceCollection
    {
        return new UfResourceCollection(Uf::get());
    }
}
