<?php

return [
    'api_url' => env('JUNO_API_URL', 'https://sandbox.boletobancario.com/authorization-server/oauth/token'),
    'client_id' => env('JUNO_API_CLIENT_ID', ''),
    'client_secret' => env('JUNO_API_CLIENT_SECRET', '')
];