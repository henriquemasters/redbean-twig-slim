<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Project;

final class ProjectController extends BaseController {

    /**
     * Lista projetos cadastrados no modulo de case do painel administrativo.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/projects.twig', [
            'title' => 'Projetos',
            'user_auth' => $_SESSION['user_auth'],
            'projects' => Project::all()
        ]);

        return $response;
    }

    /**
     * Exibe o formulario de projeto ou persiste os dados enviados.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/projects/create.twig', [
                'project' => Project::getById($args['id'] ?? null),
                'statuses' => $this->statuses()
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $name = trim($data['name'] ?? '');
        $client = trim($data['client'] ?? '');
        $status = trim($data['status'] ?? '');

        if ($name === '' || $client === '' || !in_array($status, array_keys($this->statuses()), true)) {
            return $response->withRedirect($this->router->pathFor('projectList'));
        }

        Project::save($data);

        return $response->withRedirect($this->router->pathFor('projectList'));
    }

    /**
     * Exibe o modal de confirmacao ou remove um projeto.
     */
    public function delete(Request $request, Response $response, array $args): ?Response {
        if (!$request->isDelete()) {
            $this->view->render($response, 'admin/ui/modals/projects/delete.twig', [
                'data' => Project::getById($args['id'] ?? null),
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $id = (int) ($data['id'] ?? 0);

        if ($id > 0) {
            Project::hunt('project', 'id = ?', [$id]);
        }

        return $response->withRedirect($this->router->pathFor('projectList'));
    }

    /**
     * Status intencionais para manter o modulo simples e demonstravel.
     *
     * @return array<string,string>
     */
    private function statuses(): array {
        return [
            'ideia' => 'Ideia',
            'validacao' => 'Validação',
            'mvp' => 'MVP',
            'entregue' => 'Entregue',
        ];
    }

}
