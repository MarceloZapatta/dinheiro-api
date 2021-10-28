<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
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
