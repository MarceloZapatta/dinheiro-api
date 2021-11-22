<?php

namespace App\Services;

use App\Cobranca;
use App\Helpers\Helpers;
use App\Imports\MovimentacoesImport;
use App\JunoLogs;
use App\Movimentacao;
use App\MovimentacaoImportacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MovimentacaoImportacoesService
{
    private $contasService;
    private $junoService;

    public function __construct(ContasService $contasService, JunoService $junoService)
    {
        $this->contasService = $contasService;
        $this->junoService = $junoService;
    }

    public function get()
    {
        return MovimentacaoImportacao::latest()->paginate(30);
    }

    public function show($id)
    {
        return MovimentacaoImportacao::with('movimentacoes')
            ->findOrFail($id);
    }

    public function importarExcel(Request $request)
    {
        $movimentacaoImportacao = null;

        DB::transaction(function () use ($request, &$movimentacaoImportacao) {
            $movimentacaoImportacao = MovimentacaoImportacao::create([
                'organizacao_id' => $request->organizacao_id,
                'arquivo' => $request->file('arquivo')->getPath()
            ]);

            Excel::import(new MovimentacoesImport($movimentacaoImportacao), $request->file('arquivo'));
        });

        return $movimentacaoImportacao;
    }
}
