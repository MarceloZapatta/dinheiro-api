<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterIndexOrganizacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('organizacao_pessoas', function (Blueprint $table) {
            $table->dropForeign(['pessoa_id']);
            $table->dropUnique('organizacao_id');
            $table->unique(['pessoa_id', 'organizacao_id']);
            $table->foreign('pessoa_id')->references('id')->on('pessoas');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('organizacao_pessoas', function (Blueprint $table) {
            $table->dropForeign(['organizacao_id']);
            $table->dropForeign(['pessoa_id']);
            $table->dropUnique(['pessoa_id', 'organizacao_id']);
            $table->foreign('organizacao_id')->references('id')->on('organizacoes');
            $table->foreign('pessoa_id')->references('id')->on('pessoas');
            $table->unique('pessoa_id', 'organizacao_id');
        });
        Schema::enableForeignKeyConstraints();
    }
}
