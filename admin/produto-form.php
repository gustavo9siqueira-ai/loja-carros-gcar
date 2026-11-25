<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../src/helpers.php';
exigirAdmin();

$pdo = conexao();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = [
    'id' => '', 'nome' => '', 'marca' => '', 'descricao' => '', 
    'preco' => '', 'estoque' => '', 'categoria_id' => '', 'imagem_principal' => '',
    'motor' => '', 'cavalos' => '', 'aceleracao' => '', 'cor' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Editar' : 'Novo' ?> Produto - GCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="produtos.php">Voltar para Lista</a>
        </div>
    </nav>
    <div class="container mb-5">
        <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
            <div class="card-body p-4">
                <h3 class="fw-bold mb-4"><?= $id ? 'Editar Produto' : 'Novo Produto' ?></h3>
                <form method="POST" action="produto-salvar.php">
                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($produto['marca']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoria</label>
                            <select name="categoria_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $produto['categoria_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" name="preco" class="form-control" value="<?= $produto['preco'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estoque</label>
                            <input type="number" name="estoque" class="form-control" value="<?= $produto['estoque'] ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">URL da Imagem Principal</label>
                            <input type="url" name="imagem_principal" class="form-control" value="<?= htmlspecialchars($produto['imagem_principal']) ?>" required>
                        </div>
                        
                        <div class="col-md-4 mt-4">
                            <label class="form-label">Motor</label>
                            <input type="text" name="motor" class="form-control" value="<?= htmlspecialchars($produto['motor'] ?? '') ?>" placeholder="Ex: 3.0 Biturbo" required>
                        </div>
                        <div class="col-md-4 mt-4">
                            <label class="form-label">Cavalos (cv)</label>
                            <input type="number" name="cavalos" class="form-control" value="<?= $produto['cavalos'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4 mt-4">
                            <label class="form-label">0 a 100 km/h</label>
                            <input type="text" name="aceleracao" class="form-control" value="<?= htmlspecialchars($produto['aceleracao'] ?? '') ?>" placeholder="Ex: 3.9s" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cor</label>
                            <input type="text" name="cor" class="form-control" value="<?= htmlspecialchars($produto['cor'] ?? '') ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="4" required><?= htmlspecialchars($produto['descricao']) ?></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 mt-4 py-2 fw-bold">Salvar Produto</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>