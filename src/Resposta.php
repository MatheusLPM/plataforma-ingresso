<?php

namespace Src;

class Resposta
{
    private const int STATUS_OK = 200;
    public function responder(string $caminho, string $versao): array
    {
        if ($caminho === '/') {
            return ['status' => self::STATUS_OK, 'corpo' => 'pagina inicial'];
        }
    
        if ($caminho === '/saude') {
            return ['status' => self::STATUS_OK, 'corpo' => json_encode(['status' => 'ok', 'versao' => $versao])];
        }
        return ['status' => 404, 'corpo' => 'pagina nao encontrada'];
    }
}
