<?php

use App\Enums\AccountType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('nome')->unique();
            $table->string('icone')->nullable();
            $table->unsignedBigInteger('cor_id');
            $table->foreign('cor_id')->references('id')->on('cores');
            $table->decimal('saldo_inicial')->default(0);
            $table->enum('account_type', ['bank', 'credit_card', 'investment'])->default('bank');
            $table->integer('closing_day')->nullable();
            $table->integer('due_day')->nullable();
            $table->decimal('credit_limit', 10, 2)->nullable();
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
        Schema::dropIfExists('contas');
    }
}
