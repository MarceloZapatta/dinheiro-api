<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacao_tipo_id')->constrained();
            $table->uuid('hash');
            $table->string('nome');
            $table->string('razao_social')->nullable()->default(NULL);
            $table->string('documento', 14)->nullable()->default(NULL);
            $table->string('email')->nullable()->default(NULL);
            $table->string('telefone')->nullable()->default(NULL);
            $table->string('rua')->nullable()->default(NULL);
            $table->string('numero')->nullable()->default(NULL);
            $table->string('complemento')->nullable()->default(NULL);
            $table->string('cidade')->nullable()->default(NULL);
            $table->foreignId('uf_id')->nullable()->default(NULL)->constrained();
            $table->string('cep')->nullable()->default(NULL);
            $table->unsignedBigInteger('pessoa_responsavel_id');
            $table->foreign('pessoa_responsavel_id')->references('id')->on('pessoas')->comment('Pessoa responsável pela organização.');
            $table->boolean('ativo')->default(1);
            $table->unique('documento');
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
        Schema::dropIfExists('organizacoes');
    }
}
