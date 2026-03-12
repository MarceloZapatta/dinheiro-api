<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('nome');
            $table->unsignedBigInteger('cor_id');
            $table->foreign('cor_id')->references('id')->on('cores');
            $table->string('icone');
            $table->boolean('expense')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'nome', 'expense']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias');
    }
}
