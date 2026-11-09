<?php
ini_set('display_errors', '0');

try {
    require_once __DIR__ . '/../src/Resposta.php';
    $caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $resposta = new \Src\Resposta();
    $resultado = $resposta->responder($caminho, getenv('APP_VERSAO') ?: 'dev');
    http_response_code($resultado['status']);
    echo $resultado['corpo'];
} catch (Throwable $e) {
    error_log($e);
    http_response_code(500);
    echo 'Erro interno do servidor';
    exit;
}