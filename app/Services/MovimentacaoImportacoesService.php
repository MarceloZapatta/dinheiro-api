<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Http\Requests\CreditCardInvoiceRequest;
use App\Http\Requests\ImportImageRequest;
use App\Http\Requests\ImportOfxRequest;
use App\Http\Requests\ImportRequest;
use App\Imports\ExcelImport;
use App\Models\Categoria;
use App\Models\Conta;
use App\Imports\MovimentacoesImport;
use App\Models\Movimentacao;
use App\Models\MovimentacaoImportacao;
use App\Services\IA\ExtractedTransactionDTO;
use App\Services\IA\IAServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class MovimentacaoImportacoesService
{
    public function __construct(
        private readonly OfxReaderService $ofxReaderService,
        private readonly IAServiceInterface $iaService,
        private readonly CategoriasService $categoriasService,
        private readonly CreditCardInvoiceService $creditCardInvoiceService,
        private readonly CategoryResolverService $categoryResolverService
    ) {}

    public function get()
    {
        return MovimentacaoImportacao::latest()->paginate(30);
    }

    public function show($id)
    {
        return MovimentacaoImportacao::with('movimentacoes')
            ->findOrFail($id);
    }

    public function batchImport(ImportRequest $request)
    {
        $movimentacaoImportacao = null;
        [$incomeOthersCategory, $expenseOthersCategory] = $this->categoriasService->findOthersCategories();

        DB::transaction(function () use ($request, &$movimentacaoImportacao, $incomeOthersCategory, $expenseOthersCategory) {
            $extractedTransactions = [];

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $request->file('files');

            $movimentacaoImportacao = MovimentacaoImportacao::create([
                'user_id' => Auth::id(),
                'arquivo' => $files ? implode(', ', array_map(fn($file) => $file->getClientOriginalName(), $files)) : 'Importação sem arquivo'
            ]);

            foreach ($files as $file) {
                if (in_array($file->getClientMimeType(), ['application/ofx', 'application/x-ofx', '.ofx'])) {
                    $extractedTransactions = array_merge($extractedTransactions, $this->ofxReaderService->extractTransactionsOfx($file->getPathname()));
                } elseif (str_starts_with($file->getClientMimeType(), 'image/')) {
                    Log::debug('Extracting transactions from image:', ['file' => $file->getClientOriginalName()]);
                    $extractedTransactions = array_merge($extractedTransactions, $this->iaService->extractTransactionsFromImage($file));
                    Log::debug('Extracted transactions from image:', ['transactions' => $extractedTransactions]);
                } else {
                    Log::warning('Unsupported file type for import:', ['file' => $file->getClientOriginalName(), 'mime_type' => $file->getClientMimeType()]);
                    throw new \Exception('Unsupported file type: ' . $file->getClientMimeType());
                }
            }

            $accountType = AccountType::tryFrom($request->account_type) ?? AccountType::BANK;

            $insertTransacations = [];

            $originalCreditCardInvoice = null;

            foreach ($extractedTransactions as $extractedTransaction) {
                $value = $accountType === AccountType::CREDIT_CARD ? ($extractedTransaction->value * -1) : $extractedTransaction->value;

                $transactionDate = Carbon::parse($extractedTransaction->date);

                if ($accountType === AccountType::CREDIT_CARD) {
                    if (empty($originalCreditCardInvoice)) {
                        $originalCreditCardInvoice = $this->creditCardInvoiceService->find($request->conta_id, $request->credit_card_invoice_id);
                    }
                    if ($transactionDate->isBefore(Carbon::parse($originalCreditCardInvoice->reference_date))) {
                        $referenceDate = Carbon::parse($originalCreditCardInvoice->reference_date);

                        $transactionDate = $transactionDate->copy()->setMonth($referenceDate->month)->setYear($referenceDate->year);
                    }
                }

                $installmentReference = Uuid::uuid4()->toString();

                $resolvedCategory = $this->categoryResolverService->resolveCategory($extractedTransaction->description, $value < 0);

                $insertTransacations[] = [
                    'user_id' => Auth::id(),
                    'importacao_movimentacao_id' => $movimentacaoImportacao->id,
                    'descricao' => $extractedTransaction->description,
                    'conta_id' => $request->conta_id,
                    'credit_card_invoice_id' => $request->credit_card_invoice_id ?? null,
                    'valor' => $value,
                    'categoria_id' => $resolvedCategory->id,
                    'data_transacao' => $transactionDate,
                    'refnum' => $extractedTransaction->refnum,
                    'installment_number' => $extractedTransaction->installmentNumber,
                    'total_installments' => $extractedTransaction->totalInstallments,
                    'installments_reference' => $installmentReference,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($extractedTransaction->installmentNumber && $extractedTransaction->totalInstallments) {
                    $this->processTransactionInstallments(
                        $insertTransacations,
                        $extractedTransaction,
                        $request,
                        $movimentacaoImportacao,
                        $resolvedCategory,
                        $value,
                        $transactionDate,
                        $installmentReference
                    );
                }
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

    private function processTransactionInstallments(
        array &$insertTransacations,
        ExtractedTransactionDTO $extractedTransaction,
        ImportRequest $request,
        MovimentacaoImportacao $movimentacaoImportacao,
        Categoria $resolvedCategory,
        float $value,
        Carbon $transactionDate,
        string $installmentReference,
    ) {
        $count = 0;
        for ($i = $extractedTransaction->installmentNumber + 1; $i <= $extractedTransaction->totalInstallments; $i++) {
            if (empty($originalCreditCardInvoice)) {
                $originalCreditCardInvoice = $this->creditCardInvoiceService->find($request->conta_id, $request->credit_card_invoice_id);
            }

            $invoiceDueDate = Carbon::parse($originalCreditCardInvoice->due_date)->addMonths($count);
            $invoiceReferenceDate = $invoiceDueDate->copy()->startOfMonth();

            $creditCardInvoice = $this->creditCardInvoiceService->getFromDate($request->conta_id, $invoiceReferenceDate);

            if (!$creditCardInvoice) {
                $closingDate = Carbon::parse($originalCreditCardInvoice->closing_date)->addMonths($count);
                $data = [
                    'reference_date' => $invoiceReferenceDate->toDateString(),
                    'due_date' => $invoiceDueDate->toDateString(),
                    'closing_date' => $closingDate->toDateString(),
                    'is_paid' => false,
                ];

                Validator::make($data, (new CreditCardInvoiceRequest())->rules())->validate();

                $creditCardInvoice = $this->creditCardInvoiceService->store($request->conta_id, $data);
            }

            $insertTransacations[] = [
                'user_id' => Auth::id(),
                'importacao_movimentacao_id' => $movimentacaoImportacao->id,
                'descricao' => $extractedTransaction->description,
                'conta_id' => $request->conta_id,
                'credit_card_invoice_id' => $creditCardInvoice->id,
                'valor' => $value,
                'categoria_id' => $resolvedCategory->id,
                'data_transacao' => $transactionDate->copy()->addMonths($count + 1),
                'refnum' => $extractedTransaction->refnum ? ($extractedTransaction->refnum . "-{$i}") : null,
                'installment_number' => $i,
                'total_installments' => $extractedTransaction->totalInstallments,
                'installments_reference' => $installmentReference,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;
        }
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
            Log::debug('Transações extraídas da imagem:', ['transactions' => $extractedTransactions]);

            $accountType = AccountType::tryFrom($request->account_type) ?? AccountType::BANK;

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
                    'valor' => $accountType === AccountType::CREDIT_CARD ? ($extractedTransaction->value * -1) : $extractedTransaction->value,
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
