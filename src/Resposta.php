<?php

namespace Src;

class Resposta
{
    private const int STATUS_OK = 200;
    private const int STATUS_NOT_FOUND = 404;
    public function responder(string $caminho, string $versao): array
    {
        if ($caminho === '/') {
            return ['status' => self::STATUS_OK, 'corpo' => 'pagina inicial'];
        }
    
        if ($caminho === '/saude') {
            return ['status' => self::STATUS_OK, 'corpo' => json_encode(['status' => 'ok', 'versao' => $versao])];
        }
        return ['status' => self::STATUS_NOT_FOUND, 'corpo' => 'pagina nao encontrada'];
    }
}
