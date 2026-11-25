# GCAR - Loja de Carros Esportivos

Projeto final do 4º Bimestre. Aplicação web de comércio eletrônico (e-commerce) com fluxo completo de venda, desde a vitrine até a baixa de estoque e painel administrativo.

## Tecnologias Utilizadas
- PHP 8.x
- MySQL/MariaDB (com PDO)
- HTML5, CSS3 e Bootstrap 5
- Git

## Funcionalidades
- **Vitrine**: Listagem dinâmica, filtro por categoria e busca por nome/marca.
- **Detalhes**: Carrossel com 4 imagens, ficha técnica (motor, cavalos, 0-100, cor) e estoque.
- **Carrinho**: Gerenciado via `$_SESSION`, validação de estoque e cálculo de totais.
- **Checkout**: Simulação de pagamento (PIX, Boleto, Cartão), dados de entrega, criação do pedido e baixa automática no estoque.
- **Autenticação**: Cadastro e login de clientes com senhas criptografadas (`password_hash`).
- **Painel Admin**: Área restrita para gerenciar produtos (CRUD/desativar) e visualizar pedidos.

## Como rodar o projeto localmente

1. Instale o **XAMPP** e inicie os serviços **Apache** e **MySQL**.
2. Coloque a pasta do projeto dentro de `C:\xampp\htdocs\loja-carros`.
3. No **phpMyAdmin**, crie um banco de dados chamado `loja_carros` com collation `utf8mb4_unicode_ci`.
4. Importe o arquivo `database/schema.sql` no phpMyAdmin para criar as tabelas e a carga inicial.
5. Renomeie o arquivo `.env.example` para `.env` e configure suas credenciais (no XAMPP, o padrão é usuário `root` e senha em branco).
6. Acesse no navegador: `http://localhost/loja-carros/index.php`

## Conta de Administrador
Para acessar o painel admin (`/admin`), crie uma conta normal pelo site, depois edite o arquivo `database/criar_admin.php` com o seu e-mail e acesse-o no navegador. Em seguida, faça login novamente.