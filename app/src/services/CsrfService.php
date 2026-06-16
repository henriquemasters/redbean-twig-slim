<?php

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface as Request;

class CsrfService {

    private const SESSION_KEY = '_csrf_token';
    private const FIELD_NAME = '_csrf';

    /**
     * Retorna o token atual ou cria um novo token para a sessao.
     */
    public function token(): string {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Gera o campo hidden usado pelos formularios Twig.
     */
    public function field(): string {
        return '<input type="hidden" name="' . self::FIELD_NAME . '" value="' . htmlspecialchars($this->token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida o token enviado em metodos que alteram estado.
     */
    public function validate(Request $request): bool {
        $data = $request->getParsedBody();
        $token = is_array($data) ? ($data[self::FIELD_NAME] ?? null) : null;

        return is_string($token) && hash_equals($this->token(), $token);
    }

}
