<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/src/helpers.php';

$pdo = conexao();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($action === 'add' && $id) {
        $qtd = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
        $qtd = $qtd ? $qtd : 1;

        $stmt = $pdo->prepare("SELECT estoque FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch();

        if ($produto) {
            $qtdAtual = $_SESSION['carrinho'][$id] ?? 0;
            $novaQtd = $qtdAtual + $qtd;

            if ($novaQtd <= $produto['estoque']) {
                $_SESSION['carrinho'][$id] = $novaQtd;
                definirFlash('success', 'Produto adicionado ao carrinho!');
            } else {
                definirFlash('danger', 'Quantidade solicitada excede o estoque disponível.');
            }
        }
        redirecionar('carrinho.php');
    }

    if ($action === 'update' && $id) {
        $qtd = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
        
        if ($qtd > 0) {
            $stmt = $pdo->prepare("SELECT estoque FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
            $produto = $stmt->fetch();

            if ($produto && $qtd <= $produto['estoque']) {
                $_SESSION['carrinho'][$id] = $qtd;
                definirFlash('success', 'Quantidade atualizada.');
            } else {
                definirFlash('danger', 'Estoque insuficiente para essa quantidade.');
            }
        } else {
            unset($_SESSION['carrinho'][$id]);
            definirFlash('success', 'Item removido do carrinho.');
        }
        redirecionar('carrinho.php');
    }

    if ($action === 'remove' && $id) {
        unset($_SESSION['carrinho'][$id]);
        definirFlash('success', 'Item removido do carrinho.');
        redirecionar('carrinho.php');
    }
}

$itensCarrinho = [];
$totalGeral = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("SELECT id, nome, preco, imagem_principal, estoque FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll();

    foreach ($produtos as $p) {
        $qtd = $_SESSION['carrinho'][$p['id']];
        $subtotal = $p['preco'] * $qtd;
        $totalGeral += $subtotal;
        
        $itensCarrinho[] = [
            'id' => $p['id'],
            'nome' => $p['nome'],
            'preco' => $p['preco'],
            'imagem' => $p['imagem_principal'],
            'quantidade' => $qtd,
            'estoque' => $p['estoque'],
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - GCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">GCAR</a>
            <div class="d-flex ms-auto">
                <a href="index.php" class="btn btn-outline-light btn-sm">Continuar Comprando</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <h2 class="fw-bold mb-4">Seu Carrinho</h2>

        <?php if ($flash = obterFlash()): ?>
            <div class="alert alert-<?= $flash['tipo'] ?> alert-dismissible fade show" role="alert">
                <?= $flash['mensagem'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($itensCarrinho)): ?>
            <div class="alert alert-info py-4 text-center">
                <h5 class="mb-0">Seu carrinho está vazio.</h5>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Qtd.</th>
                                <th>Subtotal</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itensCarrinho as $item): ?>
                                <tr>
                                    <td class="text-start py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($item['imagem']) ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($item['nome']) ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                    <td>
                                        <form method="POST" action="carrinho.php" class="d-flex justify-content-center">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="0" max="<?= $item['estoque'] ?>" class="form-control text-center" style="width: 70px;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="fw-bold">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                                    <td>
                                        <form method="POST" action="carrinho.php" class="m-0">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <h4 class="fw-bold mb-0">Total: R$ <?= number_format($totalGeral, 2, ',', '.') ?></h4>
                <a href="checkout.php" class="btn btn-dark px-5 py-2 fw-bold">Finalizar Compra</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>