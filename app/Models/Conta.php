<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'saldo',
        'saldo_inicial',
        'cor_id',
        'icone'
    ];

    public function cor()
    {
        return $this->belongsTo(Cor::class);
    }
}
