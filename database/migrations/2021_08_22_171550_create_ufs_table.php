<?php

use App\Uf;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ufs', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('sigla');
        });

        Uf::insert([
            [
                'nome' => 'AC',
                'sigla' => 'Acre'
            ],
            [
                'nome' => 'AL',
                'sigla' => 'Alagoas'
            ],
            [
                'nome' => 'AP',
                'sigla' => 'Amapá'
            ],
            [
                'nome' => 'AM',
                'sigla' => 'Amazonas'
            ],
            [
                'nome' => 'BA',
                'sigla' => 'Bahia'
            ],
            [
                'nome' => 'CE',
                'sigla' => 'Ceará'
            ],
            [
                'nome' => 'DF',
                'sigla' => 'Distrito Federal'
            ],
            [
                'nome' => 'ES',
                'sigla' => 'Espírito Santo'
            ],
            [
                'nome' => 'GO',
                'sigla' => 'Goiás'
            ],
            [
                'nome' => 'MA',
                'sigla' => 'Maranhão'
            ],
            [
                'nome' => 'MT',
                'sigla' => 'Mato Grosso'
            ],
            [
                'nome' => 'MS',
                'sigla' => 'Mato Grosso do Sul'
            ],
            [
                'nome' => 'MG',
                'sigla' => 'Minas Gerais'
            ],
            [
                'nome' => 'PA',
                'sigla' => 'Pará'
            ],
            [
                'nome' => 'PB',
                'sigla' => 'Paraíba'
            ],
            [
                'nome' => 'PR',
                'sigla' => 'Paraná'
            ],
            [
                'nome' => 'PE',
                'sigla' => 'Pernambuco'
            ],
            [
                'nome' => 'PI',
                'sigla' => 'Piauí'
            ],
            [
                'nome' => 'RJ',
                'sigla' => 'Rio de Janeiro'
            ],
            [
                'nome' => 'RN',
                'sigla' => 'Rio Grande do Norte'
            ],
            [
                'nome' => 'RS',
                'sigla' => 'Rio Grande do Sul'
            ],
            [
                'nome' => 'RO',
                'sigla' => 'Rondônia'
            ],
            [
                'nome' => 'RR',
                'sigla' => 'Roraima'
            ],
            [
                'nome' => 'SC',
                'sigla' => 'Santa Catarina'
            ],
            [
                'nome' => 'SP',
                'sigla' => 'São Paulo'
            ],
            [
                'nome' => 'SE',
                'sigla' => 'Sergipe'
            ],
            [
                'nome' => 'TO',
                'sigla' => 'Tocantins'
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
        Schema::dropIfExists('ufs');
    }
}
