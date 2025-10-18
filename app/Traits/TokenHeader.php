<?php

namespace App\Traits;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TokenHeader
{
    // Retorna o token JWT de um user criado
    public function tokenHeader(User $user = null)
    {
        if (! $user) {
            $user = factory('App\Models\User')->create();
        }

        $headers = array(
            'Authorization' => 'Bearer ' . JWTAuth::fromUser($user),
        );

        return $headers;
    }
}
