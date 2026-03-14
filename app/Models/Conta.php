<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'saldo_inicial',
        'cor_id',
        'icone',
        'user_id',
        'account_type',
        'closing_day',
        'due_day',
        'credit_limit',
    ];

    protected $casts = [
        'account_type' => AccountType::class,
    ];

    public function cor()
    {
        return $this->belongsTo(Cor::class);
    }

    public function creditCardInvoices()
    {
        return $this->hasMany(CreditCardInvoice::class);
    }

    public function isCreditCard(): bool
    {
        return $this->account_type === AccountType::CREDIT_CARD;
    }
}
