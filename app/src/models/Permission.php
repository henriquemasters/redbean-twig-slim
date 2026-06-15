<?php

namespace App\Model;

use RedBeanPHP\R;

/**
 * Helpers de persistencia de permissoes para regras de ACL baseadas em rotas.
 */
class Permission extends R {

    /** @return array|null Beans de permissao indexados pelo RedBeanPHP. */
    public static function all(): ?array {
        return R::find('permission');
    }

    /**
     * @param int|null $id ID da permissao.
     * @return \RedBeanPHP\OODBBean|null Bean da permissao ou null.
     */
    public static function getOne(int $id = null): ?\RedBeanPHP\OODBBean {
        return R::findOne('permission', 'id = ?', [$id]);
    }

}
