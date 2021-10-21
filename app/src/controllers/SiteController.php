<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class SiteController extends BaseController {

    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function home(Request $request, Response $response, array $args): ?Response {
        //  $this->logger->info("Home page action dispatched");
        //  $this->flash->addMessage('info', 'Sample flash message');

        $this->view->render($response, 'pages/home.twig', [
            'pagetitle' => null,
            'description' => 'Seja bem vindo!',
            'toastr' => $_SESSION['toastr']
        ]);
        //
        unset($_SESSION['toastr']);

        return $response;
    }
    
    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function PageOne(Request $request, Response $response, array $args): ?Response {

        $this->view->render($response, 'pages/page-1.twig', [
            'pagetitle' => null,
            'description' => null,
            'toastr' => $_SESSION['toastr']
        ]);
        //
        unset($_SESSION['toastr']);

        return $response;
    }
    
    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function PageTwo(Request $request, Response $response, array $args): ?Response {

        $this->view->render($response, 'pages/page-2.twig', [
            'pagetitle' => null,
            'description' => null,
            'toastr' => $_SESSION['toastr']
        ]);
        //
        unset($_SESSION['toastr']);

        return $response;
    }
    
    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function PageThree(Request $request, Response $response, array $args): ?Response {

        $this->view->render($response, 'pages/page-3.twig', [
            'pagetitle' => null,
            'description' => null,
            'toastr' => $_SESSION['toastr']
        ]);
        //
        unset($_SESSION['toastr']);

        return $response;
    }
    
}
