<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\User;
use App\Model\Role;
use App\Model\Permission;

final class AuthController extends BaseController {

    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function login(Request $request, Response $response, array $args): ?Response {

        if (isset($_SESSION['user_auth']))
            $response = $response->withRedirect('./admin/home');

        $this->view->render($response, 'login.twig');

        return $response;
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function auth(Request $request, Response $response, array $args): ?Response {

        $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getBasePath();
        $data = $request->getParsedBody();

        $user = User::getOne($data['login'], $data['pass']);
        if ($user) {
            User::updateLastLogin($user->id);
            $filePath = $baseUrl . $user->photo;

            $_SESSION['user_auth'] = [
                'id' => $user->id,
                'name' => $user->name,
                'login' => $user->login,
                'photo' => ((!is_null($user->photo) && getimagesize($filePath)) ? $filePath : null),
                'role' => ['id' => $user->role_id, 'name' => $user->role->name]
            ];
            //
            $_SESSION['config'] = [
                'resources' => Permission::getCol('select pattern from permission where role_id = 1'),
                'roles' => Role::getCol('SELECT name FROM role'),
            ];
            foreach (Permission::all() as $permission) {
                $_SESSION['config']['assignments'][$permission->status][$permission->role->name][] = $permission->pattern;
            }
            //
            $response = $response->withRedirect('./admin/home');
        } else {
            $response = $response->withRedirect('./login');
        }

        return $response;
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function logout(Request $request, Response $response, array $args): ?Response {

        if (isset($_SESSION['user_auth'])) {
            unset($_SESSION['user_auth']);
        }

        if (isset($_SESSION['config'])) {
            unset($_SESSION['config']);
        }

        return $response->withRedirect('./login');
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function checkMail(Request $request, Response $response, array $args): ?Response {
        $data = $request->getParsedBody();
        $user = User::getByLogin($data['login']);

        return $response->withJson(['id' => $user->id]);
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function forgotPassword(Request $request, Response $response, array $args): ?Response {
        if (isset($_SESSION['user_auth']))
            $response = $response->withRedirect('./admin/home');


        if (!$request->isPost()) {
            $this->view->render($response, 'forgot-password.twig');
        } else {
            $data = $request->getParsedBody();
            unset($data['confirmpassword']);
            if (User::save($data))
                $response = $response->withRedirect('./login');
        }

        return $response;
    }

}
