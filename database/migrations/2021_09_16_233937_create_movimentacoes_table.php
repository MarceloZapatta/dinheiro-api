<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimentacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizacao_id');
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
            $table->string('descricao');
            $table->string('observacoes', 300);
            $table->decimal('valor');
            $table->date('data_transacao');
            $table->foreignId('conta_id')->constrained();
            $table->foreignId('categoria_id')->constrained();
            $table->foreignId('recorrencia_id')->constrained()->nullable()->default(NULL);
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
        Schema::dropIfExists('movimentacoes');
    }
}
