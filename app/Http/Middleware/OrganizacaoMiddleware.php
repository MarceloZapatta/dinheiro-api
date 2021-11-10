<?php

namespace App\Http\Middleware;

use App\Organizacao;
use Closure;

class OrganizacaoMiddleware
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
        $organizacaoHash = $request->header('X-Organizacao-Hash');

        if (!$organizacaoHash) {
            abort(403, 'O header X-Organizacao-Hash é obrigatório');
        }

        $organizacao = Organizacao::select('id', 'pessoa_responsavel_id')
            ->where('hash', $organizacaoHash)
            ->first();

        if (!$organizacao) {
            abort(403, 'X-Organizacao-Hash inválido.');
        }

        $request->request->add(['organizacao_id' => $organizacao->id]);
        $request->request->add(['organizacao_atual' => $organizacao]);

        return $next($request);
    }
}
