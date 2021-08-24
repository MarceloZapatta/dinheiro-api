<?php

use App\OrganizacaoTipo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('tipo');
        });

        OrganizacaoTipo::insert([
            [
                'tipo' => 'Pessoa física'
            ],
            [
                'tipo' => 'Pessoa jurídica'
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
