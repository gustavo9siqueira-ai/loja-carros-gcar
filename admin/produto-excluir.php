<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../src/helpers.php';
exigirAdmin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $pdo = conexao();
    $stmt = $pdo->prepare("UPDATE produtos SET ativo = 0 WHERE id = ?");
    $stmt->execute([$id]);
    definirFlash('success', 'Produto desativado do catálogo.');
}

redirecionar('produtos.php');