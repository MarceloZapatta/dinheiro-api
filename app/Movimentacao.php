<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    protected $table = 'movimentacoes';

    protected $fillable = [
        'organizacao_id',
        'descricao',
        'observacoes',
        'valor',
        'data_transacao',
        'conta_id',
        'categoria_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }
}
