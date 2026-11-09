<?php

namespace Src;
class Resposta
{
    public function responder(string $caminho, string $versao) :array
    {
        if ($caminho === '/') {
            return [
                'status' => 200,
                'rota' => $caminho,
                'mensagem' => $versao
            ];
        }
        return [
            'status' => 404,
            'rota' => $caminho,
            'mensagem' => $versao
        ];
    }
}