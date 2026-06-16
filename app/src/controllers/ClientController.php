<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Client;

final class ClientController extends BaseController {

    /**
     * Lista clientes cadastrados no segundo modulo de case do painel.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/clients.twig', [
            'title' => 'Clientes',
            'user_auth' => $_SESSION['user_auth'],
            'clients' => Client::all()
        ]);

        return $response;
    }

    /**
     * Exibe o formulario de cliente ou persiste os dados enviados.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/clients/create.twig', [
                'client' => Client::getById($args['id'] ?? null),
                'statuses' => $this->statuses()
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $status = trim($data['status'] ?? '');

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($status, array_keys($this->statuses()), true)) {
            return $response->withRedirect($this->router->pathFor('clientList'));
        }

        Client::save($data);

        return $response->withRedirect($this->router->pathFor('clientList'));
    }

    /**
     * Exibe o modal de confirmacao ou remove um cliente.
     */
    public function delete(Request $request, Response $response, array $args): ?Response {
        if (!$request->isDelete()) {
            $this->view->render($response, 'admin/ui/modals/clients/delete.twig', [
                'data' => Client::getById($args['id'] ?? null),
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $id = (int) ($data['id'] ?? 0);

        if ($id > 0) {
            Client::hunt('client', 'id = ?', [$id]);
        }

        return $response->withRedirect($this->router->pathFor('clientList'));
    }

    /**
     * Status enxutos para indicar maturidade comercial do cliente no case.
     *
     * @return array<string,string>
     */
    private function statuses(): array {
        return [
            'lead' => 'Lead',
            'prospect' => 'Prospect',
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
        ];
    }

}
