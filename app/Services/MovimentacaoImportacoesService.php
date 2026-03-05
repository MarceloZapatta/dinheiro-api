<?php

namespace App\Services;

use App\Http\Requests\ImportOfxRequest;
use App\Models\Categoria;
use App\Models\Conta;
use App\Imports\MovimentacoesImport;
use App\Models\Movimentacao;
use App\Models\MovimentacaoImportacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MovimentacaoImportacoesService
{
    public function __construct(private readonly OfxReaderService $ofxReaderService) {}

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

    public function importOfx(ImportOfxRequest $request)
    {
        $movimentacaoImportacao = null;

        $othersCategoryExpense = Categoria::where('user_id', Auth::id())->where('nome', 'Outros')->where('expense', true)->first();
        $othersCategoryIncome = Categoria::where('user_id', Auth::id())->where('nome', 'Outros')->where('expense', false)->first();
        $defaultAccount = Conta::where('user_id', Auth::id())->first();

        DB::transaction(function () use ($request, &$movimentacaoImportacao, $defaultAccount, $othersCategoryExpense, $othersCategoryIncome) {
            $movimentacaoImportacao = MovimentacaoImportacao::create([
                'user_id' => Auth::id(),
                'arquivo' => $request->file('file')->getClientOriginalName()
            ]);

            $ofxFilePath = $request->file('file')->getPathname();

            $content = $this->ofxReaderService->read($ofxFilePath);

            $transactions = $content['BANKMSGSRSV1']['STMTTRNRS']['STMTRS']['BANKTRANLIST']['STMTTRN'] ?? [];
            $insertTransacations = [];

            foreach ($transactions as $transaction) {
                $value = isset($transaction['TRNAMT']) ? (float) $transaction['TRNAMT'] : 0.0;
                $description = $transaction['MEMO'] ?? '';
                $fitid = $transaction['FITID'] ?? null;

                if (empty($description)) {
                    $description = $value > 0 ? 'Entrada/Resgate' : 'Despesa/Aplicação';
                }

                // Parse OFX date format: 20251014112549[-3:BRT]
                $dtPostedRaw = $transaction['DTPOSTED'] ?? null;
                $datePosted = null;

                if ($dtPostedRaw) {
                    // Extract only the date/time part (first 14 digits)
                    if (preg_match('/^(\d{14})/', $dtPostedRaw, $matches)) {
                        $datePosted = Carbon::createFromFormat('YmdHis', $matches[1]);
                    }
                }

                $insertTransacations[] = [
                    'user_id' => Auth::id(),
                    'importacao_movimentacao_id' => $movimentacaoImportacao->id,
                    'valor' => $value,
                    'descricao' => $description,
                    'data_transacao' => $datePosted,
                    'conta_id' => $defaultAccount->id,
                    'categoria_id' => $value < 0 ? $othersCategoryExpense->id : $othersCategoryIncome->id,
                    'fitid' => $fitid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Movimentacao::upsert($insertTransacations, [
                'user_id',
                'fitid'
            ]);
        });

        return $movimentacaoImportacao;
    }

    public function confirmAllImport(int $id): void
    {
        $movimentacaoImportacao = MovimentacaoImportacao::findOrFail($id);

        Movimentacao::where('user_id', Auth::id())
            ->where('importacao_movimentacao_id', $movimentacaoImportacao->id)
            ->update(['importacao_movimentacao_id' => null]);

        MovimentacaoImportacao::where('user_id', Auth::id())
            ->where('id', $movimentacaoImportacao->id)
            ->delete();
    }
}
