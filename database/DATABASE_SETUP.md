# 🗄️ Configuração do Banco de Dados

Este documento explica como configurar o banco de dados MySQL para o projeto.

## 📋 Pré-requisitos

- MySQL 8.0 ou superior instalado
- Acesso ao MySQL via linha de comando ou MySQL Workbench
- Usuário com permissões para criar bancos de dados

## 🚀 Instalação Rápida

### Opção 1: Via Linha de Comando (Recomendado)

1. **Crie o banco de dados:**
```bash
mysql -u root -p -e "CREATE DATABASE alphacode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

2. **Execute a migration:**
```bash
mysql -u root -p alphacode_db < database/migration.sql
```

3. **Pronto!** O banco está configurado.

### Opção 2: Via MySQL Workbench

1. Abra o MySQL Workbench
2. Conecte-se ao servidor MySQL
3. Execute o seguinte comando para criar o banco:
   ```sql
   CREATE DATABASE alphacode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Vá em **File → Open SQL Script**
5. Selecione o arquivo `database/migration.sql`
6. Clique em **Execute** (raio ⚡)

### Opção 3: Docker (Já Incluído)

Se você está usando Docker Compose, o banco é criado automaticamente:
```bash
docker-compose up -d
```

O arquivo `docker/mysql/init.sql` será executado automaticamente na primeira inicialização.

## 📂 Estrutura de Arquivos

```
database/
├── migration.sql          # Script completo de criação do banco
└── DATABASE_SETUP.md      # Este arquivo

docker/
└── mysql/
    └── init.sql           # Script usado pelo Docker (pode estar desatualizado)
```

## 🔧 Configuração do .env

Certifique-se de que o arquivo `.env` está configurado corretamente:

```env
DB_HOST=localhost          # ou host.docker.internal se usar Docker
DB_PORT=3306
DB_NAME=alphacode_db
DB_USER=root               # ou seu usuário MySQL
DB_PASSWORD=sua_senha      # sua senha MySQL
```

## 📊 Estrutura do Banco de Dados

### Tabelas Criadas:

1. **USUARIO** - Usuários do sistema
2. **LOJA_USUARIO** - Lojas/estabelecimentos vinculados aos usuários
3. **CLIENTE** - Clientes de cada loja (CPF validado)
4. **PRODUTO** - Produtos disponíveis em cada loja
5. **PEDIDO** - Pedidos realizados pelos clientes
6. **ITEM_PEDIDO** - Itens de cada pedido (produtos e quantidades)

### Relacionamentos:

```
USUARIO (1) ──→ (N) LOJA_USUARIO
                      │
                      ├──→ (N) CLIENTE
                      ├──→ (N) PRODUTO
                      └──→ (N) PEDIDO ──→ (N) ITEM_PEDIDO
```

### Isolamento de Dados:

- ✅ Cada loja possui seus próprios clientes, produtos e pedidos
- ✅ Não há vazamento de dados entre lojas diferentes
- ✅ CPF único por loja (mesmo CPF pode existir em lojas diferentes)
- ✅ Código de barras único por loja

## 🧪 Dados de Teste (Opcional)

Para popular o banco com dados de exemplo, edite o arquivo `database/migration.sql` e descomente a seção **DADOS DE EXEMPLO** (linha ~140).

Os dados de teste incluem:
- 1 usuário (email: `admin@teste.com`, senha: `password`)
- 1 loja
- 2 clientes
- 5 produtos
- 1 pedido com 3 itens

## 🔄 Atualizar o Banco Existente

Se você já tem um banco criado e precisa atualizar:

```sql
-- Backup primeiro!
mysqldump -u root -p alphacode_db > backup_$(date +%Y%m%d).sql

-- Depois delete e recrie
DROP DATABASE alphacode_db;
CREATE DATABASE alphacode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Execute a migration novamente
mysql -u root -p alphacode_db < database/migration.sql
```

## 🛠️ Comandos Úteis

### Verificar tabelas criadas:
```sql
USE alphacode_db;
SHOW TABLES;
```

### Verificar estrutura de uma tabela:
```sql
DESCRIBE CLIENTE;
```

### Ver relacionamentos (Foreign Keys):
```sql
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'alphacode_db'
  AND REFERENCED_TABLE_NAME IS NOT NULL;
```

### Contar registros:
```sql
SELECT 
    'USUARIO' as tabela, COUNT(*) as registros FROM USUARIO
UNION ALL
SELECT 'LOJA_USUARIO', COUNT(*) FROM LOJA_USUARIO
UNION ALL
SELECT 'CLIENTE', COUNT(*) FROM CLIENTE
UNION ALL
SELECT 'PRODUTO', COUNT(*) FROM PRODUTO
UNION ALL
SELECT 'PEDIDO', COUNT(*) FROM PEDIDO
UNION ALL
SELECT 'ITEM_PEDIDO', COUNT(*) FROM ITEM_PEDIDO;
```

## ⚠️ Importante

1. **Nunca commite senhas reais** no repositório Git
2. **Faça backup** antes de executar qualquer script de atualização
3. **Use senhas fortes** em produção
4. **Altere as credenciais padrão** do `.env.example` antes do deploy

## 📞 Problemas?

### Erro: "Access denied for user"
- Verifique usuário e senha no arquivo `.env`
- Confirme que o usuário tem permissões suficientes

### Erro: "Database already exists"
- O banco já foi criado. Delete-o primeiro se quiser recriar:
  ```sql
  DROP DATABASE alphacode_db;
  ```

### Erro: "Connection refused"
- Verifique se o MySQL está rodando:
  ```bash
  # Windows
  net start MySQL80
  
  # Linux/Mac
  sudo systemctl start mysql
  ```

### Erro: "Table already exists"
- O script usa `CREATE TABLE IF NOT EXISTS`, então isso não deve acontecer
- Se acontecer, delete as tabelas manualmente ou recrie o banco

## 🎯 Próximos Passos

Após configurar o banco:

1. ✅ Configure o arquivo `.env`
2. ✅ Inicie o servidor: `docker-compose up -d` ou configure PHP manualmente
3. ✅ Acesse: `http://localhost:8080`
4. ✅ Registre um novo usuário em: `http://localhost:8080/registro`
5. ✅ Comece a usar o sistema!

---

Desenvolvido com ❤️ para AlphaCode
