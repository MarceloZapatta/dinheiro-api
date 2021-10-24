<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizacao_id');
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
            $table->string('nome')->unique();
            $table->string('icone');
            $table->unsignedBigInteger('cor_id');
            $table->foreign('cor_id')->references('id')->on('cores');
            $table->decimal('saldo')->default(0);
            $table->decimal('saldo_inicial')->default(0);
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
        Schema::dropIfExists('contas');
    }
}
