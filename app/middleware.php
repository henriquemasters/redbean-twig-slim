<?php

$csrfMiddleware = function ($request, $response, $next) use ($app) {
    $unsafeMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    if (in_array($request->getMethod(), $unsafeMethods, true) && !$app->getContainer()->csrf->validate($request)) {
        return $response->withStatus(403)->write('Token CSRF invalido. Atualize a pagina e tente novamente.');
    }

    return $next($request, $response);
};

$app->add($csrfMiddleware);

$aclMiddleware = function ($request, $response, $next) use ($app) {
    if (isset($_SESSION['user_auth'])) {
        $app->getContainer()->authSession->refreshAcl();

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
