<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'usuarios',
            function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('email');
                $table->string('senha');
                $table->boolean('ativo')->default(1);
                $table->boolean('email_verificado')->default(0);
                $table->dateTime('email_verified_at')->nullable()->default(null);
                $table->string('email_verificacao_token')->nullable()->default(null);
                $table->timestamps();
                $table->unique('email');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
