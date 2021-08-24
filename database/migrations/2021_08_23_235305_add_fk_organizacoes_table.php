<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkOrganizacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizacoes', function (Blueprint $table) {
            $table->unsignedBigInteger('organizacao_tipo_id')->after('ativo');
            $table->foreign('organizacao_tipo_id')->references('id')->on('organizacao_tipos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizacoes', function (Blueprint $table) {
            $table->dropForeign(['organizacao_tipo_id']);
            $table->dropColumn('organizacao_tipo_id');
        });
    }
}
