SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS pedido_itens;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS produto_imagens;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT UNSIGNED NOT NULL,
    nome VARCHAR(160) NOT NULL,
    marca VARCHAR(80) NOT NULL,
    motor VARCHAR(80) NOT NULL,
    cavalos INT NOT NULL,
    aceleracao VARCHAR(20) NOT NULL,
    cor VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(12,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem_principal VARCHAR(255) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produtos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    CONSTRAINT chk_produtos_preco CHECK (preco >= 0),
    CONSTRAINT chk_produtos_estoque CHECK (estoque >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE produto_imagens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produto_id INT UNSIGNED NOT NULL,
    url VARCHAR(255) NOT NULL,
    CONSTRAINT fk_imagens_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    data_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forma_pagamento ENUM('pix', 'boleto', 'cartao') NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_pedidos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT chk_pedidos_total CHECK (total >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pedido_itens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_itens_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos(id),
    CONSTRAINT chk_itens_quantidade CHECK (quantidade > 0),
    CONSTRAINT chk_itens_preco CHECK (preco_unitario >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_produtos_categoria ON produtos(categoria_id);
CREATE INDEX idx_imagens_produto ON produto_imagens(produto_id);
CREATE INDEX idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX idx_itens_pedido ON pedido_itens(pedido_id);

INSERT INTO categorias (nome) VALUES
('Cupês'),
('Sedãs Esportivos'),
('Esportivos'),
('Hatchs Esportivos');

INSERT INTO produtos (categoria_id, nome, marca, motor, cavalos, aceleracao, cor, descricao, preco, estoque, imagem_principal) VALUES
((SELECT id FROM categorias WHERE nome='Cupês'), 'BMW M2 Coupé', 'BMW', '3.0 Biturbo 6 Cilindros', 480, '3.9s', 'Cinza', 'Motor 3.0 biturbo de seis cilindros, tração traseira e câmbio automático de 8 marchas.', 499950.00, 3, 'https://placehold.co/800x600/1a1a1a/ffffff?text=BMW+M2'),
((SELECT id FROM categorias WHERE nome='Sedãs Esportivos'), 'BMW M3 Competition', 'BMW', '3.0 Biturbo 6 Cilindros', 510, '3.9s', 'Azul', 'Sedã esportivo com suspensão adaptativa e diferencial M ativo.', 799950.00, 2, 'https://placehold.co/800x600/1a1a1a/ffffff?text=BMW+M3'),
((SELECT id FROM categorias WHERE nome='Cupês'), 'BMW M4 Competition', 'BMW', '3.0 Biturbo 6 Cilindros', 510, '3.9s', 'Verde', 'Cupê de alto desempenho com tração traseira.', 849950.00, 2, 'https://placehold.co/800x600/1a1a1a/ffffff?text=BMW+M4'),
((SELECT id FROM categorias WHERE nome='Sedãs Esportivos'), 'BMW M5', 'BMW', '4.4 V8 Biturbo Híbrido', 727, '3.5s', 'Preto', 'Sedã com motor V8 biturbo híbrido e tração integral M xDrive.', 1199950.00, 1, 'https://placehold.co/800x600/1a1a1a/ffffff?text=BMW+M5'),
((SELECT id FROM categorias WHERE nome='Esportivos'), 'Porsche 911 Carrera S', 'Porsche', '3.0 Biturbo 6 Cilindros', 450, '3.5s', 'Branco', 'Flat-six com PDK de 8 marchas e chassi refinado.', 1349000.00, 1, 'https://placehold.co/800x600/1a1a1a/ffffff?text=Porsche+911'),
((SELECT id FROM categorias WHERE nome='Esportivos'), 'Porsche 718 Cayman GTS', 'Porsche', '4.0 Aspirado 6 Cilindros', 400, '4.0s', 'Amarelo', 'Motor aspirado com câmbio manual de 6 marchas disponível.', 749000.00, 2, 'https://placehold.co/800x600/1a1a1a/ffffff?text=Porsche+718'),
((SELECT id FROM categorias WHERE nome='Sedãs Esportivos'), 'Mercedes-AMG CLA 45 S', 'Mercedes-Benz', '2.0 Turbo 4 Cilindros', 421, '4.0s', 'Cinza', 'Tração integral 4MATIC+ e câmbio de dupla embreagem.', 549900.00, 3, 'https://placehold.co/800x600/1a1a1a/ffffff?text=CLA+45'),
((SELECT id FROM categorias WHERE nome='Sedãs Esportivos'), 'Mercedes-AMG CLA 35', 'Mercedes-Benz', '2.0 Turbo 4 Cilindros', 306, '4.9s', 'Branco', 'Tração integral e pacote AMG Line completo.', 389900.00, 4, 'https://placehold.co/800x600/1a1a1a/ffffff?text=CLA+35'),
((SELECT id FROM categorias WHERE nome='Hatchs Esportivos'), 'Audi RS3 Sportback', 'Audi', '2.5 Turbo 5 Cilindros', 400, '3.8s', 'Vermelho', 'Tração quattro e aceleração brutal.', 479900.00, 3, 'https://placehold.co/800x600/1a1a1a/ffffff?text=RS3'),
((SELECT id FROM categorias WHERE nome='Esportivos'), 'Audi RS6 Avant', 'Audi', '4.0 V8 Biturbo', 600, '3.6s', 'Cinza', 'Perua esportiva com tração quattro e suspensão pneumática.', 1249900.00, 1, 'https://placehold.co/800x600/1a1a1a/ffffff?text=RS6'),
((SELECT id FROM categorias WHERE nome='Esportivos'), 'Chevrolet Corvette C8', 'Chevrolet', '6.2 V8 Aspirado', 495, '2.9s', 'Vermelho', 'V8 central-traseiro com câmbio de dupla embreagem.', 1190000.00, 2, 'https://placehold.co/800x600/1a1a1a/ffffff?text=Corvette+C8'),
((SELECT id FROM categorias WHERE nome='Cupês'), 'Mercedes-AMG GT 63', 'Mercedes-Benz', '4.0 V8 Biturbo', 585, '3.4s', 'Prata', 'Cupê de quatro portas com tração 4MATIC+.', 1450000.00, 1, 'https://placehold.co/800x600/1a1a1a/ffffff?text=AMG+GT+63');

INSERT INTO produto_imagens (produto_id, url)
SELECT p.id,
       CONCAT('https://placehold.co/800x600/1a1a1a/ffffff?text=',
              REPLACE(p.nome, ' ', '+'), '+', n.n)
FROM produtos p
CROSS JOIN (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) AS n;