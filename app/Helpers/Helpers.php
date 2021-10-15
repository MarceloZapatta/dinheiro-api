<?php

namespace App\Helpers;

class Helpers {
    /**
     * Limpa os pontos e traços do documento
     *
     * @param string $documento
     * @return string
     */
    public static function limparDocumento(string $documento = null): string
    {
        return $documento ? str_replace(['.', '/', '-'], '', $documento) : '';
    }
}
