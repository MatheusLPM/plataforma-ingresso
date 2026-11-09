<?php

use PHPUnit\Framework\TestCase;
use Src\Resposta;
require_once __DIR__ . '/../src/Resposta.php';

class RespostaTest extends TestCase
{
    public function test_raiz_responde_200() :void
    {
        $resposta = new Resposta();
        $resultado = $resposta->responder('/', 'teste');
        $this->assertSame(200, $resultado['status']);
    }

    public function test_caminho_desconhecido_responde_404() :void
    {
        $resposta = new Resposta();
        $resultado = $resposta->responder('/caminhodesconhecido', 'teste');
        $this->assertSame(404, $resultado['status']);
    }
}