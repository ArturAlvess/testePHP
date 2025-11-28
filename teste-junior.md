# 🚀 Guia de Instalação Rápida

Este guia mostra como configurar o projeto do zero em uma nova máquina.

## ✅ Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- **PHP 8.2 ou superior**
- **MySQL 8.0 ou superior**
- **Composer** (gerenciador de dependências PHP)
- **Docker & Docker Compose** (opcional, mas recomendado)
- **Git** (para clonar o repositório)

## 📥 Passo 1: Clonar o Repositório

```bash
git clone https://github.com/ArturAlvess/testePHP.git
cd testePHP
```

## 🔧 Passo 2: Configurar Dependências

```bash
# Instalar dependências PHP
composer install

# Dar permissões (Linux/Mac)
chmod -R 777 public/uploads
```

## 🗄️ Passo 3: Configurar o Banco de Dados

### Criar o banco:

```bash
mysql -u root -p -e "CREATE DATABASE alphacode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Executar a migration:

```bash
mysql -u root -p alphacode_db < database/migration.sql
```

> 📖 **Para mais detalhes:** Veja `database/DATABASE_SETUP.md`

## ⚙️ Passo 4: Configurar o .env

```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar com suas credenciais
# Linux/Mac: nano .env
# Windows: notepad .env
```

Configuração mínima necessária:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=alphacode_db
DB_USER=root
DB_PASSWORD=sua_senha_aqui
```

## 🐳 Passo 5: Iniciar com Docker (Recomendado)

```bash
# Construir e iniciar containers
docker-compose up -d --build

# Ver logs
docker-compose logs -f
```

Pronto! Acesse: **http://localhost:8080**

## 🔧 Passo 5 (Alternativa): Iniciar sem Docker

Se preferir não usar Docker:

```bash
# Iniciar servidor PHP embutido
php -S localhost:8080 -t public/

# Ou configurar Apache/Nginx manualmente
```

## 🧪 Passo 6: Criar Primeiro Usuário

1. Acesse: http://localhost:8080/registro
2. Preencha os dados:
   - Nome da Loja
   - CNPJ (14 dígitos)
   - Nome do Usuário
   - Email
   - Senha
3. Faça login e comece a usar!

## ✨ Verificação Rápida

Execute estes comandos para verificar se tudo está OK:

```bash
# Verificar se o PHP está correto
php -v  # Deve mostrar 8.2 ou superior

# Verificar se o MySQL está acessível
mysql -u root -p -e "USE alphacode_db; SHOW TABLES;"

# Deve listar: USUARIO, LOJA_USUARIO, CLIENTE, PRODUTO, PEDIDO, ITEM_PEDIDO
```

## 🆘 Problemas Comuns

### Erro: "Connection refused"
```bash
# Verifique se MySQL está rodando
sudo systemctl start mysql  # Linux
net start MySQL80           # Windows
```

### Erro: "Access denied"
- Verifique usuário e senha no arquivo `.env`
- Confirme que o usuário tem permissões no banco

### Erro: "Class not found"
```bash
# Regenerar autoload do Composer
composer dump-autoload
```

### Erro: "Permission denied" em uploads
```bash
# Linux/Mac
chmod -R 777 public/uploads

# Ou dar ownership ao usuário do servidor web
chown -R www-data:www-data public/uploads
```

### Porta 8080 já em uso
Edite `docker-compose.yml` e mude a porta:
```yaml
ports:
  - "8081:80"  # Mude para 8081 ou outra porta livre
```

## 📚 Documentação Adicional

- **README.md** - Documentação completa do projeto
- **database/DATABASE_SETUP.md** - Configuração detalhada do banco
- **.env.example** - Template de configuração

## 🎯 Próximos Passos

Após a instalação:

1. ✅ Registre sua loja e usuário
2. ✅ Cadastre alguns clientes
3. ✅ Adicione produtos ao catálogo
4. ✅ Crie seu primeiro pedido
5. ✅ Explore as funcionalidades!

## 🤝 Contribuindo

Para contribuir com o projeto:

1. Fork o repositório
2. Crie uma branch: `git checkout -b feature/nova-funcionalidade`
3. Commit suas mudanças: `git commit -m 'Adiciona nova funcionalidade'`
4. Push para a branch: `git push origin feature/nova-funcionalidade`
5. Abra um Pull Request

## 📞 Suporte

Se encontrar problemas:

1. Verifique a documentação em `README.md`
2. Consulte `database/DATABASE_SETUP.md` para problemas de banco
3. Veja os logs: `docker-compose logs -f`
4. Abra uma issue no GitHub

---

