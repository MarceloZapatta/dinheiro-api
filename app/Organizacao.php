<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organizacao extends Model
{
    protected $table = 'organizacoes';
    protected $fillable = ['pessoa_responsavel_id', 'documento', 'nome', 'organizacao_tipo_id'];

    public function organizacaoPessoas()
    {
        return $this->hasMany(OrganizacaoPessoa::class);
    }

    public function organizacaoTipo()
    {
        return $this->belongsTo(OrganizacaoTipo::class);
    }

    protected static function booted()
    {
        static::creating(function (Organizacao $organizacao) {
            $organizacao->hash = Str::uuid();
        });
    }
}
