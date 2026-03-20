<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\CreditCardInvoice;
use App\Models\Movimentacao;
use App\Models\MovimentacaoImportacao;
use App\Services\CreditCardService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToCollection, WithHeadingRow
{
    private const DATE_FORMAT = 'd/m/Y';
    private ?CreditCardInvoice $creditCardInvoice = null;
    private readonly CreditCardService $creditCardService;

    function __construct(
        private readonly MovimentacaoImportacao $movimentacaoImportacao,
        private readonly int $contaId,
        private readonly ?int $creditCardInvoiceId = null
    ) {
        $this->creditCardService = app(CreditCardService::class);
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $movimentacoes)
    {
        $categorias = Categoria::where('user_id', Auth::id())->get();
        $othersCategory = $categorias->where('nome', 'Outros')->values()[0];

        if (empty($othersCategory)) {
            throw new Exception('A categoria "Outros" é obrigatória para a importação de movimentações. Por favor, crie essa categoria e tente novamente.', 422);
        }

        foreach ($movimentacoes as $movimentacao) {
            if (empty($movimentacao['data']) || empty($movimentacao['categoria']) || empty($movimentacao['descricao']) || empty($movimentacao['valor'])) {
                throw new Exception('Excel inválido.', 422);
            }

            $categoriaMovimentacao = $categorias->where('nome', $movimentacao['categoria'])->values();
            $categoriaMovimentacao = !empty($categoriaMovimentacao[0]) ? $categoriaMovimentacao[0] : $othersCategory;

            if (empty($this->creditCardInvoice)) {
                $this->creditCardInvoice = $this->creditCardService->findInvoice($this->creditCardInvoiceId);
            }

            try {
                $transactionDate = $movimentacao['data'];

                // 1/2, 2/3, match this regex /^\d+\/\d+$/
                $matchesSpecificInstallmentFormat = preg_match('/^\d+\/\d+$/', $movimentacao['parcela']);

                if (
                    !empty($movimentacao['parcela']) &&
                    $movimentacao['parcela'] !== 'Única' &&
                    $matchesSpecificInstallmentFormat
                ) {
                    $installments = explode('/', $movimentacao['parcela']);
                    $currentInstallment = (int) $installments[0];
                    $totalInstallments = (int) $installments[1];

                    if ($currentInstallment !== 1 && $currentInstallment !== $totalInstallments) {
                        $transactionDate = Carbon::createFromFormat(self::DATE_FORMAT, $movimentacao['data'])
                            ->addMonths($currentInstallment - 1)
                            ->format(self::DATE_FORMAT);
                    }
                }

                $dataTransacao = Carbon::createFromFormat(self::DATE_FORMAT, $transactionDate);
            } catch (\Throwable $th) {
                $dataTransacao = Carbon::now();
            }

            if ($this->creditCardInvoice) {
                $movimentacao['valor'] *= -1;
            }

            Movimentacao::create([
                'user_id' => Auth::id(),
                'importacao_movimentacao_id' => $this->movimentacaoImportacao->id,
                'descricao' => $movimentacao['descricao'],
                'conta_id' => $this->contaId,
                'credit_card_invoice_id' => $this->creditCardInvoiceId,
                'installment_number' => $currentInstallment ?? null,
                'total_installments' => $totalInstallments ?? null,
                'installment_group_id' => null, // TODO: Implementar lógica para agrupar as parcelas e criar as parcelas futuras não realizadas :)
                'categoria_id' => $categoriaMovimentacao->id,
                'valor' => $movimentacao['valor'],
                'data_transacao' => $dataTransacao,
            ]);
        }
    }
}
