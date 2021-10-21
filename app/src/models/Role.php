<?php

namespace App\Model;

use RedBeanPHP\R;

class Role extends R {

    /**
     * 
     * @return array|null
     */
    public static function all(): ?array {
        return R::find('role');
    }

    /**
     * 
     * @param int $id
     * @return \RedBeanPHP\OODBBean|null
     */
    public static function getOne(int $id = null): ?\RedBeanPHP\OODBBean {
        return R::findOne('role', 'id = ?', [$id]);
    }

    /**
     * 
     * @param array $data
     * @return int|null
     */
    public static function save(array $data): ?int {
        return R::store(R::dispense($data));
    }

}
