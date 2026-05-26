-- CREATE TABLE FOR USER CONTROL
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(150) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    contato VARCHAR(20) DEFAULT NULL,
    tipo_conta ENUM('Administrador', 'Gerente', 'Desenvolvedor') NOT NULL,
    foto_path VARCHAR(255) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INSERT DEFAULT ADMINISTRATOR USER
-- Email: admin@techmanager.com
-- Senha: password123
INSERT INTO usuarios (nome_completo, email, senha, contato, tipo_conta, foto_path) 
VALUES (
    'Administrador Principal', 
    'admin@techmanager.com', 
    '$2y$10$Yl4eFSY9e7WaXg8.I5Il4.fzUQpAImwHoyps1AbLMDwRaDnnSl6Bm', 
    '(11) 99999-9999', 
    'Administrador', 
    NULL
) ON DUPLICATE KEY UPDATE id=id;
