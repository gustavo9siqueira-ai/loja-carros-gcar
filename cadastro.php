<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/src/helpers.php';

if (estaLogado()) {
    redirecionar('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparEntrada($_POST['nome'] ?? '');
    $email = limparEntrada($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        definirFlash('danger', 'Preencha todos os campos.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        definirFlash('danger', 'E-mail inválido.');
    } else {
        $pdo = conexao();
        
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            definirFlash('danger', 'Este e-mail já está cadastrado.');
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nome, $email, $senhaHash, 'cliente']);
            
            definirFlash('success', 'Conta criada com sucesso! Faça login para continuar.');
            redirecionar('login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - GCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h2>GCAR</h2>
            <p class="text-center text-muted mb-4">Crie sua conta</p>
            
            <?php if ($flash = obterFlash()): ?>
                <div class="alert alert-<?= $flash['tipo'] ?> alert-dismissible fade show" role="alert">
                    <?= $flash['mensagem'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark">Cadastrar</button>
            </form>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none text-secondary">Já tenho uma conta</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>