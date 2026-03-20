<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimentacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('importacao_movimentacao_id')->nullable()->default(null);
            $table->foreign('importacao_movimentacao_id')->references('id')->on('movimentacao_importacoes');
            $table->string('descricao');
            $table->decimal('valor');
            $table->date('data_transacao');
            $table->foreignId('conta_id')->constrained();
            $table->foreignId('credit_card_invoice_id')->nullable()->constrained('credit_card_invoices');
            $table->unsignedInteger('installment_number')->nullable()->default(null);
            $table->unsignedInteger('total_installments')->nullable()->default(null);
            $table->unsignedBigInteger('installment_group_id')->nullable()->default(null);
            $table->unsignedBigInteger('movimentacao_relacao_id')->nullable()->default(null);
            $table->foreign('movimentacao_relacao_id')->references('id')->on('movimentacoes');
            $table->foreignId('categoria_id')->constrained();
            $table->string('refnum')->nullable()->default(null);
            $table->timestamps();

            $table->unique(['user_id', 'refnum']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimentacoes');
    }
}
