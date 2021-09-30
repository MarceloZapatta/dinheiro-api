<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organizacao extends Model
{
    protected $table = 'organizacoes';
    protected $fillable = ['usuario_id', 'documento', 'nome', 'organizacao_tipo_id'];
}
