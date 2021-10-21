<?php

use \RedBeanPHP\R as R;

/**
 * Testa conexão com o banco de dados
 */
if (!R::testConnection()) {
    R::setup('mysql:host=localhost; dbname=myapp', 'root', '123');
}
