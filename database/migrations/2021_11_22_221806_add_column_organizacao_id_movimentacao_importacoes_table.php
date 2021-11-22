<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOrganizacaoIdMovimentacaoImportacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimentacao_importacoes', function (Blueprint $table) {
            $table->foreignId('organizacao_id');
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimentacao_importacoes', function (Blueprint $table) {
            $table->dropForeign(['organizacao_id']);
            $table->dropColumn('organizacao_id');
        });
    }
}
