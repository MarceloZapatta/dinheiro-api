<?php

namespace App;

class Mensagem
{
    public static function erro($mensagem = 'Erro!', $campos = [], $codigo = 400)
    {
        $camposBase = [
            'sucesso' => false,
            'mensagem' => $mensagem,
            'status_codigo' => $codigo
        ];

        return array_merge($camposBase, $campos);
    }

    public static function sucesso($mensagem = 'Sucesso!', $campos = [], $codigo = 200)
    {
        $camposBase = [
            'sucesso' => true,
            'mensagem' => $mensagem,
            'status_codigo' => $codigo
        ];

        return array_merge($camposBase, $campos);
    }
}
