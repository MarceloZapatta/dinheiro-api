<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegracaoDadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integracao_dados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integracao_id');
            $table->foreign('integracao_id')->references('id')->on('integracoes');
            $table->unsignedBigInteger('organizacao_id');
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
            $table->text('dados');
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
        Schema::dropIfExists('integracao_dados');
    }
}
