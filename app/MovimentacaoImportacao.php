<?php

namespace App;

use App\Traits\WithOrganizacao;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoImportacao extends Model
{
    use WithOrganizacao;

    protected $table = 'movimentacao_importacoes';
    protected $fillable = ['organizacao_id', 'arquivo'];

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'importacao_movimentacao_id');
    }
}
