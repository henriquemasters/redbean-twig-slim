<?php

// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------
// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new \Twig\Extra\String\StringExtension());
    $view->addExtension(new Twig\Extension\DebugExtension());

    // Create a custom Filter
    // Use Ex.: {{ 'Henrique Mariano'|rot13 }}
    //  $view->getEnvironment()->addFilter(new \Twig\TwigFilter('rot13', function ($string) {
    //                      return str_rot13($string);
    //                  }));
    // Create a custom Function
    $view->getEnvironment()
            ->addFunction(new Twig\TwigFunction('isAllow', function ($url) {
                                $allows = $_SESSION['config']['assignments']['allow'][$_SESSION['user_auth']['role']['name']];
                                return in_array($url, $allows);
                            }));

    $view->offsetSet('constants', [
        'APP_NAME' => 'MY APP',
        'PHP_VERSION' => PHP_VERSION,
        'C_REDBEANPHP_VERSION' => RedBeanPHP\R::C_REDBEANPHP_VERSION,
    ]);

    $view->offsetSet('session', $_SESSION);

    return $view;
};

// -----------------------------------------------------------------------------
// Flash messages
// -----------------------------------------------------------------------------
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Errors Handles
// -----------------------------------------------------------------------------
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $error_view = (isset($_SESSION['user_auth'])) ? 'admin/errors/404.twig' : 'errors/404.twig';

        $c->view->render($response, $error_view, [
            'title' => '404 Não encontrada',
            'user_auth' => $_SESSION['user_auth']
        ]);
        //
        return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html');
    };
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $error_view = (isset($_SESSION['user_auth'])) ? 'admin/errors/500.twig' : 'errors/500.twig';

        $c->view->render($response, $error_view, [
            'title' => '500 Erro Fatal',
            'user_auth' => $_SESSION['user_auth'],
            'error_detail' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => ltrim((string) $exception->getTraceAsString(), " "),
            ]
        ]);
        //
        return $response->withStatus(500)
                ->withHeader('Content-Type', 'text/html');
    };
};

// -----------------------------------------------------------------------------
// Get all the routes created in the route.php file
// -----------------------------------------------------------------------------
$container['allroutes'] = function ($c) {
    foreach ($c->get('router')->getRoutes() as $route) {
        $return[] = ['method' => $route->getMethods()[0], 'pattern' => $route->getPattern()];
    }
    return $return;
};

// -----------------------------------------------------------------------------
// Directory for User Uploads
// -----------------------------------------------------------------------------
$container['upload_dir'] = __DIR__ . '/../uploads';

// -----------------------------------------------------------------------------
// Service factories
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
// Controller factories
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