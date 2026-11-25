<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../src/helpers.php';
exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirecionar('produtos.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = limparEntrada($_POST['nome']);
$marca = limparEntrada($_POST['marca']);
$categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
$preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
$estoque = filter_input(INPUT_POST, 'estoque', FILTER_VALIDATE_INT);
$imagem = limparEntrada($_POST['imagem_principal']);
$descricao = limparEntrada($_POST['descricao']);
$motor = limparEntrada($_POST['motor']);
$cavalos = filter_input(INPUT_POST, 'cavalos', FILTER_VALIDATE_INT);
$aceleracao = limparEntrada($_POST['aceleracao']);
$cor = limparEntrada($_POST['cor']);

$pdo = conexao();

if ($id) {
    $sql = "UPDATE produtos SET nome=?, marca=?, categoria_id=?, preco=?, estoque=?, imagem_principal=?, descricao=?, motor=?, cavalos=?, aceleracao=?, cor=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $marca, $categoria_id, $preco, $estoque, $imagem, $descricao, $motor, $cavalos, $aceleracao, $cor, $id]);
    definirFlash('success', 'Produto atualizado com sucesso!');
} else {
    $sql = "INSERT INTO produtos (nome, marca, categoria_id, preco, estoque, imagem_principal, descricao, motor, cavalos, aceleracao, cor, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $marca, $categoria_id, $preco, $estoque, $imagem, $descricao, $motor, $cavalos, $aceleracao, $cor]);
    
    $novoId = $pdo->lastInsertId();
    $stmtImg = $pdo->prepare("INSERT INTO produto_imagens (produto_id, url) VALUES (?, ?)");
    for ($i = 1; $i <= 4; $i++) {
        $stmtImg->execute([$novoId, $imagem]);
    }
    
    definirFlash('success', 'Produto cadastrado com sucesso!');
}

redirecionar('produtos.php');