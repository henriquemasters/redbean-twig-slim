# Instalação local

Este guia prepara o projeto para uso local em WAMP, Laragon, Apache ou PHP built-in server.

## Requisitos

- PHP compatível com o `composer.json`
- Composer
- MySQL ou MariaDB
- Extensões PHP exigidas pelas dependências instaladas

## 1. Instale as dependências

```sh
composer install
```

## 2. Configure o ambiente

Copie o arquivo de exemplo:

```sh
copy .env.example .env
```

Ajuste as variáveis conforme seu ambiente local:

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=myapp
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

## 3. Crie o banco de dados

Crie o banco `myapp` no MySQL/MariaDB.

```sql
CREATE DATABASE myapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 4. Importe o seed inicial

```sh
mysql -u root -p myapp < redbean-twig-slim.sql
```

Se seu usuário local não usa senha, remova o `-p`:

```sh
mysql -u root myapp < redbean-twig-slim.sql
```

## 5. Configure diretórios de escrita

Garanta permissão de escrita para:

- `uploads/`
- `log/`
- `cache/`, caso o cache Twig seja habilitado

## 6. Acesse o projeto

Abra a URL local do projeto no navegador e acesse `/login`.

Credenciais iniciais:

- Login: `admin@admin`
- Senha: `123`

## Observação de segurança

O seed já usa `password_hash()` para a senha inicial. O model de usuário ainda aceita temporariamente senhas antigas em MD5 e regrava o hash seguro no primeiro login bem-sucedido.
