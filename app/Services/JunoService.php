<?php

namespace App\Services;

use App\Cliente;
use App\Cobranca;
use App\IntegracaoDado;
use App\JunoLogs;
use App\Movimentacao;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class JunoService
{
    private $client;
    private $bearerToken;
    private $sandbox = false;

    function __construct()
    {
        $this->baseUrl = config('juno.api_url');

        if (strpos($this->baseUrl, 'sandbox.boletobancario.com') !== false) {
            $this->sandbox = true;
        }

        $this->bearerToken = $this->getBearerToken();
        $this->client = Http::withHeaders([
            'X-Api-Version' => 2,
            'Authorization' => 'Bearer ' . $this->bearerToken,
            'X-Resource-Token' => config('juno.x_resource_token')
        ]);
    }

    /**
     * Recebe o bearer token
     *
     * @return string
     */
    private function getBearerToken(): string
    {
        $bearerToken = Cache::get('juno.bearer_token');

        if ($bearerToken) {
            return $bearerToken;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->getBasicCredentials()
        ])
            ->asForm()
            ->post(
                $this->baseUrl . '/authorization-server/oauth/token',
                ['grant_type' => 'client_credentials']
            );

        if ($response->failed()) {
            throw new Exception('Ocorreu um erro ao tentar obter o bearer token JUNO: ' . $response->body());
        }

        $body = json_decode($response->body());

        Cache::put('juno.bearer_token', $body->access_token, $body->expires_in - 60);

        return $body->access_token;
    }

    private function getBasicCredentials()
    {
        return base64_encode(config('juno.client_id') . ':' . config('juno.client_secret'));
    }

    public function gerarLinkCadastro()
    {
        $response = $this->client
            ->post(
                $this->baseUrl . ($this->sandbox ? '/api-integration' : '') . '/onboarding/link-request',
                [
                    'type' => 'SIGNUP',
                    'referenceId' => request()->organizacao_id,
                    'autoTransfer' => true,
                    'emailOptOut' => false,
                    'returnUrl' => 'https://poupis.com.br' . '/?sucesso=' . urlencode('A conta foi cadastrada com sucesso! Agora é possível realizar emissão de cobranças.'),
                    'refreshUrl' => 'https://poupis.com.br' . '/?sucesso=' . urlencode('O link de cadastro está expirado. Por favor gere outro novamente pela página de integrações.'),
                ]
            );

        if ($response->failed()) {
            throw new Exception('Ocorreu um erro ao tentar gerar o link de cadastro: ' . $response->body(), $response->status());
        }

        return json_decode($response->body());
    }

    /**
     * Realiza a emissão de cobrança a um cliente
     *
     * @param Movimentacao $movimentacao
     * @param Cliente $cliente
     * @return mixed
     */
    public function emitirCobranca(Movimentacao $movimentacao)
    {
        $response = $this->client
            ->post(
                $this->baseUrl . ($this->sandbox ? '/api-integration' : '') . '/charges',
                [
                    'charge' => [
                        'pixKey' => '52b9120f-0c2b-4308-affc-46533ec4231a',
                        'pixIncludeImage' => true,
                        'description' => $movimentacao->descricao,
                        'amount' => $movimentacao->valor,
                        'dueDate' => $movimentacao->data_transacao,
                        'installments' => 1,
                        'maxOverdueDays' => 5,
                        'fine' => 0,
                        'interest' => 0,
                        'discountAmount' => 0,
                        'discountDays' => 0,
                        'paymentTypes' => [
                            'BOLETO_PIX',
                            'CREDIT_CARD'
                        ],
                        'paymentAdvance' => true
                    ],
                    'billing' => [
                        'name' => $movimentacao->cliente->nome,
                        'document' => $movimentacao->cliente->documento,
                        'email' => substr($movimentacao->cliente->email, 0, 80),
                        'address' => [
                            'street' => $movimentacao->cliente->rua,
                            'number' => $movimentacao->cliente->numero ?? 'N/A',
                            'complement' => $movimentacao->cliente->complemento,
                            'city' => $movimentacao->cliente->city,
                            'state' => $movimentacao->cliente->uf->nome,
                            'postCode' => $movimentacao->cliente->cep
                        ],
                        'notify' => true
                    ]
                ]
            );

        $user = auth('api')->user();

        JunoLogs::create([
            'movimentacao_id' => $movimentacao->id,
            'organizacao_id' => request()->organizacao_id,
            'usuario_id' => $user ? $user->id : null,
            'dados' => $response->body(),
            'code' => $response->status(),
            'mensagem' => 'Criação de cobrança'
        ]);

        if ($response->failed()) {
            throw new Exception('Ocorreu um erro ao tentar gerar  a cobrança ao cliente: ' . $response->body(), $response->status());
        }

        return json_decode($response->body());
    }

    public function gerarDataCobranca(Movimentacao $movimentacao, $cobranca)
    {
        $idPagamentoBoleto = 1;
        $cobranca = $cobranca->_embedded->charges[0];

        $data = [
            'integracao_id' => $cobranca->id,
            'movimentacao_id' => $movimentacao->id,
            'valor_pago' => null,
            'data_pagamento' => null,
            'status' => $cobranca->status,
            'pagamento_metodo_id' => $idPagamentoBoleto,
            'checkout_url' => $cobranca->checkoutUrl,
            'fatura_url' => $cobranca->link,
            'numero_pagamento' => $cobranca->payNumber,
            'pix_id' => $cobranca->pix->id,
            'pix_payload_base64' => $cobranca->pix->payloadInBase64,
            'pix_imagem_base64' => $cobranca->pix->imageInBase64,
            'numero_transacao' => null,
            'falha_razao' => null
        ];

        if ($cobranca->billetDetails) {
            $data['boleto_conta_banco'] = $cobranca->billetDetails->bankAccount;
            $data['boleto_nosso_numero'] = $cobranca->billetDetails->ourNumber;
            $data['boleto_codigo_barras_numero'] = $cobranca->billetDetails->barcodeNumber;
            $data['boleto_portfolio'] = $cobranca->billetDetails->portfolio;
        }

        if ($cobranca->pix) {
            $data['pix_payload_base64'] = $cobranca->pix->payloadInBase64;
            $data['pix_imagem_base64'] = $cobranca->pix->imageInBase64;
        }

        return $data;
    }

    public function cancelarCobranca(Cobranca $cobranca)
    {
        $response = $this->client
            ->put($this->baseUrl . ($this->sandbox ? '/api-integration' : '') . '/charges/' . $cobranca->integracao_id . '/cancelation');

        if ($response->failed()) {
            throw new Exception('Ocorreu um erro ao tentar cancelar a cobrança: ' . $response->body(), $response->status());
        }

        return json_decode($response->body());
    }
}
