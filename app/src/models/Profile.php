<?php

namespace App\Model;

use RedBeanPHP\R;

class Profile extends R {

    /**
     *
     * @param string $email
     * @param string $pass
     * @return \RedBeanPHP\OODBBean|null
     */
    public static function getOne(int $uId): ?\RedBeanPHP\OODBBean {
        return R::findOne('profile', 'user_id = ?', [$uId]);
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
