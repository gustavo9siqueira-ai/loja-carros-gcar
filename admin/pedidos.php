<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../src/helpers.php';
exigirAdmin();

$pdo = conexao();
$pedidos = $pdo->query("SELECT p.*, u.nome AS cliente FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pedidos Realizados - GCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Voltar ao Painel</a>
        </div>
    </nav>
    <div class="container mb-5">
        <h3 class="fw-bold mb-4">Pedidos</h3>

        <?php foreach ($pedidos as $pedido): ?>
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <strong>Pedido #<?= $pedido['id'] ?></strong> 
                        <span class="text-muted ms-2"><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></span>
                    </div>
                    <div>
                        <span class="badge bg-secondary me-2"><?= strtoupper($pedido['forma_pagamento']) ?></span>
                        <span class="fw-bold text-success">R$ <?= number_format($pedido['total'], 2, ',', '.') ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?></p>
                    <p class="mb-2"><strong>Entrega:</strong> <?= htmlspecialchars($pedido['endereco']) ?> (CEP: <?= htmlspecialchars($pedido['cep']) ?>) - Tel: <?= htmlspecialchars($pedido['telefone']) ?></p>
                    <table class="table table-sm table-bordered mb-0 mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Preço Unit.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmtItens = $pdo->prepare("SELECT pi.*, pr.nome FROM pedido_itens pi JOIN produtos pr ON pi.produto_id = pr.id WHERE pi.pedido_id = ?");
                            $stmtItens->execute([$pedido['id']]);
                            $itens = $stmtItens->fetchAll();
                            foreach ($itens as $item):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['nome']) ?></td>
                                    <td><?= $item['quantidade'] ?></td>
                                    <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($pedidos)): ?>
            <div class="alert alert-info">Nenhum pedido realizado ainda.</div>
        <?php endif; ?>
    </div>
</body>
</html>