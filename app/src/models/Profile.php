<?php

namespace App\Model;

use RedBeanPHP\R;

/**
 * Helpers de persistencia de perfil para dados vinculados ao usuario autenticado.
 */
class Profile extends R {

    /**
     * Busca o perfil vinculado a um ID de usuario.
     *
     * @param int $uId ID do usuario.
     * @return \RedBeanPHP\OODBBean|null Bean do perfil ou null.
     */
    public static function getOne(int $uId): ?\RedBeanPHP\OODBBean {
        return R::findOne('profile', 'user_id = ?', [$uId]);
    }

    /**
     * Persiste dados do formulario de perfil.
     *
     * @param array $data Dados do formulario de perfil.
     * @return int|null ID do bean armazenado.
     */
    public static function save(array $data): ?int {
        return R::store(R::dispense($data));
    }

}
