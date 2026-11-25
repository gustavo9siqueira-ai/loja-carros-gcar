<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/src/helpers.php';

exigirLogin();

if (empty($_SESSION['carrinho'])) {
    definirFlash('danger', 'Seu carrinho está vazio.');
    redirecionar('index.php');
}

$pdo = conexao();
$itensCarrinho = [];
$totalGeral = 0;

$ids = array_keys($_SESSION['carrinho']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id, nome, preco, estoque FROM produtos WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produtos = $stmt->fetchAll();

foreach ($produtos as $p) {
    $qtd = $_SESSION['carrinho'][$p['id']];
    $subtotal = $p['preco'] * $qtd;
    $totalGeral += $subtotal;
    $itensCarrinho[] = [
        'id' => $p['id'],
        'preco' => $p['preco'],
        'quantidade' => $qtd
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formaPagamento = $_POST['forma_pagamento'] ?? '';
    $telefone = limparEntrada($_POST['telefone'] ?? '');
    $cep = limparEntrada($_POST['cep'] ?? '');
    $endereco = limparEntrada($_POST['endereco'] ?? '');
    
    $metodosValidos = ['pix', 'boleto', 'cartao'];

    if (!in_array($formaPagamento, $metodosValidos) || empty($telefone) || empty($cep) || empty($endereco)) {
        definirFlash('danger', 'Preencha todos os dados de entrega e selecione o pagamento.');
    } else {
        try {
            $pdo->beginTransaction();

            $stmtPedido = $pdo->prepare("INSERT INTO pedidos (usuario_id, telefone, cep, endereco, forma_pagamento, total) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtPedido->execute([$_SESSION['usuario_id'], $telefone, $cep, $endereco, $formaPagamento, $totalGeral]);
            $pedidoId = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            $stmtBaixa = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");

            foreach ($itensCarrinho as $item) {
                $stmtItem->execute([$pedidoId, $item['id'], $item['quantidade'], $item['preco']]);
                
                $stmtBaixa->execute([$item['quantidade'], $item['id'], $item['quantidade']]);
                if ($stmtBaixa->rowCount() === 0) {
                    throw new Exception("Estoque insuficiente para o produto ID " . $item['id']);
                }
            }

            $pdo->commit();
            unset($_SESSION['carrinho']);
            
            definirFlash('success', 'Pedido #' . $pedidoId . ' realizado com sucesso!');
            redirecionar('index.php');

        } catch (Exception $e) {
            $pdo->rollBack();
            definirFlash('danger', 'Erro ao processar o pedido: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - GCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">GCAR</a>
            <div class="d-flex ms-auto">
                <a href="carrinho.php" class="btn btn-outline-light btn-sm">Voltar ao Carrinho</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">Finalizar Pedido</h3>
                        
                        <?php if ($flash = obterFlash()): ?>
                            <div class="alert alert-<?= $flash['tipo'] ?>"><?= $flash['mensagem'] ?></div>
                        <?php endif; ?>

                        <h5 class="mb-3">Resumo do Pedido</h5>
                        <ul class="list-group mb-4">
                            <?php foreach ($itensCarrinho as $item): ?>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Produto ID <?= $item['id'] ?></h6>
                                        <small class="text-muted">Quantidade: <?= $item['quantidade'] ?></small>
                                    </div>
                                    <span class="text-muted">R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <span class="fw-bold">Total (R$)</span>
                                <strong class="text-success">R$ <?= number_format($totalGeral, 2, ',', '.') ?></strong>
                            </li>
                        </ul>

                        <form method="POST" action="">
                            <h5 class="mb-3">Dados de Entrega</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Telefone / WhatsApp</label>
                                    <input type="text" name="telefone" class="form-control" placeholder="(11) 99999-9999" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CEP</label>
                                    <input type="text" name="cep" class="form-control" placeholder="00000-000" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Endereço Completo</label>
                                    <input type="text" name="endereco" class="form-control" placeholder="Rua, Número, Bairro, Cidade - Estado" required>
                                </div>
                            </div>

                            <h5 class="mb-3">Forma de Pagamento</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="forma_pagamento" id="pix" value="pix" required>
                                <label class="form-check-label" for="pix">PIX</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="forma_pagamento" id="boleto" value="boleto">
                                <label class="form-check-label" for="boleto">Boleto Bancário</label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="forma_pagamento" id="cartao" value="cartao">
                                <label class="form-check-label" for="cartao">Cartão de Crédito</label>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">Confirmar e Pagar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>