<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCobrancasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cobrancas', function (Blueprint $table) {
            $table->id();
            $table->string('integracao_id');
            $table->unsignedBigInteger('movimentacao_id');
            $table->foreign('movimentacao_id')->references('id')->on('movimentacoes');
            $table->double('valor_pago');
            $table->dateTime('data_pagamento');
            $table->string('status');
            $table->foreignId('pagamento_metodo_id')->constrained();
            $table->string('checkout_url');
            $table->string('fatura_url');
            $table->string('numero_pagamento');
            $table->string('boleto_conta_banco')->nullable()->defauult(NULL);
            $table->string('boleto_nosso_numero')->nullable()->default(NULL);
            $table->string('boleto_codigo_barras_numero')->nullable()->default(NULL);
            $table->string('boleto_portfolio')->nullale()->default(NULL);
            $table->string('pix_id')->nullable()->default(NULL);
            $table->string('pix_payload_base64')->nullable()->default(NULL);
            $table->string('pix_imagem_base64')->nullable()->default(NULL);
            $table->string('numero_transacao')->nullable()->default(NULL);
            $table->string('falha_razao')->nullable()->default(NULL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cobrancas');
    }
}
