<?php

use App\Controller\SiteController;
use App\Controller\AuthController;
use App\Controller\RoleController;
use App\Controller\UserController;
use App\Controller\ProfileController;
use App\Controller\DashController;

/**
 * Externals Routes
 */
$app->get('/', SiteController::class . ':home')->setName('homepage');
$app->get('/page-1', SiteController::class . ':PageOne')->setName('page1');
$app->get('/page-2', SiteController::class . ':PageTwo')->setName('page2');
$app->get('/page-3', SiteController::class . ':PageThree')->setName('page3');

/**
 * Authentication Routes
 */
$app->get('/login', AuthController::class . ':login')->setName('login');
$app->post('/auth', AuthController::class . ':auth')->setName('auth');
$app->get('/logout', AuthController::class . ':logout')->setName('logout');
$app->get('/forgot-password', AuthController::class . ':forgotPassword')->setName('forgotPassword');
$app->post('/check-mail', AuthController::class . ':checkMail')->setName('checkMail');
$app->post('/forgot-password', AuthController::class . ':forgotPassword')->setName('forgotPasswordSubmit');

/**
 * Internals Routes (by Authentication)
 */
$app->group('/admin', function () use ($app) {

    $app->get('/home', DashController::class . ':index')->setName('dashBoard');

    //  Routes Usuários & Grupos
    $app->group('/users', function () use ($app) {
        $app->get('/list', UserController::class . ':index')->setName('userList');
        $app->get('/create', UserController::class . ':create')->setName('userCreate');
        $app->post('/save', UserController::class . ':create')->setName('userSave');
        $app->get('/update/{id}', UserController::class . ':create')->setName('userEdit');
        $app->get('/delete/{id}', UserController::class . ':delete')->setName('userConfirmDelete');
        $app->delete('/delete', UserController::class . ':delete')->setName('userDelete');

        // Routes Grupo de Usuários
        $app->group('/roles', function () use ($app) {
            $app->get('/list', RoleController::class . ':index')->setName('roleList');
            $app->get('/create', RoleController::class . ':create')->setName('roleCreate');
            $app->post('/save', RoleController::class . ':create')->setName('roleSave');
            $app->get('/update/{id}', RoleController::class . ':create')->setName('roleEdit');
            $app->get('/delete/{id}', RoleController::class . ':delete')->setName('roleConfirmDelete');
            $app->delete('/delete', RoleController::class . ':delete')->setName('roleDelete');
        });
    });

    //  Routes Perfil de Usuário
    $app->group('/profile', function () use ($app) {
        $app->get('', ProfileController::class . ':index')->setName('profile');
        $app->post('/save', ProfileController::class . ':create')->setName('profileSave');
        $app->get('/change-photo', ProfileController::class . ':changePhoto')->setName('profileChangePhoto');
        $app->post('/save-photo', ProfileController::class . ':changePhoto')->setName('profileSavePhoto');
        $app->post('/save-newpass', ProfileController::class . ':changePass')->setName('profileChangePass');
    });

    /**
     * Middleware para controle de acesso:
     */
})->add($aclMiddleware)->add(function ($request, $response, $next) {

    if (!isset($_SESSION['user_auth'])) {
        return $response->withRedirect('../login');
    }
    return $next($request, $response);
});
