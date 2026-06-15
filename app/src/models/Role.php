<?php

namespace App\Model;

use RedBeanPHP\R;

/**
 * Helpers de persistencia de grupos usados pelas telas de ACL do admin.
 */
class Role extends R {

    /** @return array|null Beans de grupo indexados pelo RedBeanPHP. */
    public static function all(): ?array {
        return R::find('role');
    }

    /**
     * @param int|null $id ID do grupo.
     * @return \RedBeanPHP\OODBBean|null Bean do grupo ou null.
     */
    public static function getOne(int $id = null): ?\RedBeanPHP\OODBBean {
        return R::findOne('role', 'id = ?', [$id]);
    }

    /**
     * Persiste dados do grupo e suas permissoes relacionadas quando enviadas pelo formulario.
     *
     * @param array $data Dados do formulario de grupo.
     * @return int|null ID do bean armazenado.
     */
    public static function save(array $data): ?int {
        return R::store(R::dispense($data));
    }

}
