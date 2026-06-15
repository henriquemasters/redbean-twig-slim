# RedBean Twig Slim

Microframework PHP autoral para criar MVPs, POCs e pequenas aplicacoes administrativas com rapidez, mantendo uma estrutura MVC clara e facil de explicar em portfolio tecnico.

A proposta do projeto e simples: entregar uma base funcional com roteamento, templates, persistencia, autenticacao, painel administrativo, upload de perfil e ACL por rota sem exigir a complexidade de um framework full-stack.

## Por que este projeto existe

MVPs e POCs normalmente precisam responder rapido a tres perguntas:

- A ideia funciona para o usuario?
- O fluxo administrativo resolve a operacao minima?
- A base tecnica permite evoluir sem virar um prototipo descartavel?

Este projeto foi criado para esse tipo de cenario. Ele combina bibliotecas maduras do ecossistema PHP em uma estrutura pequena, direta e customizavel.

## O que ele demonstra

- **Arquitetura MVC objetiva:** controllers orquestram request/response, models encapsulam RedBeanPHP e Twig cuida da apresentacao.
- **Roteamento Slim 3:** rotas declarativas em `app/routes.php`, grupos protegidos e middleware por contexto.
- **Templates Twig:** heranca de layout, helpers, blocos de estilos/scripts e paginas publicas customizaveis.
- **Persistencia com RedBeanPHP:** CRUD rapido para usuarios, perfis, grupos e permissoes.
- **Painel administrativo:** login, dashboard, cadastro de usuarios, grupos, perfil e upload de foto.
- **ACL por rota:** permissoes persistidas no banco e avaliadas no middleware antes de acessar `/admin/*`.
- **Base para portfolio:** landing publica preparada para apresentar o projeto como case tecnico.

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
  routes.php              # Mapa de rotas publicas, autenticacao e admin
  dependencies.php        # Container Slim: Twig, logger, handlers, controllers
  middleware.php          # Middleware de ACL para area administrativa
  database.php            # Bootstrap da conexao RedBeanPHP
  src/
    controllers/          # Actions PSR-7 da aplicacao
    models/               # Helpers de persistencia com RedBeanPHP
  views/                  # Templates Twig publicos e administrativos
assets/
  css/site.css            # Identidade visual da landing publica
  admin/                  # Tema e plugins da area administrativa
redbean-twig-slim.sql     # Estrutura/dados iniciais do banco
index.php                 # Front controller Slim
```

## Fluxo tecnico

1. `index.php` carrega Composer, inicia sessao, configura Slim e registra dependencias, middleware e rotas.
2. `app/routes.php` conecta URLs a actions de controllers.
3. Controllers recebem `Request`, `Response` e argumentos da rota.
4. Models usam RedBeanPHP para consultar e persistir dados.
5. Twig renderiza a resposta HTML.
6. Rotas `/admin/*` passam por autenticacao de sessao e ACL por rota.

## Instalacao local

Requisitos:

- PHP compativel com as dependencias do `composer.json`
- Composer
- MySQL ou MariaDB
- Servidor local como Apache/WAMP, Laragon ou PHP built-in server

Clone o repositorio:

```sh
git clone https://github.com/henriquemasters/redbean-twig-slim.git
cd redbean-twig-slim
```

Instale as dependencias:

```sh
composer install
```

Crie o banco e importe o SQL:

```sh
mysql -u root -p myapp < redbean-twig-slim.sql
```

Ajuste a conexao se necessario em `app/database.php`:

```php
R::setup('mysql:host=localhost; dbname=myapp', 'root', '123');
```

Abra o projeto pelo servidor web apontando para a raiz do repositorio.

## Paginas de demonstracao

- `/` apresenta a landing do case.
- `/page-1` demonstra a ligacao entre rota, controller e Twig.
- `/page-2` explica a camada de models e persistencia.
- `/page-3` apresenta a area administrativa e ACL.
- `/login` acessa o fluxo autenticado.

## Pontos de extensao

- Criar novos controllers em `app/src/controllers`.
- Criar novos models em `app/src/models`.
- Registrar rotas em `app/routes.php`.
- Adicionar templates Twig em `app/views`.
- Configurar novas permissoes pelo painel administrativo.
- Customizar a landing em `app/views/pages` e `assets/css/site.css`.

## Observacoes de seguranca

Este projeto nasceu como base para MVPs e POCs. Antes de usar em producao, revise obrigatoriamente:

- Hash de senhas e politica de autenticacao.
- Variaveis de ambiente para credenciais de banco.
- `displayErrorDetails` em producao.
- Validacao server-side de formularios.
- CSRF nos formularios administrativos.
- Permissoes de escrita no diretorio `uploads`.

## Como apresentar este case

Este repositorio demonstra capacidade de integrar bibliotecas PHP, estruturar uma aplicacao MVC, criar uma area administrativa funcional, aplicar controle de acesso por rota e entregar uma interface publica com narrativa de produto.

Ele e especialmente adequado para mostrar experiencia em:

- Desenvolvimento PHP pragmatica.
- Organizacao de projetos pequenos e medios.
- Integracao de dependencias via Composer.
- Backoffice para validacao de negocios.
- Evolucao de legado e documentacao de codigo existente.

## Licenca

MIT
