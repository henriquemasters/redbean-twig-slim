<?php

namespace App\Model;

use RedBeanPHP\R;

/**
 * Helpers de persistencia de usuarios baseados em beans dinamicos do RedBeanPHP.
 */
class User extends R {

    /**
     * Busca um usuario por login e senha.
     *
     * @param string $email Login/e-mail informado no formulario de autenticacao.
     * @param string $pass Valor de senha conforme armazenado pelo schema legado.
     * @return \RedBeanPHP\OODBBean|null Bean do usuario encontrado ou null.
     */
    public static function getOne(string $email, string $pass): ?\RedBeanPHP\OODBBean {
        return R::findOne('user', 'login = ? and pass = ?', [$email, $pass]);
    }

    /** @return array|null Todos os beans de usuario indexados pelo RedBeanPHP. */
    public static function all(): ?array {
        return R::find('user');
    }

    /**
     * @param int|null $id ID do usuario.
     * @return \RedBeanPHP\OODBBean|null Bean do usuario ou null.
     */
    public static function getById(int $id = null): ?\RedBeanPHP\OODBBean {
        return R::findOne('user', 'id = ?', [$id]);
    }

    /**
     * @param string $login Login/e-mail do usuario.
     * @return \RedBeanPHP\OODBBean|null Bean do usuario ou null.
     */
    public static function getByLogin(string $login): ?\RedBeanPHP\OODBBean {
        return R::findOne('user', 'login = ?', [$login]);
    }

    /**
     * Persiste um bean de usuario a partir dos dados do formulario.
     *
     * O RedBeanPHP mapeia chaves do array para propriedades do bean; portanto,
     * controllers devem enviar apenas campos esperados pela tabela user ou pelas
     * relacoes previstas no formulario.
     *
     * @param array $data Dados do formulario de usuario.
     * @return int|null ID do bean armazenado.
     */
    public static function save(array $data): ?int {
        unset($data['confirmpassword']);
        return R::store(R::dispense($data));
    }

    /**
     * @param int $uId ID do usuario.
     * @return int|null ID do bean armazenado.
     */
    public static function updateLastLogin(int $uId): ?int {
        $u = R::loadForUpdate('user', $uId);
        $u->lastlogin = date('Y-m-d H:i:s');
        return R::store($u);
    }

    /**
     * @param int $uId ID do usuario.
     * @param string $newName Novo nome de exibicao.
     * @return int|null ID do bean armazenado.
     */
    public static function updateName(int $uId, string $newName): ?int {
        $u = R::loadForUpdate('user', $uId);
        $u->name = $newName;
        return R::store($u);
    }

    /**
     * @param int $uId ID do usuario.
     * @return string|null Caminho web da foto armazenada.
     */
    public static function getPhoto(int $uId): ?string {
        return R::getCell('SELECT photo FROM user WHERE id = ?', [$uId]);
    }

    /**
     * @param int $uId ID do usuario.
     * @param string $newName Novo caminho web da foto.
     * @return int|null ID do bean armazenado.
     */
    public static function updatePhoto(int $uId, string $newName): ?int {
        $p = R::loadForUpdate('user', $uId);
        $p->photo = $newName;
        return R::store($p);
    }

}
