<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

function ok(string $message): void {
    echo "[OK] {$message}" . PHP_EOL;
}

function fail(array &$errors, string $message): void {
    $errors[] = $message;
    echo "[ERRO] {$message}" . PHP_EOL;
}

$requiredFiles = [
    'index.php',
    'app/routes.php',
    'app/dependencies.php',
    'app/middleware.php',
    'app/database.php',
    'app/views/login.twig',
    'app/views/admin/pages/dashboard.twig',
    'app/views/admin/pages/projects.twig',
    'app/views/admin/pages/clients.twig',
    'app/views/admin/pages/reports/cases.twig',
    'app/src/controllers/AuthController.php',
    'app/src/controllers/ProjectController.php',
    'app/src/controllers/ClientController.php',
    'app/src/controllers/ReportController.php',
    'app/src/models/Project.php',
    'app/src/models/Client.php',
    'redbean-twig-slim.sql',
];

foreach ($requiredFiles as $file) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    is_file($path) ? ok("Arquivo encontrado: {$file}") : fail($errors, "Arquivo ausente: {$file}");
}

$routesPath = $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes.php';
$routes = is_file($routesPath) ? file_get_contents($routesPath) : '';

$routeFragments = [
    '/login',
    "group('/admin'",
    "group('/projects'",
    "group('/clients'",
    "group('/reports'",
    "'/list', ProjectController",
    "'/save', ProjectController",
    "'/delete', ProjectController",
    "'/list', ClientController",
    "'/save', ClientController",
    "'/delete', ClientController",
    "'/cases', ReportController",
];

foreach ($routeFragments as $route) {
    strpos($routes, $route) !== false ? ok("Rota mapeada: {$route}") : fail($errors, "Rota nao encontrada: {$route}");
}

$sqlPath = $root . DIRECTORY_SEPARATOR . 'redbean-twig-slim.sql';
$sql = is_file($sqlPath) ? file_get_contents($sqlPath) : '';

foreach (['CREATE TABLE `project`', '/admin/projects/list', 'Portal de validação de MVP', 'CREATE TABLE `client`', '/admin/clients/list', 'Clínica Horizonte', '/admin/reports/cases'] as $needle) {
    strpos($sql, $needle) !== false ? ok("Seed contempla: {$needle}") : fail($errors, "Seed incompleto para: {$needle}");
}

if (getenv('SMOKE_DB') === '1') {
    require $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    require $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'database.php';

    try {
        \RedBeanPHP\R::getCell('SELECT 1');
        ok('Conexao com banco validada via RedBeanPHP');
    } catch (Throwable $exception) {
        fail($errors, 'Falha na conexao com banco: ' . $exception->getMessage());
    }
} else {
    ok('Teste de banco ignorado. Use SMOKE_DB=1 para validar conexao real.');
}

$baseUrl = rtrim((string) getenv('SMOKE_BASE_URL'), '/');
if ($baseUrl !== '') {
    foreach (['/', '/login'] as $path) {
        $headers = @get_headers($baseUrl . $path);
        if ($headers && preg_match('/^HTTP\/\S+\s+(200|302)/', $headers[0])) {
            ok("Endpoint respondeu: {$path} ({$headers[0]})");
        } else {
            fail($errors, "Endpoint nao respondeu como esperado: {$path}");
        }
    }
} else {
    ok('Teste HTTP ignorado. Use SMOKE_BASE_URL=http://localhost/seu-projeto para validar endpoints.');
}

if ($errors !== []) {
    echo PHP_EOL . count($errors) . ' falha(s) encontrada(s).' . PHP_EOL;
    exit(1);
}

echo PHP_EOL . 'Smoke test concluido com sucesso.' . PHP_EOL;
