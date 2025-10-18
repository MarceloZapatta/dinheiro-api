<?php

namespace App\Services;

use App\Cliente;
use Illuminate\Http\Request;

class ClientesService {
    public function get(Request $request)
    {
        $clientes = Cliente::latest();

        if ($request->busca) {
            $numeros = preg_replace('/[^0-9]/', '', $request->busca);

            $clientes->where(function ($query) use ($request, $numeros) {
                $query->where('documento', $numeros)
                    ->orWhere('nome', 'LIKE', '%' . $request->busca . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->busca . '%');
            });
        }

        return Cliente::paginate(15);
    }

    public function store(Request $request)
    {
        $request->merge([
            'organizacao_id' => $request->organizacao_id,
            'documento' => str_replace(['.', '-', '/'], '', $request->documento),
            'cep' => str_replace('-', '', $request->cep)
        ]);

        $data = $request->only([
            'organizacao_id',
            'nome',
            'email',
            'documento'
        ]);

        $dataEndereco = $request->only('endereco');
        $dataEndereco['endereco']['cep'] = str_replace('-', '', $request->endereco['cep']);
        $data = array_merge($data, $dataEndereco['endereco']);

        return Cliente::create($data);
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'organizacao_id' => $request->organizacao_id,
            'documento' => str_replace(['.', '-', '/'], '', $request->documento)
        ]);

        $data = $request->only([
            'organizacao_id',
            'nome',
            'email',
            'documento'
        ]);

        $dataEndereco = $request->only('endereco');
        $dataEndereco['endereco']['cep'] = str_replace('-', '', $request->endereco['cep']);
        $data = array_merge($data, $dataEndereco['endereco']);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($data);
    }

    public function delete($id)
    {
        return Cliente::where('id', $id)->delete();
    }

    public function find($id)
    {
        return Cliente::findOrFail($id);
    }
}
