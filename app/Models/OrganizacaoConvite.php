<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizacaoConvite extends Model
{
    protected $fillable = [
        'email',
        'token',
        'organizacao_id'
    ];
}
