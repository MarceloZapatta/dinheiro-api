<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultorPessoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultor_pessoas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained();
            $table->unsignedBigInteger('consultor_id');
            $table->foreign('consultor_id')->references('id')->on('consultores');
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
        Schema::dropIfExists('consultor_pessoas');
    }
}
