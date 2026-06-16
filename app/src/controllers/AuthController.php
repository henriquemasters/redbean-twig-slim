<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\User;

final class AuthController extends BaseController {

    /**
     * Exibe a pagina de login ou redireciona usuarios autenticados para o admin.
     */
    public function login(Request $request, Response $response, array $args): ?Response {
        if (isset($_SESSION['user_auth'])) {
            return $response->withRedirect($this->router->pathFor('dashBoard'));
        }

        $this->view->render($response, 'login.twig');

        return $response;
    }

    /**
     * Autentica o usuario e delega a montagem de sessao/ACL ao servico dedicado.
     */
    public function auth(Request $request, Response $response, array $args): ?Response {
        $data = $request->getParsedBody();
        $login = trim($data['login'] ?? '');
        $password = (string) ($data['pass'] ?? '');

        if ($login === '' || $password === '') {
            return $response->withRedirect($this->router->pathFor('login'));
        }

        $user = User::getOne($login, $password);
        if (!$user) {
            return $response->withRedirect($this->router->pathFor('login'));
        }

        $this->authSession->login($user, $request);

        return $response->withRedirect($this->router->pathFor('dashBoard'));
    }

    /**
     * Limpa os dados de autenticacao e ACL da sessao.
     */
    public function logout(Request $request, Response $response, array $args): ?Response {
        $this->authSession->logout();

        return $response->withRedirect($this->router->pathFor('login'));
    }

    /**
     * Verifica se um login existe para o fluxo de recuperacao de senha.
     */
    public function checkMail(Request $request, Response $response, array $args): ?Response {
        $data = $request->getParsedBody();
        $login = trim($data['login'] ?? '');
        $user = ($login !== '') ? User::getByLogin($login) : null;

        return $response->withJson(['id' => $user ? $user->id : null]);
    }

    /**
     * Exibe o formulario de recuperacao ou grava a troca de senha enviada.
     */
    public function forgotPassword(Request $request, Response $response, array $args): ?Response {
        if (isset($_SESSION['user_auth'])) {
            return $response->withRedirect($this->router->pathFor('dashBoard'));
        }

        if (!$request->isPost()) {
            $this->view->render($response, 'forgot-password.twig');
            return $response;
        }

        $data = $request->getParsedBody();
        $id = (int) ($data['id'] ?? 0);
        $password = (string) ($data['pass'] ?? '');
        $confirmation = (string) ($data['confirmpassword'] ?? '');

        if ($id <= 0 || $password === '' || $password !== $confirmation) {
            return $response->withRedirect($this->router->pathFor('forgotPassword'));
        }

        if (User::save($data)) {
            return $response->withRedirect($this->router->pathFor('login'));
        }

        return $response;
    }

}
