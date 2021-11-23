<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUniqueContasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropUnique(['nome']);
            $table->unique(['nome', 'organizacao_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropUnique(['nome', 'organizacao_id']);
            $table->unique('nome');
        });
    }
}
