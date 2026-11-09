<?php

namespace Src;
class Resposta
{
    public function responder(string $rota, string $mensagem)
    {
        return [
            'status' => 200,
            'rota' => $rota,
            'mensagem' => $mensagem
        ];
    }
}