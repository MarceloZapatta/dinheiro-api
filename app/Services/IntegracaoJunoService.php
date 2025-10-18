<?php

namespace App\Services;

use App\IntegracaoDado;
use Carbon\Carbon;

class IntegracaoJunoService
{
    private $junoService;

    public function __construct(JunoService $junoService)
    {
        $this->junoService = $junoService;
    }

    public function obterLinkCadastro()
    {
        $integracaoJunoId = 1;

        $integracaoDado = IntegracaoDado::where('integracao_id', $integracaoJunoId)
            ->where('dados', 'LIKE', '%"onBoarding":%')
            ->first();

        if (
            $integracaoDado &&
            $integracaoDado->onBoarding &&
            Carbon::parse($integracaoDado->onBoarding->createdOn)
                ->lessThan(Carbon::now())
        ) {
            return $integracaoDado->onBoarding->url;
        }

        $response = $this->junoService->gerarLinkCadastro();

        $dados = [
            'onBoarding' => (array) $response
        ];

        IntegracaoDado::create([
            'integracao_id' => $integracaoJunoId,
            'organizacao_id' => request()->organizacao_id,
            'dados' => $dados
        ]);

        return $response->url;
    }
}
