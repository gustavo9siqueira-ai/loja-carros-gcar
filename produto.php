<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/src/helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    definirFlash('danger', 'Produto não encontrado.');
    redirecionar('index.php');
}

$pdo = conexao();

$stmt = $pdo->prepare("SELECT p.*, c.nome AS categoria_nome FROM produtos p JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ? AND p.ativo = 1");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    definirFlash('danger', 'Produto não encontrado ou indisponível.');
    redirecionar('index.php');
}

$stmtImg = $pdo->prepare("SELECT url FROM produto_imagens WHERE produto_id = ?");
$stmtImg->execute([$id]);
$imagens = $stmtImg->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produto['nome']) ?> - GCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">GCAR</a>
            <div class="d-flex ms-auto align-items-center gap-3">
                <a href="index.php" class="btn btn-outline-light btn-sm">Voltar para a Vitrine</a>
                <?php if (estaLogado()): ?>
                    <a href="carrinho.php" class="btn btn-light btn-sm text-dark fw-bold">Carrinho</a>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-light btn-sm text-dark fw-bold">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if ($flash = obterFlash()): ?>
            <div class="alert alert-<?= $flash['tipo'] ?> alert-dismissible fade show" role="alert">
                <?= $flash['mensagem'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-7 mb-4">
                <div id="carouselProduto" class="carousel slide shadow-sm rounded" data-bs-ride="carousel">
                    <div class="carousel-inner rounded">
                        <?php foreach ($imagens as $index => $img): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($img['url']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($produto['nome']) ?>" style="height: 500px; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselProduto" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselProduto" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <span class="badge bg-secondary mb-2 align-self-start"><?= htmlspecialchars($produto['categoria_nome']) ?></span>
                        <h2 class="fw-bold mb-1"><?= htmlspecialchars($produto['nome']) ?></h2>
                        <p class="text-muted mb-3"><?= htmlspecialchars($produto['marca']) ?></p>
                        
                        <h3 class="fw-bold text-dark mb-3">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></h3>

                        <div class="row g-2 mb-4">
                            <div class="col-6"><div class="p-2 bg-light rounded text-center"><small class="text-muted d-block">Motor</small><strong><?= htmlspecialchars($produto['motor']) ?></strong></div></div>
                            <div class="col-6"><div class="p-2 bg-light rounded text-center"><small class="text-muted d-block">Potência</small><strong><?= $produto['cavalos'] ?> cv</strong></div></div>
                            <div class="col-6"><div class="p-2 bg-light rounded text-center"><small class="text-muted d-block">0 a 100 km/h</small><strong><?= htmlspecialchars($produto['aceleracao']) ?></strong></div></div>
                            <div class="col-6"><div class="p-2 bg-light rounded text-center"><small class="text-muted d-block">Cor</small><strong><?= htmlspecialchars($produto['cor']) ?></strong></div></div>
                        </div>

                        <p class="text-secondary mb-4"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                        
                        <div class="mb-4">
                            <strong>Disponibilidade: </strong>
                            <?php if ($produto['estoque'] > 0): ?>
                                <span class="text-success fw-bold"><?= $produto['estoque'] ?> unidades em estoque</span>
                            <?php else: ?>
                                <span class="text-danger fw-bold">Esgotado</span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto">
                            <?php if ($produto['estoque'] > 0): ?>
                                <form method="POST" action="carrinho.php" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                    <input type="number" name="quantidade" class="form-control" value="1" min="1" max="<?= $produto['estoque'] ?>" style="width: 80px;">
                                    <button type="submit" class="btn btn-dark flex-grow-1 fw-bold">Adicionar ao Carrinho</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>Produto Esgotado</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>