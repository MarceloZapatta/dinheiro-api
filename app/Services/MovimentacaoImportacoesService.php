<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Http\Requests\ImportImageRequest;
use App\Http\Requests\ImportOfxRequest;
use App\Imports\ExcelImport;
use App\Models\Categoria;
use App\Models\Conta;
use App\Imports\MovimentacoesImport;
use App\Models\Movimentacao;
use App\Models\MovimentacaoImportacao;
use App\Services\IA\IAServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MovimentacaoImportacoesService
{
    public function __construct(private readonly OfxReaderService $ofxReaderService, private readonly IAServiceInterface $iaService, private readonly CategoriasService $categoriasService) {}

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
                'user_id' => Auth::id(),
                'arquivo' => $request->file('file')->getPath()
            ]);

            $contaId = (int) $request->conta_id;
            $creditCardInvoiceId = $request->credit_card_invoice_id ? (int) $request->credit_card_invoice_id : null;

            Excel::import(new ExcelImport($movimentacaoImportacao, $contaId, $creditCardInvoiceId), $request->file('file'));
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

    public function importImage(ImportImageRequest $request): MovimentacaoImportacao
    {
        return DB::transaction(function () use ($request): MovimentacaoImportacao {
            $movimentacaoImportacao = MovimentacaoImportacao::create([
                'user_id' => Auth::id(),
                'arquivo' => $request->file('file')->getClientOriginalName()
            ]);

            $extractedTransactions = $this->iaService->extractTransactionsFromImage($request->file('file'));

            foreach ($extractedTransactions as $extractedTransaction) {
                [$incomeOthersCategory, $expenseOthersCategory] = $this->categoriasService->findOthersCategories();

                Movimentacao::create([
                    'user_id' => Auth::id(),
                    'importacao_movimentacao_id' => $movimentacaoImportacao->id,
                    'descricao' => $extractedTransaction->description,
                    'observacoes' => null,
                    'conta_id' => $request->conta_id,
                    'credit_card_invoice_id' => $request->credit_card_invoice_id ?? null,
                    'categoria_id' => $extractedTransaction->value < 0 ? $expenseOthersCategory->id : $incomeOthersCategory->id,
                    'valor' => $extractedTransaction->value,
                    'data_transacao' => $extractedTransaction->date,
                ]);
            }

            return $movimentacaoImportacao;
        });
    }

    public function importOfx(ImportOfxRequest $request)
    {
        $movimentacaoImportacao = null;

        [$othersCategoryIncome, $othersCategoryExpense] = $this->categoriasService->findOthersCategories();

        DB::transaction(function () use ($request, &$movimentacaoImportacao, $othersCategoryExpense, $othersCategoryIncome) {
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
                $refnum = $transaction['REFNUM'] ?? null;

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
                    'conta_id' => $request->conta_id,
                    'credit_card_invoice_id' => $request->credit_card_invoice_id ?? null,
                    'categoria_id' => $value < 0 ? $othersCategoryExpense->id : $othersCategoryIncome->id,
                    'refnum' => $refnum,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Movimentacao::upsert($insertTransacations, uniqueBy: [
                'user_id',
                'refnum'
            ], update: [
                'valor',
                'importacao_movimentacao_id',
                'data_transacao',
                'updated_at'
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
