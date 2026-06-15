<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Role;
use App\Model\User;

final class UserController extends BaseController {

    /**
     * Lista os usuarios cadastrados no painel administrativo.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/users.twig', [
            'title' => 'Lista de Usuarios',
            'user_auth' => $_SESSION['user_auth'],
            'users' => User::all()
        ]);

        return $response;
    }

    /**
     * Exibe o formulario de usuario ou persiste os dados enviados.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada ou redirecionamento.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/users/create.twig', [
                'roles' => Role::all(),
                'user' => User::getById($args['id'] ?? null)
            ]);
        } else {
            User::save($request->getParsedBody());
            $response = $response->withRedirect('../users/list');
        }

        return $response;
    }

    /**
     * Exibe o modal de confirmacao ou remove um usuario.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada ou redirecionamento.
     */
    public function delete(Request $request, Response $response, array $args): ?Response {
        if (!$request->isDelete()) {
            $this->view->render($response, 'admin/ui/modals/users/delete.twig', [
                'data' => User::findOne('user', 'id = ?', [$args['id']]),
            ]);
        } else {
            $data = $request->getParsedBody();
            User::hunt('user', 'id = ?', [$data['id']]);
            $response = $response->withRedirect('../users/list');
        }

        return $response;
    }

}
