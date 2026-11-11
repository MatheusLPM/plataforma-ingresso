<?php

use PHPUnit\Framework\TestCase;
use Src\Resposta;
require_once __DIR__ . '/../src/Resposta.php';

class RespostaTest extends TestCase
{
    public function testRaizResponde200() :void
    {
        $resposta = new Resposta();
        $resultado = $resposta->responder('/', 'teste');
        $this->assertSame(200, $resultado['status']);
    }

    public function testRaizRespondePaginaInicial() :void
    {
        $resposta = new Resposta();
        $resultado = $resposta->responder('/', 'teste');
        $this->assertSame('pagina inicial', $resultado['corpo']);
    }

    public function testCaminhoDesconhecidoResponde404() :void
    {
        $resposta = new Resposta();
        $resultado = $resposta->responder('/caminhodesconhecido', 'teste');
        $this->assertSame(404, $resultado['status']);
    }

    public function testCaminhoDesconhecidoRespondeMensagem() :void
    {
        $resposta = new Resposta();
        $resultado = $resposta->responder('/caminhodesconhecido', 'teste');
        $this->assertSame('pagina nao encontrada', $resultado['corpo']);
    }

    public function testSaudeResponde200ComVersao() :void
    {
        $resposta = new Resposta();
        $resultado = $resposta->responder('/saude', 'teste');
        $this->assertSame(200, $resultado['status']);
        $this->assertSame(
            ['status' => 'ok', 'versao' => 'teste'],
            json_decode($resultado['corpo'], true)
        );
    }
}