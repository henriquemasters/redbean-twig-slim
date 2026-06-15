# RedBean Twig Slim

Microframework PHP autoral para criar MVPs, POCs e pequenas aplicações administrativas com rapidez, mantendo uma estrutura MVC clara e fácil de explicar em portfólio técnico.

A proposta do projeto e simples: entregar uma base funcional com roteamento, templates, persistência, autenticação, painel administrativo, upload de perfil e ACL por rota sem exigir a complexidade de um framework full-stack.

## Por que este projeto existe

MVPs e POCs normalmente precisam responder rápido a três perguntas:

- A ideia funciona para o usuário?
- O fluxo administrativo resolve a operação minima?
- A base técnica permite evoluir sem virar um prototipo descartável?

Este projeto foi criado para esse tipo de cenário. Ele combina bibliotecas maduras do ecossistema PHP em uma estrutura pequena, direta e customizável.

## O que ele demonstra

- **Arquitetura MVC objetiva:** controllers orquestram request/response, models encapsulam RedBeanPHP e Twig cuida da apresentação.
- **Roteamento Slim 3:** rotas declarativas em `app/routes.php`, grupos protegidos e middleware por contexto.
- **Templates Twig:** herança de layout, helpers, blocos de estilos/scripts e paginas publicas customizáveis.
- **Persistência com RedBeanPHP:** CRUD rápido para usuários, perfis, grupos e permissões.
- **Painel administrativo:** login, dashboard, cadastro de usuários, grupos, perfil e upload de foto.
- **ACL por rota:** permissões persistidas no banco e avaliadas no middleware antes de acessar `/admin/*`.
- **Base para portfólio:** landing pública preparada para apresentar o projeto como case técnico.

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
  dependencies.php        # Container Slim: Twig, logger, handlers, controllers
  middleware.php          # Middleware de ACL para área administrativa
  database.php            # Bootstrap da conexão RedBeanPHP
  src/
    controllers/          # Actions PSR-7 da aplicação
    models/               # Helpers de persistência com RedBeanPHP
  views/                  # Templates Twig públicos e administrativos
assets/
  css/site.css            # Identidade visual da landing pública
  admin/                  # Tema e plugins da área administrativa
redbean-twig-slim.sql     # Estrutura/dados iniciais do banco
index.php                 # Front controller Slim
```

## Fluxo técnico

1. `index.php` carrega Composer, inicia sessão, configura Slim e registra dependências, middleware e rotas.
2. `app/routes.php` conecta URLs a actions de controllers.
3. Controllers recebem `Request`, `Response` e argumentos da rota.
4. Models usam RedBeanPHP para consultar e persistir dados.
5. Twig renderiza a resposta HTML.
6. Rotas `/admin/*` passam por autenticação de sessão e ACL por rota.

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

Crie o banco e importe o SQL:

```sh
mysql -u root -p myapp < redbean-twig-slim.sql
```

Ajuste a conexão se necessário em `app/database.php`:

```php
R::setup('mysql:host=localhost; dbname=myapp', 'root', '123');
```

Abra o projeto pelo servidor web apontando para a raiz do repositório.

## Paginas de demonstração

- `/` apresenta a landing do case.
- `/page-1` demonstra a ligação entre rota, controller e Twig.
- `/page-2` explica a camada de models e persistência.
- `/page-3` apresenta a area administrativa e ACL.
- `/login` acessa o fluxo autenticado.

## Acesso inicial ao admin

Depois de importar o arquivo `redbean-twig-slim.sql`, acesse `/login` com:

- Login: `admin@admin`
- Senha: `123`

O formulário aplica MD5 no navegador antes de enviar a senha, por compatibilidade com o seed legado do banco. Antes de usar este projeto em produção, substitua esse fluxo por hash seguro no servidor, como `password_hash()` e `password_verify()`.

## Pontos de extensao

- Criar novos controllers em `app/src/controllers`.
- Criar novos models em `app/src/models`.
- Registrar rotas em `app/routes.php`.
- Adicionar templates Twig em `app/views`.
- Configurar novas permissões pelo painel administrativo.
- Customizar a landing em `app/views/pages` e `assets/css/site.css`.

## Observações de segurança

Este projeto nasceu como base para MVPs e POCs. Antes de usar em produção, revise obrigatoriamente:

- Hash de senhas e política de autenticação.
- Variáveis de ambiente para credenciais de banco.
- `displayErrorDetails` em produção.
- Validação server-side de formulários.
- CSRF nos formulários administrativos.
- Permissões de escrita no diretório `uploads`.

## Como apresentar este case

Este repositório demonstra capacidade de integrar bibliotecas PHP, estruturar uma aplicação MVC, criar uma area administrativa funcional, aplicar controle de acesso por rota e entregar uma interface publica com narrativa de produto.

Ele e especialmente adequado para mostrar experiência em:

- Desenvolvimento PHP pragmática.
- Organização de projetos pequenos e medios.
- Integracão de dependências via Composer.
- Backoffice para validação de negócios.
- Evolução de legado e documentação de código existente.

## Licença e autoria

Este projeto e distribuído sob a licença GNU General Public License v3 or later.

Você pode usar, estudar, modificar e redistribuir este tema, inclusive em forks, desde que mantenha os avisos de copyright, a licença original e a atribuição ao autor original.

Autor original: Henrique Mariano dos Santos Silva.

Este software e fornecido sem garantia de funcionamento, suporte ou adequação a qualquer finalidade específica. Veja `LICENSE` para os termos completos.
