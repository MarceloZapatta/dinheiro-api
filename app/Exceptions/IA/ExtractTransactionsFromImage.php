<?php

namespace App\Exceptions\IA;

use Exception;

class ExtractTransactionsFromImage extends Exception
{
    protected $message = 'Ocorreu um erro ao extrair as transações da imagem. Por favor, tente novamente.';
}
