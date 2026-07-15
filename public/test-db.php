<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Config\Database;

try {

    Database::connect();

    echo "Conectado com sucesso!";

} catch (Exception $e) {

    echo $e->getMessage();

}