<?php

use \RedBeanPHP\R as R;

/**
 * Carrega variaveis simples de um arquivo .env sem exigir dependencias extras.
 *
 * O projeto e propositalmente enxuto; por isso este loader cobre apenas o
 * formato CHAVE=valor usado pelas configuracoes locais do banco.
 */
if (!function_exists('loadEnvFile')) {
    function loadEnvFile(string $path): void {
        if (!is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            if ($key !== '' && getenv($key) === false) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

/**
 * Busca uma variavel de ambiente com fallback local seguro para desenvolvimento.
 */
if (!function_exists('envValue')) {
    function envValue(string $key, string $default = ''): string {
        $value = getenv($key);

        return ($value === false) ? $default : $value;
    }
}

loadEnvFile(__DIR__ . '/../.env');

if (!R::testConnection()) {
    $driver = envValue('DB_DRIVER', 'mysql');
    $host = envValue('DB_HOST', 'localhost');
    $database = envValue('DB_NAME', 'myapp');
    $charset = envValue('DB_CHARSET', 'utf8mb4');
    $user = envValue('DB_USER', 'root');
    $password = envValue('DB_PASS', '');

    R::setup("{$driver}:host={$host};dbname={$database};charset={$charset}", $user, $password);
}
