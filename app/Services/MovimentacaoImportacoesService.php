<?php

namespace App\Services;

use App\Categoria;
use App\Cobranca;
use App\Conta;
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

    public function importarCodigoBarras(Request $request)
    {
        $movimentacaoImportacao = null;

        DB::transaction(function () use ($request, &$movimentacaoImportacao) {
            $movimentacaoImportacao = MovimentacaoImportacao::create([
                'organizacao_id' => $request->organizacao_id,
                'arquivo' => 'Código de barras'
            ]);

            $informacaoBoleto = substr($request->codigo_barras, 5, 4) . substr($request->codigo_barras, 9, 10);
            $dataVencimento = substr($informacaoBoleto, 0, 4);
            $dataVencimento = Carbon::createFromFormat('d/m/Y', '07/10/1997')->addDays($dataVencimento);
            $valorBoleto = ((int) substr($informacaoBoleto, 4)) / 100;
            $valorBoleto *= -1;

            $categoria = Categoria::first();
            $conta = Conta::first();

            Movimentacao::create([
                'organizacao_id' => request()->organizacao_id,
                'importacao_movimentacao_id' => $movimentacaoImportacao->id,
                'descricao' => '',
                'observacoes' => null,
                'conta_id' => $conta->id,
                'categoria_id' => $categoria->id,
                'valor' => $valorBoleto,
                'data_transacao' => $dataVencimento,
            ]);
        });

        return $movimentacaoImportacao;
    }
}
