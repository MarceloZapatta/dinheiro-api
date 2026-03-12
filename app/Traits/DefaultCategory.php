<?php

declare(strict_types=1);

namespace App\Traits;

trait DefaultCategory
{
    public function getDefaultExpensesCategories(): array
    {
        return [
            [
                'nome' => 'Alimentação',
                'icone' => 'fastFood',
                'cor_id' => 3
            ],
            [
                'nome' => 'Moradia',
                'icone' => 'home',
                'cor_id' => 3
            ],
            [
                'nome' => 'Transporte',
                'icone' => 'car',
                'cor_id' => 3
            ],
            [
                'nome' => 'Saúde',
                'icone' => 'medkit',
                'cor_id' => 3
            ],
            [
                'nome' => 'Lazer',
                'icone' => 'beer',
                'cor_id' => 4
            ],
            [
                'nome' => 'Pets',
                'icone' => 'paw',
                'cor_id' => 3
            ],
            [
                'nome' => 'Compras',
                'icone' => 'cart',
                'cor_id' => 3
            ],
            [
                'nome' => 'Pagamentos',
                'icone' => 'calculator',
                'cor_id' => 8
            ],
            [
                'nome' => 'Esportes',
                'icone' => 'bicycle',
                'cor_id' => 5
            ],
            [
                'nome' => 'Viagens',
                'icone' => 'airplane',
                'cor_id' => 1
            ],
            [
                'nome' => 'Outros',
                'icone' => 'logoAndroid',
                'cor_id' => 6
            ],
            [
                'nome' => 'Transferência',
                'icone' => 'swap',
                'cor_id' => 7
            ]
        ];
    }

    public function getDefaultIncomesCategories(): array
    {
        return [
            [
                'nome' => 'Salário',
                'icone' => 'rocket',
                'cor_id' => 3
            ],
            [
                'nome' => 'Investimentos',
                'icone' => 'chartLine',
                'cor_id' => 3
            ],
            [
                'nome' => 'Presentes',
                'icone' => 'gift',
                'cor_id' => 3
            ],
            [
                'nome' => 'Aluguel',
                'icone' => 'home',
                'cor_id' => 3
            ],
            [
                'nome' => 'Vendas',
                'icone' => 'tags',
                'cor_id' => 3
            ],
            [
                'nome' => 'Outros',
                'icone' => 'logoAndroid',
                'cor_id' => 6
            ],
            [
                'nome' => 'Transferência',
                'icone' => 'swap',
                'cor_id' => 7
            ],
            [
                'nome' => 'Estorno',
                'icone' => 'undo',
                'cor_id' => 8
            ]
        ];
    }
}
