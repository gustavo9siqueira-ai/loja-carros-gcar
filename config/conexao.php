<?php
require_once __DIR__ . '/env.php';

carregarEnv(__DIR__ . '/../.env');

function env(string $chave, ?string $padrao = null): ?string
{
    $valor = getenv($chave);

    return $valor === false ? $padrao : $valor;
}

function conexao(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env('DB_HOST');
    $porta = env('DB_PORT', '3306');
    $banco = env('DB_NAME');
    $usuario = env('DB_USER');
    $senha = env('DB_PASS', '');

    if (!$host || !$banco || !$usuario) {
        throw new RuntimeException('Variaveis de ambiente do banco nao configuradas.');
    }

    $dsn = "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4";

    $pdo = new PDO($dsn, $usuario, $senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}