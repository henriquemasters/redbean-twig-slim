<?php

namespace App\Controller;

use Slim\Container;

/**
 * Classe base dos controllers da aplicacao.
 *
 * O Slim 3 resolve controllers pelo container de injecao de dependencias. Esta
 * classe centraliza os servicos compartilhados para que os controllers filhos
 * foquem na orquestracao da requisicao, e nao em detalhes de lookup.
 */
class BaseController {

    /** @var \Slim\Views\Twig Renderizador Twig registrado em app/dependencies.php. */
    protected $view;

    /** @var \Monolog\Logger Logger da aplicacao. */
    protected $logger;

    /** @var \Slim\Flash\Messages Armazenamento de mensagens flash. */
    protected $flash;

    /** @var array<int,array{method:string,pattern:string}> Rotas expostas para a tela de ACL. */
    protected $allroutes;

    /** @var string Caminho absoluto usado pelas rotinas de upload. */
    protected $upload_dir;

    /** @var \Slim\Router Roteador usado para gerar URLs por nome de rota. */
    protected $router;

    /** @var \App\Service\AuthSessionService Servico de sessao autenticada e ACL. */
    protected $authSession;

    /** @var \App\Service\CsrfService Servico de protecao CSRF. */
    protected $csrf;

    /**
     * Recebe o container do Slim e armazena servicos compartilhados.
     *
     * @param Container $c Container Slim configurado.
     */
    public function __construct(Container $c) {
        $this->view = $c->get('view');
        $this->logger = $c->get('logger');
        $this->flash = $c->get('flash');
        $this->allroutes = $c->get('allroutes');
        $this->upload_dir = $c->get('upload_dir');
        $this->router = $c->get('router');
        $this->authSession = $c->get('authSession');
        $this->csrf = $c->get('csrf');
    }

}
