<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organizacao extends Model
{
    protected $table = 'organizacoes';
    protected $fillable = [
        'pessoa_responsavel_id', 
        'organizacao_tipo_id',
        'documento', 
        'nome', 
        'email',
        'razao_social'
    ];

    public function organizacaoPessoas()
    {
        return $this->hasMany(OrganizacaoPessoa::class);
    }

    public function organizacaoTipo()
    {
        return $this->belongsTo(OrganizacaoTipo::class);
    }

    public function organizacaoConvites()
    {
        return $this->hasMany(OrganizacaoConvite::class);
    }

    protected static function booted()
    {
        static::creating(function (Organizacao $organizacao) {
            $organizacao->hash = Str::uuid();
        });
    }
}
