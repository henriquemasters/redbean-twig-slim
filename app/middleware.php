<?php

$aclMiddleware = function ($request, $response, $next) use ($app) {
    if (isset($_SESSION['user_auth'])) {

        /* Beg - Atualizando Sessão */
        $_SESSION['user_auth']['role']['name'] = App\Model\Role::getCell('SELECT name FROM role WHERE id = ?', [$_SESSION['user_auth']['role']['id']]);

        unset($_SESSION['config']['roles']);
        $_SESSION['config']['roles'] = App\Model\Role::getCol('SELECT name FROM role');

        unset($_SESSION['config']['assignments']);
        foreach (\App\Model\Permission::all() as $permission) {
            $_SESSION['config']['assignments'][$permission->status][$permission->role->name][] = $permission->pattern;
        }
        /* End - Atualizando Sessão */


        $acl = new \Geggleto\Acl\AclRepository([$_SESSION['user_auth']['role']['name']], $_SESSION['config']);
        $allowed = false;
        //  $route = '/' . ltrim($request->getUri()->getPath(), '/');
        $route = $request->getAttribute('route')->getPattern();

        try {
            $allowed = $acl->isAllowed($acl->getRole()[0], $route);
            //
        } catch (\Zend\Permissions\Acl\Assertion\Exception\InvalidAssertionException $exc) {

            $fn = function (Psr\Http\Message\ServerRequestInterface $requestInterface, Geggleto\Acl\AclRepository $aclRepo) {
                $route = $requestInterface->getAttribute('route');
                if (!empty($route)) {
                    foreach ($aclRepo->getRole() as $role) {
                        if ($aclRepo->isAllowed($role, $route->getPattern())) {
                            return true;
                        }
                    }
                }
                return false;
            };

            $allowed = $fn($request, $acl);
        }

        if ($allowed) {
            return $next($request, $response);
        } else {
            $error_view = (isset($_SESSION['user_auth'])) ? 'admin/errors/401.twig' : 'errors/401.twig';
            $app->getContainer()->view->render($response, $error_view, [
                'title' => '401 Não autorizada',
                'user_auth' => $_SESSION['user_auth']
            ]);
            //
            return $response->withStatus(401)
                            ->withHeader('Content-Type', 'text/html');
        }
    } else {
        return $next($request, $response);
    }
    //
};
