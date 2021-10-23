<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizacaoPessoa extends Model
{
    protected $fillable = ['organizacao_id', 'pessoa_id'];
}
