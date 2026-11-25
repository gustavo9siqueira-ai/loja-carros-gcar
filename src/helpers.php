<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function definirFlash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem
    ];
}

function obterFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function estaLogado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin';
}

function exigirLogin(): void
{
    if (!estaLogado()) {
        definirFlash('danger', 'Você precisa estar logado para acessar essa página.');
        redirecionar('login.php');
    }
}

function exigirAdmin(): void
{
    exigirLogin();
    if (!isAdmin()) {
        definirFlash('danger', 'Acesso negado. Área restrita para administradores.');
        redirecionar('../index.php');
    }
}

function limparEntrada(string $valor): string
{
    return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
}