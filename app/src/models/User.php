<?php

namespace App\Model;

use RedBeanPHP\R;

class User extends R {

    /**
     *
     * @param string $email
     * @param string $pass
     * @return \RedBeanPHP\OODBBean|null
     */
    public static function getOne(string $email, string $pass): ?\RedBeanPHP\OODBBean {
        return R::findOne('user', 'login = ? and pass = ?', [$email, $pass]);
    }

    /**
     * 
     * @return array|null
     */
    public static function all(): ?array {
        return R::find('user');
    }

    /**
     * 
     * @param int $id
     * @return \RedBeanPHP\OODBBean|null
     */
    public static function getById(int $id = null): ?\RedBeanPHP\OODBBean {
        return R::findOne('user', 'id = ?', [$id]);
    }

    /**
     * 
     * @param string $login
     * @return \RedBeanPHP\OODBBean|null
     */
    public static function getByLogin(string $login): ?\RedBeanPHP\OODBBean {
        return R::findOne('user', 'login = ?', [$login]);
    }

    /**
     * 
     * @param array $data
     * @return int|null
     */
    public static function save(array $data): ?int {
        unset($data['confirmpassword']);
        return R::store(R::dispense($data));
    }

    /**
     * 
     * @param int $uId
     * @return int|null
     */
    public static function updateLastLogin(int $uId): ?int {
        $u = R::loadForUpdate('user', $uId);
        $u->lastlogin = date('Y-m-d H:i:s');
        return R::store($u);
    }

    /**
     * 
     * @param int $uId
     * @param string $newName
     * @return int|null
     */
    public static function updateName(int $uId, string $newName): ?int {
        $u = R::loadForUpdate('user', $uId);
        $u->name = $newName;
        return R::store($u);
    }

    /**
     * 
     * @param int $uId
     * @return string|null
     */
    public static function getPhoto(int $uId): ?string {
        return R::getCell('SELECT photo FROM user WHERE id = ?', [$uId]);
    }

    /**
     * 
     * @param int $uId
     * @param string $newName
     * @return int|null
     */
    public static function updatePhoto(int $uId, string $newName): ?int {
        $p = R::loadForUpdate('user', $uId);
        $p->photo = $newName;
        return R::store($p);
    }

}
