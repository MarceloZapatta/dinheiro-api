<?php

use App\OrganizacaoTipo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizacaoTiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizacao_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 15);
        });

        OrganizacaoTipo::insert([
            [
                'tipo' => 'Pessoa física'
            ],
            [
                'tipo' => 'Pessoa júridica'
            ]
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizacao_tipos');
    }
}
