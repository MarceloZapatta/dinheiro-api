<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJunoLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('juno_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacao_id')->nullable()->default(NULL);
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
            $table->foreignId('movimentacao_id')->nullable()->default(NULL);
            $table->foreign('movimentacao_id')->references('id')->on('movimentacoes');
            $table->foreignId('usuario_id')->nullable()->default(NULL);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->string('dados')->nullable()->default(NULL);
            $table->string('mensagem')->nullable()->default(NULL);
            $table->string('code')->nullable()->default(NULL);
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
        Schema::dropIfExists('juno_logs');
    }
}
