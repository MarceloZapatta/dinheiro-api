<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertPagamentoMetodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pagamento_metodos')->insert([
            [
                'nome' => 'Boleto'
            ],
            [
                'nome' => 'Cartão de crédito'
            ],
            [
                'nome' => 'Boleto / PIX'
            ],
            [
                'nome' => 'Boleto / Cartão de crédito'
            ],
            [
                'nome' => 'Boleto / PIX / Cartão de crédito'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('pagamento_metodos')->truncate();
    }
}
