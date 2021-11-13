<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacao_id');
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
            $table->string('nome');
            $table->string('email');
            $table->string('documento');
            $table->string('cep');
            $table->string('rua');
            $table->string('complemento');
            $table->string('numero');
            $table->string('cidade');
            $table->foreignId('uf_id')->constrained();
            $table->unique(['organizacao_id', 'documento']);
            $table->unique(['organizacao_id', 'email']);
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
        Schema::dropIfExists('clientes');
    }
}
