<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cobranca extends Model
{
    protected $fillable = [
        'id',
        'integracao_id',
        'movimentacao_id',
        'valor_pago',
        'data_pagamento',
        'status',
        'pagamento_metodo_id',
        'checkout_url',
        'fatura_url',
        'numero_pagamento',
        'boleto_conta_banco',
        'boleto_nosso_numero',
        'boleto_codigo_barras_numero',
        'boleto_portfolio',
        'pix_id',
        'pix_payload_base64',
        'pix_imagem_base64',
        'numero_transacao',
        'falha_razao'
    ];
}
