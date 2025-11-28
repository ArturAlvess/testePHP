## 📋 Índice

- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Funcionalidades](#-funcionalidades)
- [Arquitetura](#-arquitetura)
- [Instalação](#-instalação)
- [Como Rodar](#-como-rodar)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Banco de Dados](#-banco-de-dados)
- [Padrões e Boas Práticas](#-padrões-e-boas-práticas)

## 🚀 Tecnologias Utilizadas

- **PHP 8.2** - Linguagem principal
- **MySQL 8.0** - Banco de dados relacional
- **Nginx** - Servidor web
- **Docker & Docker Compose** - Containerização
- **Bootstrap 5.3** - Framework CSS
- **Bootstrap Icons** - Ícones
- **PDO** - Abstração de banco de dados (proteção contra SQL Injection)


## 📦 Instalação

### Pré-requisitos

- [Docker](https://www.docker.com/get-started) instalado
- [Docker Compose](https://docs.docker.com/compose/install/) instalado
- Git (opcional)

### Passo a Passo

1. **Clone ou baixe o repositório**
```bash
git clone https://github.com/ArturAlvess/testePHP.git
cd testePHP
```

2. **Configure o arquivo .env**
```bash
cp .env.example .env
```

O arquivo `.env` está configurado para usar seu banco MySQL local:
```env
DB_HOST=host.docker.internal
DB_PORT=3306
DB_NAME=alphacode_db
DB_USER=root
DB_PASSWORD=artur123
```

> **Nota:** A aplicação está configurada para usar seu MySQL existente em `127.0.0.1:3306`

3. **Configure o Banco de Dados**

Execute o script de migration para criar todas as tabelas:

```bash
# Crie o banco de dados
mysql -u root -p -e "CREATE DATABASE alphacode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Execute a migration
mysql -u root -p alphacode_db < database/migration.sql
```

**Alternativa - MySQL Workbench:**
1. File → Open SQL Script → Selecione `database/migration.sql`
2. Execute o script (⚡)

> **📖 Documentação Completa:** Veja `database/DATABASE_SETUP.md` para instruções detalhadas, troubleshooting e dados de teste.

4. **Construa e inicie os containers**
```bash
docker-compose up -d --build
```

Este comando irá:
- Baixar as imagens necessárias (PHP e Nginx)
- Construir a imagem customizada do PHP
- Criar e iniciar os containers
- Conectar ao seu banco MySQL local

5. **Aguarde a inicialização**
```bash
# Verifique os logs para confirmar que tudo está OK
docker-compose logs -f
```

Aguarde até ver mensagens indicando que os containers estão prontos.

## 🎮 Como Rodar

### Acessar a Aplicação

Após a instalação, acesse no navegador:

**URL:** http://localhost:8080

### Comandos Úteis

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f php
docker-compose logs -f mysql
docker-compose logs -f nginx

# Acessar o container PHP
docker-compose exec php bash

# Acessar o MySQL
docker-compose exec mysql mysql -u alphacode_user -palphacode_pass alphacode_db

# Reinstalar do zero (CUIDADO: apaga todos os dados)
docker-compose down -v
docker-compose up -d --build
```

### Solução de Problemas

**Porta 8080 já em uso?**
```bash
# Edite docker-compose.yml e mude a porta:
ports:
  - "8081:80"  # Mude 8080 para 8081 ou outra porta livre
```

**Erro de permissão?**
```bash
# No Linux/Mac, pode ser necessário dar permissões:
sudo chown -R $USER:$USER .
```

**Banco não inicializa?**
```bash
# Recrie o volume do MySQL:
docker-compose down -v
docker-compose up -d
```


## 👤 Autor

**Artur de Miranda Alves**
- GitHub: [@ArturAlvess](https://github.com/ArturAlvess)
- Email: [alvesartur1010@gmail.com](mailto:artur@email.com)

## 📞 Suporte

Se encontrar algum problema ou tiver dúvidas:

1. Verifique a seção [Solução de Problemas](#solução-de-problemas)
2. Confira os logs: `docker-compose logs -f`
3. Abra uma issue no GitHub

---

