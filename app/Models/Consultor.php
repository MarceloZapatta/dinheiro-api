<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultor extends Model
{
    protected $table = 'consultores';
    protected $fillable = [
        'user_id',
        'resumo',
        'imagem_capa'
    ];
}
