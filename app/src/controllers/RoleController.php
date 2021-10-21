<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Role;
use App\Model\Permission;

final class RoleController extends BaseController {

    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $data = $request->getParsedBody();

        $this->view->render($response, 'admin/pages/roles.twig', [
            'title' => 'Grupos',
            'user_auth' => $_SESSION['user_auth'],
            'roles' => Role::all()
        ]);
        //

        return $response;
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/roles/create.twig', [
                'allroutes' => $this->listRoutes($args['id']),
                'role' => Role::getOne($args['id'])
            ]);
        } else {
            // Apaga do Banco de dados as "Permissions" de um "Role" específico do Formulário
            Permission::hunt('permission', 'role_id = ?', [$request->getParsedBody()['id']]);

            // Salva o "Role" e suas "Permissions" atualizadas pelo Formulário
            Role::save($request->getParsedBody());

            $response = $response->withRedirect('../roles/list');
        }
        //

        return $response;
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
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
        //

        return $response;
    }

    /**
     * 
     * @param int $role_id
     * @return array|null
     */
    private function listRoutes(int $role_id = null): ?array {

        foreach ($this->allroutes as $key => $value) {
            if (strpos($value['pattern'], '/admin/') !== FALSE) {
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
