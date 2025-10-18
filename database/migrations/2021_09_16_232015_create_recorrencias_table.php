<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecorrenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recorrencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recorrencia_tipo_id');
            $table->foreign('recorrencia_tipo_id', 'recorrecia_tipo')->references('id')->on('recorrencia_tipos');
            $table->unsignedInteger('frequencia');
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
        Schema::dropIfExists('recorrencias');
    }
}
