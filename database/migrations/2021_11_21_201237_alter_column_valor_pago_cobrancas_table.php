<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnValorPagoCobrancasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            $table->decimal('valor_pago')->nullable()->default(NULL)->change();
            $table->dateTime('data_pagamento')->nullable()->default(NULL)->change();
            $table->text('pix_imagem_base64')->nullable()->default(NULL)->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            $table->decimal('valor_pago')->change();
            $table->dateTime('data_pagamento')->change();
            $table->string('pix_imagem_base64')->nullable()->default(NULL)->change();
        });
    }
}
