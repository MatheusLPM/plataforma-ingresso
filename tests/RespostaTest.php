<?php

use PHPUnit\Framework\TestCase;
use Src\Resposta;
require_once __DIR__ . '/../src/Resposta.php';

class RespostaTest extends TestCase
{
    function test_reponse_200()
    {
        $resposta = new Resposta();
        $ressultado = $resposta->responder('/', 'teste');
        $this->assertSame(200, $ressultado['status']);
    }
}