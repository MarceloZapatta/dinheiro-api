<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    public function store(Request $request): \App\Models\User
    {
        $request->merge([
            'password' => Hash::make($request->password)
        ]);

        return User::create($request->only('nome', 'email', 'password'));
    }
}
