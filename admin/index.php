<?php
require_once __DIR__ . '/../src/helpers.php';
exigirAdmin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - GCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">GCAR - ADMIN</a>
            <div class="d-flex ms-auto gap-2">
                <a href="../index.php" class="btn btn-outline-light btn-sm">Ir para o Site</a>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="fw-bold mb-4">Painel de Controle</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-3">Gerenciar Produtos</h4>
                        <p class="text-muted">Cadastrar, editar preço/estoque ou desativar veículos do catálogo.</p>
                        <a href="produtos.php" class="btn btn-dark px-4">Acessar Produtos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-3">Pedidos Realizados</h4>
                        <p class="text-muted">Visualizar as vendas, clientes e os itens de cada pedido.</p>
                        <a href="pedidos.php" class="btn btn-dark px-4">Acessar Pedidos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>