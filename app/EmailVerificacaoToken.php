<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailVerificacaoToken extends Model
{
    protected $fillable = array(
        'usuario_id',
        'token',
    );
}
