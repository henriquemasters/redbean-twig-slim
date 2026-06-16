# RedBean Twig Slim

Microframework PHP autoral para criar MVPs, POCs e pequenas aplicações administrativas com rapidez, mantendo uma estrutura MVC clara e fácil de explicar em portfólio técnico.

A proposta do projeto é simples: entregar uma base funcional com roteamento, templates, persistência, autenticação, painel administrativo, upload de perfil, CSRF, ACL por rota e CRUDs de exemplo sem exigir a complexidade de um framework full-stack.

## Por que este projeto existe

MVPs e POCs normalmente precisam responder rápido a três perguntas:

- A ideia funciona para o usuário?
- O fluxo administrativo resolve a operação mínima?
- A base técnica permite evoluir sem virar um protótipo descartável?

Este projeto foi criado para esse tipo de cenário. Ele combina bibliotecas maduras do ecossistema PHP em uma estrutura pequena, direta e customizável.

## O que ele demonstra

- **Arquitetura MVC objetiva:** controllers orquestram request/response, models encapsulam RedBeanPHP e Twig cuida da apresentação.
- **Roteamento Slim 3:** rotas declarativas em `app/routes.php`, grupos protegidos e middleware por contexto.
- **Templates Twig:** herança de layout, helpers, blocos de estilos/scripts e páginas públicas customizáveis.
- **Persistência com RedBeanPHP:** CRUD rápido para usuários, perfis, grupos, permissões e projetos.
- **Painel administrativo:** login, dashboard, cadastro de usuários, grupos, perfil, upload de foto e módulos de projetos/clientes.
- **Segurança pragmática:** senha com `password_hash()`, CSRF em formulários e ACL por rota.
- **Base para portfólio:** landing, login, dashboard e CRUD de case preparados para apresentar o projeto como vitrine técnica.

## Stack principal

- PHP
- Slim Framework 3
- Twig 3
- RedBeanPHP
- Monolog
- Slim Flash
- PHPMailer
- Intervention Image
- Bootstrap 4 no front administrativo

## Estrutura do projeto

```text
app/
  routes.php              # Mapa de rotas públicas, autenticação e admin
  dependencies.php        # Container Slim: Twig, logger, serviços e controllers
  middleware.php          # CSRF, autenticação e ACL para área administrativa
  database.php            # Bootstrap da conexão RedBeanPHP
  src/
    controllers/          # Actions PSR-7 da aplicação
    models/               # Helpers de persistência com RedBeanPHP
    services/             # Serviços de sessão/autenticação e CSRF
  views/                  # Templates Twig públicos e administrativos
assets/
  css/site.css            # Identidade visual da landing pública
  admin/                  # Tema e plugins da área administrativa
  docs/screenshots/       # Imagens usadas no README
scripts/
  smoke-test.php          # Validação rápida de arquivos, rotas e ambiente
redbean-twig-slim.sql     # Estrutura/dados iniciais do banco
index.php                 # Front controller Slim
```

## Fluxo técnico

1. `index.php` carrega Composer, inicia sessão, configura Slim e registra dependências, middleware e rotas.
2. `app/routes.php` conecta URLs a actions de controllers.
3. Controllers recebem `Request`, `Response` e argumentos da rota.
4. Services concentram regras transversais, como sessão autenticada, ACL e CSRF.
5. Models usam RedBeanPHP para consultar e persistir dados.
6. Twig renderiza a resposta HTML.
7. Rotas `/admin/*` passam por autenticação de sessão e ACL por rota.

## Instalação local

Requisitos:

- PHP compatível com as dependências do `composer.json`
- Composer
- MySQL ou MariaDB
- Servidor local como Apache/WAMP, Laragon ou PHP built-in server

Clone o repositório:

```sh
git clone https://github.com/henriquemasters/redbean-twig-slim.git
cd redbean-twig-slim
```

Instale as dependências:

```sh
composer install
```

Copie o arquivo de ambiente:

```sh
copy .env.example .env
```

Crie o banco e importe o SQL:

```sh
mysql -u root -p myapp < redbean-twig-slim.sql
```

Ajuste as credenciais em `.env` quando necessário. Para o passo a passo completo, consulte `INSTALL.md`.

## Páginas de demonstração

- `/` apresenta a landing do case.
- `/page-1` demonstra a ligação entre rota, controller e Twig.
- `/page-2` explica a camada de models e persistência.
- `/page-3` apresenta a área administrativa e ACL.
- `/login` acessa o fluxo autenticado.

## Área administrativa

Depois de importar o arquivo `redbean-twig-slim.sql`, acesse `/login` com:

- Login: `admin@admin`
- Senha: `123`

O seed inicial usa `password_hash()` para armazenar a senha. Senhas antigas em MD5 ainda são aceitas temporariamente e migradas automaticamente para hash seguro no primeiro login bem-sucedido.

### Módulos de case: Projetos e Clientes

Os CRUDs de `Projetos` e `Clientes` demonstram como estender o microframework com módulos administrativos completos. O resource `Relatórios` demonstra uma rota protegida por ACL sem formulário de escrita, útil para telas de consulta, indicadores e painéis executivos.

- Controller em `app/src/controllers/ProjectController.php`.
- Model em `app/src/models/Project.php`.
- Controller em `app/src/controllers/ClientController.php`.
- Model em `app/src/models/Client.php`.
- Views Twig em `app/views/admin/pages/projects.twig` e `app/views/admin/ui/modals/projects`.
- Views Twig em `app/views/admin/pages/clients.twig` e `app/views/admin/ui/modals/clients`.
- Rotas protegidas em `/admin/projects/*`.
- Rotas protegidas em `/admin/clients/*`.
- Resource protegido em `/admin/reports/cases`.
- Permissões ACL no seed SQL.
- Formulários com CSRF e validação server-side mínima.

## Smoke tests

Execute a validação básica:

```sh
composer smoke
```

Se o ambiente local não resolver o PHP corretamente pelo Composer, execute diretamente:

```sh
php scripts/smoke-test.php
```

Esse teste verifica arquivos críticos, rotas principais e conteúdo do seed SQL. Para validar banco e endpoints HTTP reais:

```sh
set SMOKE_DB=1
set SMOKE_BASE_URL=http://localhost/redbean-twig-slim
composer smoke
```

No PowerShell:

```powershell
$env:SMOKE_DB='1'
$env:SMOKE_BASE_URL='http://localhost/redbean-twig-slim'
composer smoke
```

## Pontos de extensão

- Criar novos controllers em `app/src/controllers`.
- Criar novos models em `app/src/models`.
- Registrar rotas em `app/routes.php`.
- Adicionar templates Twig em `app/views`.
- Configurar novas permissões pelo painel administrativo.
- Customizar a landing em `app/views/pages` e `assets/css/site.css`.

## Observações de segurança

Este projeto nasceu como base para MVPs e POCs. Antes de usar em produção, revise obrigatoriamente:

- Política de autenticação, recuperação de senha e rotação de credenciais.
- Variáveis de ambiente para credenciais de banco.
- `displayErrorDetails` em produção.
- Validação server-side de formulários.
- CSRF nos formulários administrativos.
- Permissões de escrita no diretório `uploads`.

## Como apresentar este case

Este repositório demonstra capacidade de integrar bibliotecas PHP, estruturar uma aplicação MVC, criar uma área administrativa funcional, aplicar controle de acesso por rota e entregar uma interface pública com narrativa de produto.

Ele é especialmente adequado para mostrar experiência em:

- Desenvolvimento PHP pragmático.
- Organização de projetos pequenos e médios.
- Integração de dependências via Composer.
- Backoffice para validação de negócios.
- Evolução de legado e documentação de código existente.

## Licença e autoria

Este projeto e distribuído sob a licença GNU General Public License v3 or later.

Você pode usar, estudar, modificar e redistribuir este tema, inclusive em forks, desde que mantenha os avisos de copyright, a licença original e a atribuição ao autor original.

Autor original: Henrique Mariano dos Santos Silva.

Este software e fornecido sem garantia de funcionamento, suporte ou adequação a qualquer finalidade específica. Veja LICENSE para os termos completos.
