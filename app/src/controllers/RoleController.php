<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Role;
use App\Model\Permission;

final class RoleController extends BaseController {

    /**
     * Lista os grupos gerenciados no painel administrativo.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/roles.twig', [
            'title' => 'Grupos',
            'user_auth' => $_SESSION['user_auth'],
            'roles' => Role::all()
        ]);

        return $response;
    }

    /**
     * Exibe o formulario de grupo ou salva o grupo com suas permissoes por rota.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada ou redirecionamento.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $roleId = $args['id'] ?? null;
            $this->view->render($response, 'admin/ui/modals/roles/create.twig', [
                'allroutes' => $this->listRoutes($roleId),
                'role' => Role::getOne($roleId)
            ]);
        } else {
            // As permissoes sao reconstruidas a partir da matriz enviada pelo formulario
            // para evitar regras antigas apos a edicao de um grupo.
            Permission::hunt('permission', 'role_id = ?', [$request->getParsedBody()['id']]);
            Role::save($request->getParsedBody());

            $response = $response->withRedirect('../roles/list');
        }

        return $response;
    }

    /**
     * Exibe o modal de confirmacao ou remove um grupo.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada ou redirecionamento.
     */
    public function delete(Request $request, Response $response, array $args): ?Response {
        if (!$request->isDelete()) {
            $this->view->render($response, 'admin/ui/modals/roles/delete.twig', [
                'data' => Role::findOne('role', 'id = ?', [$args['id']]),
            ]);
        } else {
            $data = $request->getParsedBody();
            Role::hunt('role', 'id = ?', [$data['id']]);
            $response = $response->withRedirect('../roles/list');
        }

        return $response;
    }

    /**
     * Monta a matriz de rotas administrativas usada no formulario de permissoes.
     *
     * @param int|null $role_id Grupo em edicao, ou null para um novo grupo.
     * @return array<string,array{method:string,pattern:string,checked:mixed}> Rotas indexadas por METHOD:/pattern.
     */
    private function listRoutes(int $role_id = null): ?array {
        $return = [];

        foreach ($this->allroutes as $value) {
            if (strpos($value['pattern'], '/admin/') !== false) {
                $return[$value['method'] . ':' . $value['pattern']] = [
                    'method' => $value['method'],
                    'pattern' => $value['pattern'],
                    'checked' => Permission::getCell('SELECT status FROM permission WHERE role_id = ? AND method = ? AND pattern = ?', [$role_id, $value['method'], $value['pattern']])
                ];
            }
        }

        return $return;
    }

}
