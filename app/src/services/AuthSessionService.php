<?php

namespace App\Service;

use App\Model\Permission;
use App\Model\Role;
use App\Model\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use RedBeanPHP\OODBBean;

class AuthSessionService {

    /**
     * Monta a sessao autenticada e a configuracao inicial de ACL.
     */
    public function login(OODBBean $user, Request $request): void {
        User::updateLastLogin($user->id);

        $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getBasePath();
        $photoUrl = $user->photo ? $baseUrl . $user->photo : null;

        $_SESSION['user_auth'] = [
            'id' => $user->id,
            'name' => $user->name,
            'login' => $user->login,
            'photo' => $photoUrl,
            'role' => ['id' => $user->role_id, 'name' => $user->role->name]
        ];

        $_SESSION['config'] = [
            'resources' => [],
            'roles' => [],
            'assignments' => [],
        ];

        $this->refreshAcl();
    }

    /**
     * Remove dados de autenticacao e ACL da sessao.
     */
    public function logout(): void {
        unset($_SESSION['user_auth'], $_SESSION['config']);
    }

    /**
     * Reconstroi ACL em sessao para refletir alteracoes feitas no painel.
     */
    public function refreshAcl(): void {
        if (!isset($_SESSION['user_auth'])) {
            return;
        }

        $_SESSION['user_auth']['role']['name'] = Role::getCell('SELECT name FROM role WHERE id = ?', [$_SESSION['user_auth']['role']['id']]);
        $_SESSION['config']['resources'] = Permission::getCol('SELECT DISTINCT pattern FROM permission');
        $_SESSION['config']['roles'] = Role::getCol('SELECT name FROM role');
        $_SESSION['config']['assignments'] = [
            'allow' => [],
            'deny' => [],
        ];

        foreach (Permission::all() as $permission) {
            $_SESSION['config']['assignments'][$permission->status][$permission->role->name][] = $permission->pattern;
        }
    }

}
