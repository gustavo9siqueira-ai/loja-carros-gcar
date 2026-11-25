<?php
require_once __DIR__ . '/../config/conexao.php';

$email = 'gustavo02siqueira@gmail.com'; 
$pdo = conexao();

$stmt = $pdo->prepare("UPDATE usuarios SET perfil = 'admin' WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    echo 'Sucesso! O usuario ' . $email . ' agora e um Administrador.';
} else {
    echo 'Usuario nao encontrado. Cadastre-se primeiro no site.';
}