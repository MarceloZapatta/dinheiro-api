<?php

namespace App\Traits;

use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TokenHeader {
    // Retorna o token JWT de um user criado
    public function tokenHeader(User $user = null) {
        if (!$user) {
            $user = factory('App\User')->create();
        }
        
        $headers = [
            'Authorization' => 'Bearer ' . JWTAuth::fromUser($user)
        ];

        return $headers;
    }
}