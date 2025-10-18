<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JunoLogs extends Model
{
    protected $fillable = [
        'organizacao_id',
        'movimentacao_id',
        'usuario_id',
        'dados',
        'mensagem',
        'code'
    ];
}
