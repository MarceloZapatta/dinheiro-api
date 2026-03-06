<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    protected $table = 'movimentacoes';

    protected $fillable = [
        'user_id',
        'importacao_movimentacao_id',
        'descricao',
        'valor',
        'data_transacao',
        'conta_id',
        'categoria_id',
        'refnum'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function cobranca()
    {
        return $this->hasOne(Cobranca::class);
    }
}
