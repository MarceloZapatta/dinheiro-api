<?php

namespace App;

use App\Traits\WithOrganizacao;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use WithOrganizacao;
    
    protected $fillable = [
        'organizacao_id',
        'nome',
        'cor_id',
        'icone'
    ];

    public function cor()
    {
        return $this->belongsTo(Cor::class);
    }
}
