-- =====================================================
-- MIGRATION - Sistema de Pedidos AlphaCode (Docker)
-- =====================================================
-- Este arquivo é executado automaticamente pelo Docker
-- Para instalação manual, use: database/migration.sql
-- =====================================================

-- =====================================================
-- TABELA: USUARIO
-- Armazena os usuários do sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS USUARIO (
    ID_USUARIO INT AUTO_INCREMENT PRIMARY KEY,
    NOME VARCHAR(100) NOT NULL,
    EMAIL VARCHAR(255) NOT NULL UNIQUE,
    SENHA VARCHAR(255) NOT NULL,
    DATA_CRIACAO TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (EMAIL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: LOJA_USUARIO
-- Relaciona usuários com suas lojas/estabelecimentos
-- =====================================================
CREATE TABLE IF NOT EXISTS LOJA_USUARIO (
    ID_LOJA_USUARIO INT AUTO_INCREMENT PRIMARY KEY,
    ID_USUARIO INT NOT NULL,
    NOME_LOJA VARCHAR(200) NOT NULL,
    CNPJ VARCHAR(14) UNIQUE,
    ENDERECO TEXT,
    TELEFONE VARCHAR(30),
    DATA_CRIACAO TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_USUARIO) REFERENCES USUARIO(ID_USUARIO) ON DELETE CASCADE,
    INDEX idx_usuario (ID_USUARIO),
    INDEX idx_cnpj (CNPJ)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: CLIENTE
-- Armazena os clientes de cada loja
-- =====================================================
CREATE TABLE IF NOT EXISTS CLIENTE (
    ID_CLIENTE INT AUTO_INCREMENT PRIMARY KEY,
    ID_LOJA_USUARIO INT NOT NULL,
    NOME_CLIENTE VARCHAR(100) NOT NULL,
    CPF CHAR(11) NOT NULL,
    EMAIL VARCHAR(255),
    TELEFONE VARCHAR(30),
    CEP VARCHAR(8),
    LOGRADOURO VARCHAR(255),
    NUMERO VARCHAR(20),
    COMPLEMENTO VARCHAR(100),
    BAIRRO VARCHAR(100),
    CIDADE VARCHAR(100),
    ESTADO CHAR(2),
    DATA_CRIACAO TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_LOJA_USUARIO) REFERENCES LOJA_USUARIO(ID_LOJA_USUARIO) ON DELETE CASCADE,
    UNIQUE KEY unique_cpf_loja (CPF, ID_LOJA_USUARIO),
    INDEX idx_loja_usuario (ID_LOJA_USUARIO),
    INDEX idx_cpf (CPF),
    INDEX idx_nome (NOME_CLIENTE)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: PRODUTO
-- Catálogo de produtos de cada loja
-- =====================================================
CREATE TABLE IF NOT EXISTS PRODUTO (
    ID_PRODUTO INT AUTO_INCREMENT PRIMARY KEY,
    ID_LOJA_USUARIO INT NOT NULL,
    COD_BARRAS VARCHAR(50) NOT NULL,
    NOME_PRODUTO VARCHAR(200) NOT NULL,
    DESCRICAO TEXT,
    VALOR_UNITARIO DECIMAL(12,2) NOT NULL,
    IMAGEM_URL VARCHAR(500),
    DATA_CRIACAO TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_LOJA_USUARIO) REFERENCES LOJA_USUARIO(ID_LOJA_USUARIO) ON DELETE CASCADE,
    UNIQUE KEY unique_cod_barras_loja (COD_BARRAS, ID_LOJA_USUARIO),
    INDEX idx_loja_usuario (ID_LOJA_USUARIO),
    INDEX idx_cod_barras (COD_BARRAS),
    INDEX idx_nome (NOME_PRODUTO)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: PEDIDO
-- Pedidos realizados pelos clientes
-- =====================================================
CREATE TABLE IF NOT EXISTS PEDIDO (
    ID_PEDIDO INT AUTO_INCREMENT PRIMARY KEY,
    ID_LOJA_USUARIO INT NOT NULL,
    ID_CLIENTE INT NOT NULL,
    DATA_PEDIDO DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    STATUS ENUM('EM_ABERTO','PAGO','CANCELADO') NOT NULL DEFAULT 'EM_ABERTO',
    OBSERVACAO TEXT,
    DATA_ATUALIZACAO TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_LOJA_USUARIO) REFERENCES LOJA_USUARIO(ID_LOJA_USUARIO) ON DELETE CASCADE,
    FOREIGN KEY (ID_CLIENTE) REFERENCES CLIENTE(ID_CLIENTE) ON DELETE RESTRICT,
    INDEX idx_loja_usuario (ID_LOJA_USUARIO),
    INDEX idx_cliente (ID_CLIENTE),
    INDEX idx_status (STATUS),
    INDEX idx_data (DATA_PEDIDO)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: ITEM_PEDIDO
-- Itens que compõem cada pedido
-- =====================================================
CREATE TABLE IF NOT EXISTS ITEM_PEDIDO (
    ID_ITEM INT AUTO_INCREMENT PRIMARY KEY,
    ID_PEDIDO INT NOT NULL,
    ID_PRODUTO INT NOT NULL,
    QUANTIDADE INT NOT NULL,
    VALOR_UNITARIO DECIMAL(12,2) NOT NULL,
    VALOR_TOTAL DECIMAL(12,2) GENERATED ALWAYS AS (QUANTIDADE * VALOR_UNITARIO) STORED,
    FOREIGN KEY (ID_PEDIDO) REFERENCES PEDIDO(ID_PEDIDO) ON DELETE CASCADE,
    FOREIGN KEY (ID_PRODUTO) REFERENCES PRODUTO(ID_PRODUTO) ON DELETE RESTRICT,
    INDEX idx_pedido (ID_PEDIDO),
    INDEX idx_produto (ID_PRODUTO)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VIEW: Resumo de Pedidos
-- =====================================================
CREATE OR REPLACE VIEW VW_PEDIDOS_RESUMO AS
SELECT 
    p.ID_PEDIDO,
    p.ID_LOJA_USUARIO,
    lu.NOME_LOJA,
    p.ID_CLIENTE,
    c.NOME_CLIENTE,
    c.CPF,
    p.DATA_PEDIDO,
    p.STATUS,
    COALESCE(SUM(ip.VALOR_TOTAL), 0) AS VALOR_TOTAL_PEDIDO,
    COUNT(ip.ID_ITEM) AS QTD_ITENS
FROM PEDIDO p
LEFT JOIN LOJA_USUARIO lu ON p.ID_LOJA_USUARIO = lu.ID_LOJA_USUARIO
LEFT JOIN CLIENTE c ON p.ID_CLIENTE = c.ID_CLIENTE
LEFT JOIN ITEM_PEDIDO ip ON p.ID_PEDIDO = ip.ID_PEDIDO
GROUP BY p.ID_PEDIDO, p.ID_LOJA_USUARIO, lu.NOME_LOJA, p.ID_CLIENTE, 
         c.NOME_CLIENTE, c.CPF, p.DATA_PEDIDO, p.STATUS;

