<?php

namespace App\Model;

use RedBeanPHP\R;

class Permission extends R {

    /**
     * 
     * @return array|null
     */
    public static function all(): ?array {
        return R::find('permission');
    }

    /**
     * 
     * @param int $id
     * @return \RedBeanPHP\OODBBean|null
     */
    public static function getOne(int $id = null): ?\RedBeanPHP\OODBBean {
        return R::findOne('permission', 'id = ?', [$id]);
    }

}
