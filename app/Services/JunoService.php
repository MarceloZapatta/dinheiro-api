<?php

namespace App\Services;

use App\IntegracaoDado;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class JunoService {
    private $client;
    private $bearerToken;

    function __construct()
    {
        $this->baseUrl = config('juno.api_url');
        $this->bearerToken = $this->getBearerToken();
    }

    /**
     * Recebe o bearer token
     *
     * @return string
     */ 
    private function getBearerToken(): string {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->getBasicCredentials()
        ])
            ->post($this->baseUrl . '/oauth/token', ['grant_type' => 'client_credentials']);

        if ($response->failed()) {
            throw new Exception('Ocorreu um erro ao tentar obter o bearer token JUNO');
        }

        $body = json_decode($response->body());

        Cache::put('juno.bearer_token', $body->access_token, $body->expires_in);

        return $body->access_token;
    }

    private function getBasicCredentials() {
        return base64_encode(config('juno.client_id') . ':' . config('juno.client_secret'));
    }

    public function storeDadosIntegracao(Request $request)
    {
        $dados = $request->only([
            'bussiness_area',
            'lines_of_bussiness',
            'bank_account.bank_number',
            'bank_account.bank_agency',
            'bank_account.account_number',
            'bank_account.account_complement_number',
            'bank_account.account_type',
            'monthly_income_or_revenue',
            'cnae',
            'establishment_date',
            'pep',
            'company_members',
        ]);


        
    }

    private function gerarLinkCadastro()
    {
        $this->client->get('')
    }
}
