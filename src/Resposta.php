<?php

namespace Src;

class Resposta
{
    public function responder(string $caminho, string $versao): array
    {
        if ($caminho === '/') {
            return ['status' => 200, 'corpo' => ''];
        }
        return ['status' => 404, 'corpo' => ''];
    }
}
