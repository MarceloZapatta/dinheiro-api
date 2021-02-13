<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Hash;

class Users
{
    public function store($nome, $email, $senha)
    {
        return User::create([
            'nome' => $nome,
            'email' => $email,
            'password' =>  Hash::make($senha)
        ]);
    }
}
