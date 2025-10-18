<?php

namespace App\Services;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    public function store(Request $request): \App\User
    {
        $request->merge([
            'senha' => Hash::make($request->senha)
        ]);

        return User::create($request->only('nome', 'email', 'senha'));
    }
}
