<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoImportacao extends Model
{
    protected $table = 'movimentacao_importacoes';
    protected $fillable = ['user_id', 'arquivo'];

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'importacao_movimentacao_id');
    }
}
