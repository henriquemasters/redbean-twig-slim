<?php

// Configuracao do DIC (Dependency Injection Container).

require_once __DIR__ . '/src/services/AuthSessionService.php';
require_once __DIR__ . '/src/services/CsrfService.php';

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Provedores de servico
// -----------------------------------------------------------------------------
// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new \Twig\Extra\String\StringExtension());
    $view->addExtension(new Twig\Extension\DebugExtension());

    // Helper de ACL consumido pelos templates admin para ocultar acoes indisponiveis.
    // O middleware continua sendo a fonte da verdade; este helper melhora apenas a UX.
    $view->getEnvironment()
            ->addFunction(new Twig\TwigFunction('isAllow', function ($url) {
                                $roleName = $_SESSION['user_auth']['role']['name'] ?? null;
                                $allows = $_SESSION['config']['assignments']['allow'][$roleName] ?? [];
                                return in_array($url, $allows);
                            }));
    $view->getEnvironment()
            ->addFunction(new Twig\TwigFunction('csrf_field', function () use ($c) {
                                return $c->get('csrf')->field();
                            }, ['is_safe' => ['html']]));

    $view->offsetSet('constants', [
        'APP_NAME' => 'RedBean Twig Slim',
        'PHP_VERSION' => PHP_VERSION,
        'C_REDBEANPHP_VERSION' => RedBeanPHP\R::C_REDBEANPHP_VERSION,
    ]);

    $view->offsetSet('session', $_SESSION);

    return $view;
};

// -----------------------------------------------------------------------------
// Mensagens flash
// -----------------------------------------------------------------------------
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages;
};

$container['authSession'] = function ($c) {
    return new \App\Service\AuthSessionService();
};

$container['csrf'] = function ($c) {
    return new \App\Service\CsrfService();
};

// -----------------------------------------------------------------------------
// Tratadores de erro
// -----------------------------------------------------------------------------
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $error_view = (isset($_SESSION['user_auth'])) ? 'admin/errors/404.twig' : 'errors/404.twig';

        $c->view->render($response, $error_view, [
            'title' => '404 Nao encontrada',
            'user_auth' => $_SESSION['user_auth'] ?? null
        ]);

        return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html');
    };
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $error_view = (isset($_SESSION['user_auth'])) ? 'admin/errors/500.twig' : 'errors/500.twig';

        $c->view->render($response, $error_view, [
            'title' => '500 Erro Fatal',
            'user_auth' => $_SESSION['user_auth'] ?? null,
            'error_detail' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => ltrim((string) $exception->getTraceAsString(), ' '),
            ]
        ]);

        return $response->withStatus(500)
                ->withHeader('Content-Type', 'text/html');
    };
};

// -----------------------------------------------------------------------------
// Lista todas as rotas criadas em app/routes.php para as telas de ACL.
// -----------------------------------------------------------------------------
$container['allroutes'] = function ($c) {
    $return = [];

    foreach ($c->get('router')->getRoutes() as $route) {
        $return[] = ['method' => $route->getMethods()[0], 'pattern' => $route->getPattern()];
    }

    return $return;
};

// -----------------------------------------------------------------------------
// Diretorio para uploads de usuarios
// -----------------------------------------------------------------------------
$container['upload_dir'] = __DIR__ . '/../uploads';

// -----------------------------------------------------------------------------
// Fabricas de servico
// -----------------------------------------------------------------------------
// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
    return $logger;
};

// -----------------------------------------------------------------------------
// Fabricas de controllers
// -----------------------------------------------------------------------------
$container['App\Controller\SiteController'] = function ($c) {
    return new App\Controller\SiteController($c);
};

$container['App\Controller\AuthController'] = function ($c) {
    return new App\Controller\AuthController($c);
};

$container['App\Controller\RoleController'] = function ($c) {
    return new App\Controller\RoleController($c);
};

$container['App\Controller\UserController'] = function ($c) {
    return new App\Controller\UserController($c);
};

$container['App\Controller\ProfileController'] = function ($c) {
    return new App\Controller\ProfileController($c);
};

$container['App\Controller\DashController'] = function ($c) {
    return new App\Controller\DashController($c);
};

$container['App\Controller\ProjectController'] = function ($c) {
    return new App\Controller\ProjectController($c);
};
