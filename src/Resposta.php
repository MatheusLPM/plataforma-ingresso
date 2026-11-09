<?php

namespace Src;
class Resposta
{
    public function responder(string $rota, string $mensagem)
    {
        return [
            'status' => 0,
            'rota' => $rota,
            'mensagem' => $mensagem
        ];
    }
}