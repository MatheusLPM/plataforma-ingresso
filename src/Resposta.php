<?php

namespace Src;

class Resposta
{
    private const int STATUS_NOT_FOUND = 404;
    public function responder(string $caminho, string $versao): array
    {
        if ($caminho === '/') {
            return ['status' => 200, 'corpo' => 'pagina inicial'];
        }
    
        if ($caminho === '/saude') {
            return ['status' => 200, 'corpo' => json_encode(['status' => 'ok', 'versao' => $versao])];
        }
        return ['status' => self::STATUS_NOT_FOUND, 'corpo' => 'pagina nao encontrada'];
    }
}
