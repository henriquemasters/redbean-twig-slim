<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Role;
use App\Model\User;

final class UserController extends BaseController {

    /**
     * Lista os usuarios cadastrados no painel administrativo.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/users.twig', [
            'title' => 'Lista de Usuários',
            'user_auth' => $_SESSION['user_auth'],
            'users' => User::all()
        ]);

        return $response;
    }

    /**
     * Exibe o formulario de usuario ou persiste os dados enviados.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/users/create.twig', [
                'roles' => Role::all(),
                'user' => User::getById($args['id'] ?? null)
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $id = (int) ($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $login = trim($data['login'] ?? '');
        $roleId = (int) ($data['role_id'] ?? 0);
        $password = (string) ($data['pass'] ?? '');
        $confirmation = (string) ($data['confirmpassword'] ?? '');

        if ($name === '' || $login === '' || $roleId <= 0 || ($id <= 0 && $password === '') || ($password !== '' && $password !== $confirmation)) {
            return $response->withRedirect($this->router->pathFor('userList'));
        }

        if ($id > 0 && $password === '') {
            unset($data['pass'], $data['confirmpassword']);
        }

        User::save($data);

        return $response->withRedirect($this->router->pathFor('userList'));
    }

    /**
     * Exibe o modal de confirmacao ou remove um usuario.
     */
    public function delete(Request $request, Response $response, array $args): ?Response {
        if (!$request->isDelete()) {
            $this->view->render($response, 'admin/ui/modals/users/delete.twig', [
                'data' => User::findOne('user', 'id = ?', [$args['id']]),
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $id = (int) ($data['id'] ?? 0);

        if ($id > 0) {
            User::hunt('user', 'id = ?', [$id]);
        }

        return $response->withRedirect($this->router->pathFor('userList'));
    }

}
