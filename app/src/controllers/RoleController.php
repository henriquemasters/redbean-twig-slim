<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Role;
use App\Model\Permission;

final class RoleController extends BaseController {

    /**
     * Lista os grupos gerenciados no painel administrativo.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/roles.twig', [
            'title' => 'Grupos',
            'user_auth' => $_SESSION['user_auth'],
            'roles' => Role::all()
        ]);

        return $response;
    }

    /**
     * Exibe o formulario de grupo ou salva o grupo com suas permissoes por rota.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $roleId = $args['id'] ?? null;
            $this->view->render($response, 'admin/ui/modals/roles/create.twig', [
                'routeGroups' => $this->listRoutes($roleId),
                'role' => Role::getOne($roleId)
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $roleId = (int) ($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return $response->withRedirect($this->router->pathFor('roleList'));
        }

        // As permissoes sao reconstruidas a partir da matriz enviada pelo formulario
        // para evitar regras antigas apos a edicao de um grupo.
        if ($roleId > 0) {
            Permission::hunt('permission', 'role_id = ?', [$roleId]);
        }
        Role::save($data);

        return $response->withRedirect($this->router->pathFor('roleList'));
    }

    /**
     * Exibe o modal de confirmacao ou remove um grupo.
     */
    public function delete(Request $request, Response $response, array $args): ?Response {
        if (!$request->isDelete()) {
            $this->view->render($response, 'admin/ui/modals/roles/delete.twig', [
                'data' => Role::findOne('role', 'id = ?', [$args['id']]),
            ]);

            return $response;
        }

        $data = $request->getParsedBody();
        $id = (int) ($data['id'] ?? 0);

        if ($id > 0) {
            Role::hunt('role', 'id = ?', [$id]);
        }

        return $response->withRedirect($this->router->pathFor('roleList'));
    }

    /**
     * Monta a matriz de rotas administrativas usada no formulario de permissoes.
     *
     * @param int|null $role_id Grupo em edicao, ou null para um novo grupo.
     * @return array<string,array{label:string,routes:array<int,array{method:string,pattern:string,checked:mixed}>>>
     */
    private function listRoutes(int $role_id = null): array {
        $return = [];

        foreach ($this->allroutes as $value) {
            if (strpos($value['pattern'], '/admin/') !== false) {
                $groupKey = $this->routeGroupKey($value['pattern']);
                if (!isset($return[$groupKey])) {
                    $return[$groupKey] = [
                        'label' => $this->routeGroupLabel($groupKey),
                        'routes' => []
                    ];
                }

                $return[$groupKey]['routes'][] = [
                    'method' => $value['method'],
                    'pattern' => $value['pattern'],
                    'checked' => Permission::getCell('SELECT status FROM permission WHERE role_id = ? AND method = ? AND pattern = ?', [$role_id, $value['method'], $value['pattern']])
                ];
            }
        }

        return $return;
    }

    /**
     * Identifica o modulo administrativo ao qual uma rota pertence.
     */
    private function routeGroupKey(string $pattern): string {
        if (strpos($pattern, '/admin/users/roles') === 0) {
            return 'roles';
        }

        if (strpos($pattern, '/admin/users') === 0) {
            return 'users';
        }

        if (strpos($pattern, '/admin/profile') === 0) {
            return 'profile';
        }

        if (strpos($pattern, '/admin/projects') === 0) {
            return 'projects';
        }

        if (strpos($pattern, '/admin/clients') === 0) {
            return 'clients';
        }

        if (strpos($pattern, '/admin/reports') === 0) {
            return 'reports';
        }

        if (strpos($pattern, '/admin/home') === 0) {
            return 'dashboard';
        }

        return 'other';
    }

    /**
     * Rotulos amigaveis para facilitar a leitura da tela de ACL.
     */
    private function routeGroupLabel(string $groupKey): string {
        $labels = [
            'dashboard' => 'Dashboard',
            'users' => 'Usuários',
            'roles' => 'Grupos e permissões',
            'profile' => 'Perfil',
            'projects' => 'Projetos',
            'clients' => 'Clientes',
            'reports' => 'Relatórios',
            'other' => 'Outras rotas',
        ];

        return $labels[$groupKey] ?? $groupKey;
    }

}
