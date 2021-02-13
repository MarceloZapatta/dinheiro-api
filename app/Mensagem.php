<?php

namespace App;

class Mensagem
{
    public static function erro($mensagem = 'Erro!', $campos = array(), $codigo = 400)
    {
        $camposBase = array(
            'sucesso' => false,
            'mensagem' => $mensagem,
            'status_codigo' => $codigo,
        );

        return array_merge($camposBase, $campos);
    }

    public static function sucesso($mensagem = 'Sucesso!', $campos = array(), $codigo = 200)
    {
        $camposBase = array(
            'sucesso' => true,
            'mensagem' => $mensagem,
            'status_codigo' => $codigo,
        );

        return array_merge($camposBase, $campos);
    }
}
