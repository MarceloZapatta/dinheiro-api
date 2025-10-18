<?php

namespace App\Http\Middleware;

use Closure;

class OrganizacaoPjMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $organizacaoTipoIdPessoaJuridica = 2;
        
        if (
            request()->organizacao_atual->organizacao_tipo_id !==
            $organizacaoTipoIdPessoaJuridica
        ) {
            abort(403, 'Acesso negado. Tipo de organização inválida.');
        }

        return $response;
    }
}
