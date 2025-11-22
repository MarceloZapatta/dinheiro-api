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
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('importacao_movimentacao_id')->nullable()->default(NULL);
            $table->foreign('importacao_movimentacao_id')->references('id')->on('movimentacao_importacoes');
            $table->string('descricao');
            $table->decimal('valor');
            $table->date('data_transacao');
            $table->foreignId('conta_id')->constrained();
            $table->foreignId('categoria_id')->constrained();
            $table->foreignId('recorrencia_id')->constrained()->nullable()->default(NULL);
            $table->string('fitid')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'fitid']);
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
