<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consultor extends Model
{
    protected $table = 'consultores';
    protected $fillable = [
        'usuario_id',
        'resumo', 
        'imagem_capa'
    ];
}
