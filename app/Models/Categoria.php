<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cor_id',
        'icone'
    ];

    public function cor()
    {
        return $this->belongsTo(Cor::class);
    }
}
