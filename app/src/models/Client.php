<?php

namespace App\Model;

use RedBeanPHP\R;

/**
 * CRUD de clientes para demonstrar um segundo modulo administrativo simples.
 */
class Client extends R {

    /** @return array|null Clientes ordenados pelos registros mais recentes. */
    public static function all(): ?array {
        try {
            return R::find('client', ' ORDER BY id DESC ');
        } catch (\Throwable $exception) {
            return [];
        }
    }

    /**
     * @param int|null $id ID do cliente.
     * @return \RedBeanPHP\OODBBean|null Bean do cliente ou null.
     */
    public static function getById(int $id = null): ?\RedBeanPHP\OODBBean {
        try {
            return R::findOne('client', 'id = ?', [$id]);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * Persiste os dados do cliente enviados pelo formulario administrativo.
     *
     * @param array $data Dados do formulario de cliente.
     * @return int|null ID do bean armazenado.
     */
    public static function save(array $data): ?int {
        unset($data['_csrf'], $data['_METHOD']);

        if (empty($data['createdat'])) {
            $data['createdat'] = date('Y-m-d H:i:s');
        }

        return R::store(R::dispense($data));
    }

}
