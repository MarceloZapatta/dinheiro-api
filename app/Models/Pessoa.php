<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $fillable = ['documento', 'usuario_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
