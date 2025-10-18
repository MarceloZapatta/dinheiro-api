<?php

use App\Cor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cores', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('hexadecimal', 8)->unique();
            $table->timestamps();
        });

        Cor::insert([
            [
                'nome' => 'Magic Mint',
                'hexadecimal' => 'bcf6cdff'
            ],
            [
                'nome' => 'Carolina Blue',
                'hexadecimal' => '2e9bd6ff'
            ],
            [
                'nome' => 'Sheen Green',
                'hexadecimal' => 'a9e029ff'
            ],
            [
                'nome' => 'Emerald',
                'hexadecimal' => '21d989ff'
            ],
            [
                'nome' => 'Medium Champagne',
                'hexadecimal' => 'fcf6b1ff'
            ],
            [
                'nome' => 'Honey Yellow',
                'hexadecimal' => 'f7b32bff'
            ],
            [
                'nome' => 'Dark orange',
                'hexadecimal' => 'ff8800ff'
            ],
            [
                'nome' => 'Red Ryb',
                'hexadecimal' => 'f72c25ff'
            ],
            [
                'nome' => 'Pink Lavander',
                'hexadecimal' => 'eac4e7ff'
            ],
            [
                'nome' => 'Steel Pink',
                'hexadecimal' => 'b91bc8ff'
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
        Schema::dropIfExists('cores');
    }
}
