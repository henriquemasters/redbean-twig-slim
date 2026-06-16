<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RedBeanPHP\R;

final class ReportController extends BaseController {

    /**
     * Recurso de relatorio usado como case de permissao sem CRUD.
     */
    public function cases(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/reports/cases.twig', [
            'title' => 'Relatórios',
            'user_auth' => $_SESSION['user_auth'],
            'summary' => [
                'projects' => $this->countTable('project'),
                'clients' => $this->countTable('client'),
                'users' => $this->countTable('user'),
                'permissions' => $this->countTable('permission'),
            ],
            'recent_projects' => $this->recentRows('project', ['name', 'client', 'status'], 5),
            'recent_clients' => $this->recentRows('client', ['name', 'email', 'status'], 5),
        ]);

        return $response;
    }

    /**
     * Conta registros de forma tolerante quando a tabela ainda nao foi importada.
     */
    private function countTable(string $table): int {
        try {
            return R::count($table);
        } catch (\Throwable $exception) {
            return 0;
        }
    }

    /**
     * Retorna registros recentes com apenas colunas esperadas para o relatorio.
     *
     * @param array<int,string> $columns
     * @return array<int,array<string,mixed>>
     */
    private function recentRows(string $table, array $columns, int $limit): array {
        try {
            $select = implode(', ', array_map(static function ($column) {
                return "`{$column}`";
            }, $columns));

            return R::getAll("SELECT {$select} FROM `{$table}` ORDER BY id DESC LIMIT ?", [$limit]);
        } catch (\Throwable $exception) {
            return [];
        }
    }

}
