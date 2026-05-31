<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryResolverService
{
    /**
     * Resolves a category based on the provided description.
     */
    public function resolveCategory(string $description, bool $isExpense): Categoria
    {
        $categories = Cache::rememberForever('categories_' . Auth::id(), function (): Collection {
            return Categoria::where('user_id', Auth::id())->get();
        });

        if ($isExpense) {
            return match (true) {
                $this->containWords($description, ['açai', 'quitanda', 'minuto', 'rodosnack', 'supermercados', 'chimar']) => $categories->where('expense', 1)->firstWhere('nome', 'Alimentação'),
                $this->containWords($description, ['farmaconde', 'saude', 'farmamed', 'farma', 'raia', 'drogasil', 'droga']) => $categories->where('expense', 1)->firstWhere('nome', 'Saúde'),
                $this->containWords($description, ['viki', 'pastelari', 'casadecarne', 'churras']) => $categories->where('expense', 1)->firstWhere('nome', 'Lazer'),
                $this->containWords($description, ['valet', 'parking', 'posto', 'auto', 'ethanol', 'gasolina', 'alcool', 'etanol', 'petobras', 'ipiranga', 'box 15', 'box 015', 'chevrolet']) => $categories->where('expense', 1)->firstWhere('nome', 'Transporte'),
                $this->containWords($description, ['materiais', 'matieli']) => $categories->where('expense', 1)->firstWhere('nome', 'Moradia'),
                $this->containWords($description, ['saae', 'cpfl', 'seguro']) => $categories->where('expense', 1)->firstWhere('nome', 'Pagamentos'),
                $this->containWords($description, ['mercadolivre', 'shopee']) => $categories->where('expense', 1)->firstWhere('nome', 'Compras'),
                $this->containWords($description, ['max', 'cobasi']) => $categories->where('expense', 1)->firstWhere('nome', 'Pets'),
                default => $categories->where('expense', 1)->firstWhere('nome', 'Outros'),
            };
        }

        return match (true) {
            $this->containWords($description, ['pró-labore', 'labore', 'pro labore', 'pró labore', 'salario', 'pro-labore']) => $categories->where('expense', 0)->firstWhere('nome', 'Salário'),
            $this->containWords($description, ['convenio', 'blablacar', 'pix recebido', 'devolução', 'estorno']) => $categories->where('expense', 0)->firstWhere('nome', 'Estorno'),
            default => $categories->where('expense', 0)->firstWhere('nome', 'Outros'),
        };
    }

    /**
     * Helper to check if description contains any of the given keywords.
     */
    private function containWords(string $description, array $words): bool
    {
        $descriptionLower = mb_strtolower($description, 'UTF-8');
        
        foreach ($words as $word) {
            if (str_contains($descriptionLower, mb_strtolower($word, 'UTF-8'))) {
                return true;
            }
        }
        
        return false;
    }
}