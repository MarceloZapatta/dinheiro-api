<?php

namespace App\Http\Middleware;

use Closure;

class OrganizacaoResponsavelMiddleware
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
        // dd(request()->organizacao_atual, auth('api')->user()->pessoa->id);
        if (request()->organizacao_atual->pessoa_responsavel_id !== auth('api')->user()->pessoa->id) {
            abort(403, 'Acesso negado. Apenas o responsável da organização pode fazer essa solicitação.');
        }

        return $response;
    }
}
