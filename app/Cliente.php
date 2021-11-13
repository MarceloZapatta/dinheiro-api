<?php

namespace App;

use App\Traits\WithOrganizacao;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use WithOrganizacao;

    protected $fillable = [
        'organizacao_id',
        'nome',
        'email',
        'documento',
        'cep',
        'rua',
        'complemento',
        'numero',
        'uf_id',
        'cidade'
    ];

    public function uf()
    {
        return $this->belongsTo(Uf::class);
    }
}
