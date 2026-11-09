<?php

namespace Src;
class Resposta
{
    public function responder(string $rota, string $mensagem)
    {
        if ($rota === '/') {
            return [
                'status' => 200,
                'rota' => $rota,
                'mensagem' => $mensagem
            ];
        }
        return [
            'status' => 404,
            'rota' => $rota,
            'mensagem' => $mensagem
        ];
    }
}