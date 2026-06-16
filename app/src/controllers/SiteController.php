<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class SiteController extends BaseController {

    /**
     * Pagina publica principal usada como vitrine do projeto.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
     */
    public function home(Request $request, Response $response, array $args): ?Response {
        return $this->renderPublicPage($response, 'pages/home.twig', 'Microframework PHP para MVPs e POCs', 'Slim 3, Twig, RedBeanPHP, ACL e painel administrativo em uma base enxuta.');
    }

    /**
     * Demonstra o fluxo de rota publica e renderizacao com Twig.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
     */
    public function pageOne(Request $request, Response $response, array $args): ?Response {
        return $this->renderPublicPage($response, 'pages/page-1.twig', 'Roteamento objetivo', 'Rotas declarativas ligam URLs a controllers PSR-7 sem boilerplate.');
    }

    /**
     * Demonstra acesso a models com RedBeanPHP e o fluxo de dados do admin.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
     */
    public function pageTwo(Request $request, Response $response, array $args): ?Response {
        return $this->renderPublicPage($response, 'pages/page-2.twig', 'Persistencia simples', 'Models encapsulam RedBeanPHP para acelerar CRUDs pequenos.');
    }

    /**
     * Demonstra os recursos de autenticacao e ACL.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
     */
    public function pageThree(Request $request, Response $response, array $args): ?Response {
        return $this->renderPublicPage($response, 'pages/page-3.twig', 'Admin com ACL', 'Autenticacao, perfis e permissoes por rota ja estao integrados.');
    }

    /**
     * Renderiza paginas publicas e consome uma mensagem toast pendente, se existir.
     *
     * Manter esse comportamento em um unico ponto evita notices quando a pagina
     * publica e aberta sem mensagem de sessao previa e deixa as actions pequenas.
     *
     * @param Response $response Resposta PSR-7 atual.
     * @param string $template Caminho do template Twig relativo a app/views.
     * @param string|null $pageTitle Titulo exibido pelo template.
     * @param string|null $description Descricao curta para apresentacao/SEO.
     * @return Response|null Resposta renderizada.
     */
    private function renderPublicPage(Response $response, string $template, ?string $pageTitle, ?string $description): ?Response {
        $toastr = $_SESSION['toastr'] ?? null;

        $this->view->render($response, $template, [
            'pagetitle' => $pageTitle,
            'description' => $description,
            'toastr' => $toastr,
        ]);

        unset($_SESSION['toastr']);

        return $response;
    }

}
