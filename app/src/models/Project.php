<?php

namespace App\Model;

use RedBeanPHP\R;

/**
 * CRUD de projetos usado como case simples para demonstrar a extensao do admin.
 */
class Project extends R {

    /** @return array|null Projetos ordenados pelos registros mais recentes. */
    public static function all(): ?array {
        try {
            return R::find('project', ' ORDER BY id DESC ');
        } catch (\Throwable $exception) {
            return [];
        }
    }

    /**
     * @param int|null $id ID do projeto.
     * @return \RedBeanPHP\OODBBean|null Bean do projeto ou null.
     */
    public static function getById(int $id = null): ?\RedBeanPHP\OODBBean {
        try {
            return R::findOne('project', 'id = ?', [$id]);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * Persiste os dados do projeto enviados pelo formulario administrativo.
     *
     * @param array $data Dados do formulario de projeto.
     * @return int|null ID do bean armazenado.
     */
    public static function save(array $data): ?int {
        unset($data['_csrf'], $data['_METHOD']);

        if (empty($data['createdat'])) {
            $data['createdat'] = date('Y-m-d H:i:s');
        }

        if (!empty($data['deliverydate'])) {
            $data['deliverydate'] = date('Y-m-d', strtotime($data['deliverydate']));
        }

        return R::store(R::dispense($data));
    }

}
