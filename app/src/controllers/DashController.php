<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RedBeanPHP\R;

final class DashController extends BaseController {

    /**
     * Exibe a pagina inicial da area administrativa com indicadores do projeto.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/dashboard.twig', [
            'title' => 'Início',
            'user_auth' => $_SESSION['user_auth'],
            'metrics' => [
                'users' => $this->countTable('user'),
                'roles' => $this->countTable('role'),
                'permissions' => $this->countTable('permission'),
                'projects' => $this->countTable('project'),
                'php_version' => PHP_VERSION,
                'redbean_version' => R::C_REDBEANPHP_VERSION,
            ],
        ]);

        return $response;
    }

    /**
     * Evita quebra do dashboard quando um modulo novo ainda nao foi importado no banco.
     */
    private function countTable(string $table): int {
        try {
            return R::count($table);
        } catch (\Throwable $exception) {
            return 0;
        }
    }

}
