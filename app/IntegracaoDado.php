<?php

namespace App;

use App\Traits\WithOrganizacao;
use Illuminate\Database\Eloquent\Model;

class IntegracaoDado extends Model
{
    use WithOrganizacao;

    protected $fillable = [
        'integracao_id',
        'organizacao_id',
        'dados'
    ];

    public function integracao()
    {
        return $this->belongsTo(Integracao::class, 'integracao_id');
    }
}
