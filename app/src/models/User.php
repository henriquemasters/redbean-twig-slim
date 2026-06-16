<?php

namespace App\Model;

use RedBeanPHP\R;

/**
 * Helpers de persistencia de usuarios baseados em beans dinamicos do RedBeanPHP.
 */
class User extends R {

    /**
     * Busca um usuario por login e valida a senha no servidor.
     *
     * Mantem compatibilidade temporaria com senhas MD5 antigas. Quando uma senha
     * legada autentica com sucesso, ela e migrada automaticamente para
     * password_hash().
     *
     * @param string $email Login/e-mail informado no formulario de autenticacao.
     * @param string $pass Senha em texto enviada pelo formulario.
     * @return \RedBeanPHP\OODBBean|null Bean do usuario encontrado ou null.
     */
    public static function getOne(string $email, string $pass): ?\RedBeanPHP\OODBBean {
        $user = self::getByLogin($email);

        if (!$user || !self::passwordMatches($pass, (string) $user->pass)) {
            return null;
        }

        if (self::passwordNeedsRehash((string) $user->pass)) {
            self::updatePassword($user->id, $pass);
        }

        return $user;
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

        if (!empty($data['pass']) && self::passwordNeedsRehash((string) $data['pass'])) {
            $data['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);
        }

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

    /**
     * Atualiza somente a senha do usuario usando hash seguro.
     */
    public static function updatePassword(int $uId, string $plainPassword): ?int {
        $u = R::loadForUpdate('user', $uId);
        $u->pass = password_hash($plainPassword, PASSWORD_DEFAULT);
        return R::store($u);
    }

    /**
     * Verifica senha moderna ou senha legada em MD5.
     */
    private static function passwordMatches(string $plainPassword, string $storedPassword): bool {
        if (password_verify($plainPassword, $storedPassword)) {
            return true;
        }

        return strlen($storedPassword) === 32 && hash_equals($storedPassword, md5($plainPassword));
    }

    /**
     * Detecta hashes ausentes, legados ou gerados com parametros antigos.
     */
    private static function passwordNeedsRehash(string $storedPassword): bool {
        if (strlen($storedPassword) === 32 && ctype_xdigit($storedPassword)) {
            return true;
        }

        return password_get_info($storedPassword)['algo'] === 0 || password_needs_rehash($storedPassword, PASSWORD_DEFAULT);
    }

}
