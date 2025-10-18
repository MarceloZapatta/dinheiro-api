<?php

namespace App\Services;

use App\Movimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardsService
{
    public function porCategoria(Request $request)
    {
        $movimentacoes = Movimentacao::select('categorias.nome', DB::raw('CONCAT(\'#\', cores.hexadecimal) as cor'), DB::raw('SUM(valor) as valor'))
            ->whereNull('importacao_movimentacao_id')
            ->where('movimentacoes.organizacao_id', request()->organizacao_id)
            ->whereBetween('data_transacao', [
                Carbon::createFromFormat('d/m/Y', $request->data_inicio),
                Carbon::createFromFormat('d/m/Y', $request->data_fim)
            ])
            ->join('categorias', 'movimentacoes.categoria_id', '=', 'categorias.id')
            ->join('cores', 'cores.id', '=', 'categorias.cor_id');

        if ($request->tipo === 'despesas') {
            $movimentacoes->where('valor', '<', 0);
        } else {
            $movimentacoes->where('valor', '>=', 0);
        }

        $movimentacoes = $movimentacoes->groupBy('categoria_id')
            ->orderBy('categorias.nome')
            ->get();

        $labels = $movimentacoes->pluck('nome')->values()->toArray();
        $datasets = [
            [
                'data' => $movimentacoes->pluck('valor')->values()->map(function ($valor) {
                    return (float) $valor;
                })->toArray(),
                'backgroundColor' => $movimentacoes->pluck('cor')->values()->toArray(),
                'borderWidth' => 0
            ]
        ];

        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];

        return $data;
    }

    public function movimentacoesAnual(Request $request)
    {
        $movimentacoesDespesas = Movimentacao::select(DB::raw('DATE_FORMAT(movimentacoes.data_transacao, "%m") as mes'), DB::raw('SUM(valor) as valor'))
            ->whereNull('importacao_movimentacao_id')
            ->where('organizacao_id', request()->organizacao_id)
            ->whereBetween('data_transacao', [
                Carbon::createFromFormat('d/m/Y', $request->data_inicio),
                Carbon::createFromFormat('d/m/Y', $request->data_fim)
            ])
            ->where('valor', '<', 0)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $movimentacoesReceitas = Movimentacao::select(DB::raw('DATE_FORMAT(movimentacoes.data_transacao, "%m") as mes'), DB::raw('SUM(valor) as valor'))
            ->whereNull('importacao_movimentacao_id')
            ->where('organizacao_id', request()->organizacao_id)
            ->whereBetween('data_transacao', [
                Carbon::createFromFormat('d/m/Y', $request->data_inicio),
                Carbon::createFromFormat('d/m/Y', $request->data_fim)
            ])
            ->where('valor', '>=', 0)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $labels = [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ];

        $datasetDespesas = [];

        foreach ($labels as $key => $label) {
            $datasetDespesas[] = abs((float) ($movimentacoesDespesas->where('mes', str_pad($key + 1, 2, "0", STR_PAD_LEFT))[0]->valor ?? 0));
        }

        $datasetReceitas = [];

        foreach ($labels as $key => $label) {
            $datasetReceitas[] = (float) ($movimentacoesReceitas->where('mes', str_pad($key + 1, 2, "0", STR_PAD_LEFT))[0]->valor ?? 0);
        }

        $datasets = [
            [
                'label' => 'Receitas',
                'data' => $datasetReceitas,
                'backgroundColor' => '#00ff5f',
                'borderWidth' => 0
            ],
            [
                'label' => 'Despesas',
                'data' => $datasetDespesas,
                'backgroundColor' => '#ff4961',
                'borderWidth' => 0
            ],
        ];

        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];

        return $data;
    }
}
