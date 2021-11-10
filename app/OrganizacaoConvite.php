<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizacaoConvite extends Model
{
    protected $fillable = [
        'email',
        'token',
        'organizacao_id'
    ];
}
