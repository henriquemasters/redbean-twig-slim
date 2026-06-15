<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\User;
use App\Model\Role;
use App\Model\Permission;

final class AuthController extends BaseController {

    /**
     * Exibe a pagina de login ou redireciona usuarios autenticados para o admin.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada ou redirecionamento.
     */
    public function login(Request $request, Response $response, array $args): ?Response {
        if (isset($_SESSION['user_auth'])) {
            return $response->withRedirect('./admin/home');
        }

        $this->view->render($response, 'login.twig');

        return $response;
    }

    /**
     * Autentica o usuario e prepara o dados de sessao usado pela ACL.
     *
     * @param Request $request Requisicao PSR-7 atual com campos login/pass.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta de redirecionamento.
     */
    public function auth(Request $request, Response $response, array $args): ?Response {
        $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getBasePath();
        $data = $request->getParsedBody();

        $user = User::getOne($data['login'], $data['pass']);
        if (!$user) {
            return $response->withRedirect('./login');
        }

        User::updateLastLogin($user->id);
        $photoUrl = $user->photo ? $baseUrl . $user->photo : null;

        $_SESSION['user_auth'] = [
            'id' => $user->id,
            'name' => $user->name,
            'login' => $user->login,
            'photo' => $photoUrl,
            'role' => ['id' => $user->role_id, 'name' => $user->role->name]
        ];

        $_SESSION['config'] = [
            'resources' => Permission::getCol('select pattern from permission where role_id = 1'),
            'roles' => Role::getCol('SELECT name FROM role'),
            'assignments' => [],
        ];

        foreach (Permission::all() as $permission) {
            $_SESSION['config']['assignments'][$permission->status][$permission->role->name][] = $permission->pattern;
        }

        return $response->withRedirect('./admin/home');
    }

    /**
     * Limpa os dados de autenticacao e ACL da sessao.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta de redirecionamento.
     */
    public function logout(Request $request, Response $response, array $args): ?Response {
        unset($_SESSION['user_auth'], $_SESSION['config']);

        return $response->withRedirect('./login');
    }

    /**
     * Verifica se um login existe para o fluxo de recuperacao de senha.
     *
     * @param Request $request Requisicao PSR-7 atual com campo login.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta JSON.
     */
    public function checkMail(Request $request, Response $response, array $args): ?Response {
        $data = $request->getParsedBody();
        $user = User::getByLogin($data['login']);

        return $response->withJson(['id' => $user ? $user->id : null]);
    }

    /**
     * Exibe o formulario de recuperacao ou grava a troca de senha enviada.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada ou redirecionamento.
     */
    public function forgotPassword(Request $request, Response $response, array $args): ?Response {
        if (isset($_SESSION['user_auth'])) {
            return $response->withRedirect('./admin/home');
        }

        if (!$request->isPost()) {
            $this->view->render($response, 'forgot-password.twig');
            return $response;
        }

        $data = $request->getParsedBody();
        unset($data['confirmpassword']);

        if (User::save($data)) {
            return $response->withRedirect('./login');
        }

        return $response;
    }

}
