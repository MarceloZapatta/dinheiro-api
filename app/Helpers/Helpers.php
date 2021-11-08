<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

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

    /**
     * Remove o cache com base no wildcard
     *
     * @param string $keyWildcard
     * @return void
     */
    public static function flushCacheWildcard(string $keyWildcard)
    {
        DB::table('cache')
            ->where('key', 'like', 'poupis_cache' . $keyWildcard)
            ->delete();
    }
}
