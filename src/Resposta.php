<?php

namespace Src;

class Resposta
{
    public function responder(string $caminho, string $versao): array
    {
        if ($caminho === '/') {
            return ['status' => 200, 'corpo' => ''];
        }
    
        if ($caminho === '/saude') {
            return ['status' => 200, 'corpo' => json_encode(['status' => 'ok', 'versao' => $versao])];
        }
        return ['status' => 404, 'corpo' => ''];
    }
}
