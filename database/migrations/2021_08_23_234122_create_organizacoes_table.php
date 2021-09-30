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
            $table->string('nome');
            $table->string('documento');
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
