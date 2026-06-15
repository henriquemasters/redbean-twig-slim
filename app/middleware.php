<?php

$aclMiddleware = function ($request, $response, $next) use ($app) {
    if (isset($_SESSION['user_auth'])) {
        // Reconstroi os dados de ACL a cada requisicao protegida para que
        // alteracoes de grupos/permissoes no painel tenham efeito sem novo login.
        $_SESSION['user_auth']['role']['name'] = App\Model\Role::getCell('SELECT name FROM role WHERE id = ?', [$_SESSION['user_auth']['role']['id']]);

        $_SESSION['config']['roles'] = App\Model\Role::getCol('SELECT name FROM role');
        $_SESSION['config']['assignments'] = [];

        foreach (\App\Model\Permission::all() as $permission) {
            $_SESSION['config']['assignments'][$permission->status][$permission->role->name][] = $permission->pattern;
        }

        $acl = new \Geggleto\Acl\AclRepository([$_SESSION['user_auth']['role']['name']], $_SESSION['config']);
        $allowed = false;
        $route = $request->getAttribute('route');
        $routePattern = $route ? $route->getPattern() : $request->getUri()->getPath();

        try {
            $allowed = $acl->isAllowed($acl->getRole()[0], $routePattern);
        } catch (\Zend\Permissions\Acl\Assertion\Exception\InvalidAssertionException $exc) {
            // Algumas assertions de ACL precisam do objeto completo da rota.
            // Quando a checagem direta por pattern falha, tentamos os papeis disponiveis.
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
        }

        $error_view = (isset($_SESSION['user_auth'])) ? 'admin/errors/401.twig' : 'errors/401.twig';
        $app->getContainer()->view->render($response, $error_view, [
            'title' => '401 Nao autorizada',
            'user_auth' => $_SESSION['user_auth'] ?? null
        ]);

        return $response->withStatus(401)
                        ->withHeader('Content-Type', 'text/html');
    }

    return $next($request, $response);
};
