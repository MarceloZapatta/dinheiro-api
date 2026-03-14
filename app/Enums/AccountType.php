<?php

namespace App\Enums;

enum AccountType: string
{
    case BANK = 'bank';
    case INVESTMENT = 'investment';
    case CREDIT_CARD = 'credit_card';

    public function label(): string
    {
        return match ($this) {
            self::BANK => 'Banco',
            self::INVESTMENT => 'Investimento',
            self::CREDIT_CARD => 'Cartão de Crédito',
        };
    }
}
