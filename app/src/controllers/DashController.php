<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class DashController extends BaseController {

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function index(Request $request, Response $response, array $args): ?Response {

        $this->view->render($response, 'admin/pages/dashboard.twig', [
            'title' => 'Início',
            'user_auth' => $_SESSION['user_auth'],
        ]);
        //

        return $response;
    }

}
