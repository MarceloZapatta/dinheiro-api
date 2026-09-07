<?php

namespace App\Services;

use App\Models\Movimentacao;

class MonthlyReportService
{
    public function generateMonthlyReport(int $userId, string $startDate, string $endDate): array
    {
        $transactions = Movimentacao::with('categoria')
            ->where('user_id', $userId)
            ->whereBetween('data_transacao', [$startDate, $endDate])
            ->get();

        $isEstorno = fn($transaction) => strcasecmp((string) $transaction->categoria?->nome, 'estorno') === 0;

        $regularTransactions = $transactions->reject($isEstorno);
        $estornoTotal = $transactions->filter($isEstorno)->sum(fn($transaction) => abs($transaction->valor));

        $incomeTotal = $regularTransactions->filter(fn($transaction) => $transaction->valor > 0)->sum('valor');
        $outcomeTotal = abs($regularTransactions->filter(fn($transaction) => $transaction->valor < 0)->sum('valor')) - $estornoTotal;

        $expensesByCategory = $regularTransactions
            ->filter(fn($transaction) => $transaction->valor < 0)
            ->groupBy('categoria_id')
            ->map(function ($categoryTransactions) {
                $categoria = $categoryTransactions->first()->categoria;

                return [
                    'category_id' => $categoria?->id,
                    'category_name' => $categoria?->nome ?? 'Sem categoria',
                    'total' => (float) number_format(abs($categoryTransactions->sum('valor')), 2, '.', ''),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->all();

        return [
            'income_total' => (float) number_format($incomeTotal, 2, '.', ''),
            'outcome_total' => (float) number_format($outcomeTotal, 2, '.', ''),
            'expenses_by_category' => $expensesByCategory,
        ];
    }
}
