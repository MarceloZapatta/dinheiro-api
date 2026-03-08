<?php

namespace App\Services;

use App\Models\Movimentacao;
use Illuminate\Support\Facades\DB;

class MonthlyReportService
{
    public function generateMonthlyReport(int $userId, string $startDate, string $endDate): array
    {
        $transactions = Movimentacao::where('user_id', $userId)
            ->whereBetween('data_transacao', [$startDate, $endDate])
            ->get();

        $incomeTotal = $transactions->filter(fn($transaction) => $transaction->valor > 0)->sum('valor');
        $outcomeTotal = $transactions->filter(fn($transaction) => $transaction->valor < 0)->sum('valor');

        return [
            'income_total' => (float) number_format($incomeTotal, 2, '.', ''),
            'outcome_total' => (float) number_format(abs($outcomeTotal), 2, '.', ''),
        ];
    }
}
