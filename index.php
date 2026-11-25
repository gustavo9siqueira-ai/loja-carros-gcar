<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/src/helpers.php';

$pdo = conexao();

$busca = trim($_GET['busca'] ?? '');
$categoriaId = trim($_GET['categoria'] ?? '');

$sql = "SELECT p.*, c.nome AS categoria_nome FROM produtos p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.ativo = 1";
$params = [];

if ($busca !== '') {
    $sql .= " AND (p.nome LIKE ? OR p.marca LIKE ?)";
    $params[] = "%{$busca}%";
    $params[] = "%{$busca}%";
}

if ($categoriaId !== '') {
    $sql .= " AND p.categoria_id = ?";
    $params[] = $categoriaId;
}

$sql .= " ORDER BY p.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCAR - Loja de Carros Esportivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">GCAR</a>
            <div class="d-flex ms-auto align-items-center gap-3">
                <?php if (estaLogado()): ?>
                    <span class="text-white">Olá, <?= explode(' ', $_SESSION['usuario_nome'])[0] ?></span>
                    <?php if (isAdmin()): ?>
                        <a href="admin/index.php" class="btn btn-outline-light btn-sm">Painel Admin</a>
                    <?php endif; ?>
                    <a href="carrinho.php" class="btn btn-light btn-sm text-dark fw-bold">Carrinho</a>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm">Entrar</a>
                    <a href="cadastro.php" class="btn btn-light btn-sm text-dark fw-bold">Cadastrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($flash = obterFlash()): ?>
            <div class="alert alert-<?= $flash['tipo'] ?> alert-dismissible fade show" role="alert">
                <?= $flash['mensagem'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="" class="d-flex gap-2">
                    <input type="text" name="busca" class="form-control" placeholder="Buscar por modelo ou marca..." value="<?= htmlspecialchars($busca) ?>">
                    <button type="submit" class="btn btn-dark px-4">Buscar</button>
                </form>
            </div>
            <div class="col-md-4">
                <form method="GET" action="">
                    <select name="categoria" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoriaId == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
            <?php foreach ($produtos as $produto): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="<?= htmlspecialchars($produto['imagem_principal']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produto['nome']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge bg-secondary mb-2"><?= htmlspecialchars($produto['categoria_nome']) ?></span>
                            <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($produto['nome']) ?></h5>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($produto['marca']) ?> | <?= htmlspecialchars($produto['cor']) ?></p>
                            
                            <div class="d-flex gap-3 mb-3 text-secondary small">
                                <span><strong><?= $produto['cavalos'] ?></strong> cv</span>
                                <span>0-100: <strong><?= $produto['aceleracao'] ?></strong></span>
                            </div>

                            <h4 class="fw-bold mb-3">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></h4>
                            <a href="produto.php?id=<?= $produto['id'] ?>" class="btn btn-outline-dark w-100">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($produtos)): ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">Nenhum veículo encontrado.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>