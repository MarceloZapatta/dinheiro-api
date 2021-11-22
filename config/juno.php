<?php

return [
    'api_url' => env('JUNO_API_URL', 'https://sandbox.boletobancario.com'),
    'client_id' => env('JUNO_API_CLIENT_ID', ''),
    'client_secret' => env('JUNO_API_CLIENT_SECRET', ''),
    'x_resource_token' => env('JUNO_API_TOKEN_PRIVADO', '')
];