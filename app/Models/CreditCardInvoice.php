<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCardInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conta_id',
        'reference_date',
        'closing_date',
        'due_date',
        'amount',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'closing_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'is_paid' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }
}
