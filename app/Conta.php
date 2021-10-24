<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    protected $fillable = ['organizacao_id', 'nome', 'saldo', 'saldo_inicial', 'cor_id', 'icone'];
}
