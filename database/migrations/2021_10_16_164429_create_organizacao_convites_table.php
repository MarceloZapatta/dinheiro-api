<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizacaoConvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizacao_convites', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->unsignedBigInteger('organizacao_id');
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
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
        Schema::dropIfExists('organizacao_convites');
    }
}
