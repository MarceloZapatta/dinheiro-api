<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cor extends Model
{
    protected $fillable = ['nome', 'hexadecimal'];
    protected $table = 'cores';
}
