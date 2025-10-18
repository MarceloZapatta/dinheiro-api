<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsOrganizacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizacoes', function (Blueprint $table) {
            $table->dropColumn('telefone');
            $table->dropColumn('rua');
            $table->dropColumn('numero');
            $table->dropColumn('complemento');
            $table->dropColumn('cidade');
            $table->dropForeign(['uf_id']);
            $table->dropColumn('uf_id');
            $table->dropColumn('cep');
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
            $table->string('telefone')->nullable()->default(NULL);
            $table->string('rua')->nullable()->default(NULL);
            $table->string('numero')->nullable()->default(NULL);
            $table->string('complemento')->nullable()->default(NULL);
            $table->string('cidade')->nullable()->default(NULL);
            $table->foreignId('uf_id')->nullable()->default(NULL)->constrained();
            $table->string('cep')->nullable()->default(NULL);
        });
    }
}
